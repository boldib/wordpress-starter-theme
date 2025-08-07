# WordPress Starter Theme

A modern WordPress starter theme with Tailwind CSS, PostCSS, Composer, Laravel Mix, and WordPress Scripts integration.

## Features

- **Tailwind CSS 3**: Utility-first CSS framework for rapid UI development
- **PostCSS**: Transform CSS with JavaScript plugins
- **Composer**: PHP dependency management with autoloading
- **Laravel Mix**: Webpack wrapper for simplified asset compilation
- **WordPress Scripts**: Official WordPress development toolkit
- **Block Editor Support**: Full support for Gutenberg blocks
- **Security Enhancements**: Various security measures implemented
- **Responsive Design**: Mobile-first approach with Tailwind CSS

## Requirements

- WordPress 6.7+
- PHP 8.2+
- Node.js 22+
- Composer

## Installation

1. Clone this repository to your WordPress themes directory:
   ```
   cd wp-content/themes/
   git clone https://github.com/boldib/starter-theme.git
   ```

2. Install PHP dependencies:
   ```
   cd starter-theme
   composer install
   ```

3. Install Node.js dependencies:
   ```
   npm install
   ```

## Available Commands

```bash
# Start development server with hot reloading (both assets and blocks)
npm start

# Start block development only
npm run start:blocks

# Watch for changes in assets only
npm run watch

# Build assets and blocks for development
npm run build

# Build assets and blocks for production
npm run build:production

# Build blocks only
npm run build:blocks

# Lint CSS
npm run lint:css

# Lint JavaScript
npm run lint:js

# Lint package.json
npm run lint:pkg-json

# Format JavaScript
npm run format:js
```

6. Activate the theme in the WordPress admin panel.

## Development

### Directory Structure

```
starter-theme/
├── build/                  # Compiled assets (generated)
├── inc/                    # PHP includes
│   ├── blocks.php          # Block registration
│   ├── enqueue.php         # Script/style enqueuing
│   ├── security.php        # Security enhancements
│   └── theme-setup.php     # Theme setup and features
├── parts/                  # Block template parts
│   ├── header.html         # Header template part
│   └── footer.html         # Footer template part
├── src/                    # Source files
├── template-parts/         # Template parts
│   ├── content.php         # Default content template
│   └── content-none.php    # No content found template
├── composer.json           # Composer configuration
├── functions.php           # Theme functions
├── index.php               # Main template file
├── package.json            # NPM configuration
├── style.css               # Theme metadata
├── tailwind.config.js      # Tailwind configuration
├── theme.json              # Theme configuration
├── webpack.mix.js          # Laravel Mix configuration
└── versioning.php          # Theme version file
```

## Customization

### Tailwind Configuration

Edit `tailwind.config.js` to customize your Tailwind setup.

### Adding PHP Functionality

Add new PHP files to the `inc/` directory and include them in `composer.json` autoload section.
