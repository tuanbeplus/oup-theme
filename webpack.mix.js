const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining Webpack build steps
 | for your WordPress theme. By default, we compile JS and SCSS.
 |
 */

mix
    .js('assets/js/main.js', 'assets/js/main.min.js')
    .sass('assets/scss/main.scss', 'assets/css/main.css')
    .options({
        processCssUrls: false, // Don't process URLs in CSS (WordPress handles paths differently)
    })
    .setPublicPath('./') // Set public path to theme root so mix-manifest.json is written here
    .sourceMaps(false, 'source-map'); // Enable source maps in dev for debugging
