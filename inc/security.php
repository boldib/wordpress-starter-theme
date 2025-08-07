<?php
/**
 * Security enhancements for WordPress.
 *
 * @package Theme
 */

abspath_check();

/**
 * Disable XML-RPC functionality.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Remove XML-RPC pingback header.
 *
 * @param array $headers HTTP headers.
 * @return array Modified HTTP headers.
 */
function theme_remove_x_pingback( $headers ) {
	unset( $headers['X-Pingback'] );
	return $headers;
}
add_filter( 'wp_headers', 'theme_remove_x_pingback' );

/**
 * Disable user enumeration.
 *
 * @param object $query The WP_Query instance.
 * @return object Modified query.
 */
function theme_disable_user_enumeration( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return $query;
	}

	if ( isset( $_GET['author'] ) && ! current_user_can( 'edit_posts' ) ) {
		wp_redirect( home_url(), 301 );
		exit;
	}

	return $query;
}
add_action( 'pre_get_posts', 'theme_disable_user_enumeration' );

/**
 * Disable file editing in the WordPress admin.
 */
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}

/**
 * Add security headers.
 */
function theme_add_security_headers() {
	// X-Content-Type-Options.
	header( 'X-Content-Type-Options: nosniff' );

	// X-Frame-Options.
	header( 'X-Frame-Options: SAMEORIGIN' );

	// X-XSS-Protection.
	header( 'X-XSS-Protection: 1; mode=block' );

	// Referrer-Policy.
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );

	// Content-Security-Policy - Customize as needed.
	// header( 'Content-Security-Policy: default-src \'self\'' );
}
add_action( 'send_headers', 'theme_add_security_headers' );

/**
 * Block bad queries.
 */
function theme_block_bad_queries() {
	global $user_ID;

	if ( ! $user_ID ) {
		if ( preg_match( '/\.\.\//i', $_SERVER['REQUEST_URI'] ) ) {
			wp_die( 'Invalid request.', 'Security Error', array( 'response' => 403 ) );
		}

		$request_methods = array( 'REQUEST_URI', 'QUERY_STRING' );
		$bad_patterns = array(
			'eval\(', 'UNION.*SELECT', 'CONCAT.*\(', 'base64_', '\/etc\/passwd',
			'\/etc\/shadow', '\/proc\/self\/environ', '\/tmp\/', '\/var\/'
		);

		foreach ( $request_methods as $method ) {
			if ( ! isset( $_SERVER[ $method ] ) ) {
				continue;
			}

			foreach ( $bad_patterns as $pattern ) {
				if ( preg_match( '/' . $pattern . '/i', $_SERVER[ $method ] ) ) {
					wp_die( 'Invalid request.', 'Security Error', array( 'response' => 403 ) );
				}
			}
		}
	}
}
add_action( 'init', 'theme_block_bad_queries' );

/**
 * Disable login error messages.
 *
 * @return string Generic error message.
 */
function theme_disable_login_errors() {
	return 'Invalid login credentials.';
}
add_filter( 'login_errors', 'theme_disable_login_errors' );

/**
 * Disable the application password feature.
 */
add_filter( 'wp_is_application_passwords_available', '__return_false' );

/**
 * Limit login attempts.
 */
function theme_limit_login_attempts() {
	// Check if we're on the login page.
	if ( ! isset( $_POST['log'] ) || $GLOBALS['pagenow'] !== 'wp-login.php' ) {
		return;
	}

	$ip_address = $_SERVER['REMOTE_ADDR'];
	$transient_name = 'login_attempts_' . md5( $ip_address );

	// Get login attempts.
	$login_attempts = get_transient( $transient_name );

	if ( false === $login_attempts ) {
		$login_attempts = array(
			'count' => 1,
			'time' => time(),
		);
		set_transient( $transient_name, $login_attempts, HOUR_IN_SECONDS );
	} else {
		// Increment login attempts.
		$login_attempts['count']++;
		set_transient( $transient_name, $login_attempts, HOUR_IN_SECONDS );

		// If more than 10 attempts in an hour, block the login.
		if ( $login_attempts['count'] > 10 ) {
			wp_die(
				'Too many failed login attempts. Please try again in an hour.',
				'Login Blocked',
				array( 'response' => 403 )
			);
		}
	}
}
add_action( 'authenticate', 'theme_limit_login_attempts', 30, 3 );
