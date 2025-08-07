const mix = require('laravel-mix');
const fs = require('fs');
const path = require('path');

// Set the public path to the build directory
mix.setPublicPath('./build');

// Process CSS files
mix.postCss('src/css/app.css', 'css/app.css', [
    require('postcss-import'),
    require('tailwindcss')('./tailwind.config.js'),
    require('autoprefixer'),
]);

// Process JavaScript files
mix.js('src/js/app.js', 'js');

// Disable success notifications
mix.disableSuccessNotifications();

// Disable mix manifest file generation to prevent infinite loops
mix.options({
    manifest: false
});

// Source maps only in development
mix.sourceMaps(!mix.inProduction());

// Configure webpack with watch options to prevent infinite compilation loops
mix.webpackConfig({
    watchOptions: {
        ignored: [
            path.resolve(__dirname, 'build/**'),
            path.resolve(__dirname, 'node_modules/**'),
            path.resolve(__dirname, '.git/**'),
            path.resolve(__dirname, 'src/blocks/**'),
            path.resolve(__dirname, 'build/blocks/**')
        ]
    },
    stats: {
        children: false
    }
});

// Generate versioning.php file when running in production mode
if (mix.inProduction()) {
  mix.then(() => {
    // Generate a random string of 32 characters
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let randomString = '';
    
    for (let i = 0; i < 32; i++) {
      randomString += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    
    // Create the PHP file content
    const versionFileContent = `<?php
/**
 * Theme Version
 * 
 * Generated automatically by webpack.mix.js
 * Do not modify this file directly
 */

define('THEME_VERSION', '${randomString}');
`;
    
    // Write the file
    const versionFilePath = path.join(__dirname, 'versioning.php');
    fs.writeFileSync(versionFilePath, versionFileContent);
    
    console.log(`\n✅ Generated versioning.php with version: ${randomString}`);
  });
}
