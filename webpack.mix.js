const mix = require('laravel-mix');
const glob = require('glob');

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
    // Global theme assets
    .sass('assets/scss/main.scss', 'assets/css/main.css')
    .options({
        processCssUrls: false, // Don't process URLs in CSS (WordPress handles paths differently)
    })
    .setPublicPath('./') // Set public path to theme root so mix-manifest.json is written here
    .sourceMaps(false, 'source-map'); // Enable source maps in dev for debugging

/*
 |--------------------------------------------------------------------------
 | Elementor Widget SCSS Compilation
 |--------------------------------------------------------------------------
 |
 | Automatically finds all style.scss files inside elementor/widgets/
 | and compiles each one to style.css in the same widget directory.
 |
 */

glob.sync('elementor/widgets/*/style.scss').forEach((scssFile) => {
    // scssFile = "elementor/widgets/sample-widget/style.scss"
    // Output CSS goes to the same directory: "elementor/widgets/sample-widget"
    const outputDir = scssFile.replace('/style.scss', '');
    mix.sass(scssFile, outputDir + '/style.css');
});
