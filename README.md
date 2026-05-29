# Onwards & Upwards Psychology WordPress Theme

This is a custom WordPress child theme created for **Onwards & Upwards Psychology**, built on top of the [Hello Elementor] theme.

---

## Features

- **Elementor Integrated**: Designed to work seamlessly with the Elementor page builder. Custom Elementor widgets are supported and automatically registered.
- **Modern Asset Compilation**: Uses Laravel Mix (Webpack) to compile, bundle, and minify Sass (SCSS). JavaScript is loaded directly without minification to maintain the IIFE structure.
- **WooCommerce Ready**: Contains custom templates and overrides (e.g., product tag display and custom loops) located in `inc/woo.php`.
- **AJAX Support**: Pre-localizes `ajaxurl` for AJAX handlers in JavaScript.

---

## Project Structure

```text
oup-theme/
├── assets/
│   ├── css/
│   │   ├── main.css            # Compiled CSS (Enqueued in WP)
│   │   └── main.css.map        # Source map for CSS debugging
│   ├── js/
│   │   ├── main.js             # Main JavaScript entry point (Enqueued in WP)
│   │   └── widgets/            # Custom widget JS files
│   ├── scss/
│   │   ├── main.scss           # Main SCSS entry point
│   │   ├── _forms.scss         # Form styles
│   │   ├── _products.scss      # WooCommerce product styles
│   │   └── widgets/            # Custom widget SCSS files
│   └── imgs/                   # Theme images
├── elementor/
│   ├── widgets/                # Custom Elementor widgets folder
│   │   └── sample-widget/
│   │       └── widget.php      # Sample Elementor Widget definition
│   └── widgets-load.php        # Register and load Elementor widgets
├── inc/
│   └── woo.php                 # WooCommerce hooks and overrides
├── functions.php               # Enqueues assets & bootstrap files
├── webpack.mix.js              # Laravel Mix build configuration
├── package.json                # NPM dependency & script definitions
├── style.css                   # WordPress theme metadata
└── README.md                   # This file
```

---

## Local Git Branch Setup

When starting on a new task or widget, always work in a dedicated feature branch rather than directly committing to the `main` or default branch.

1. **Check current branch status:**
   ```bash
   git status
   ```

2. **Pull the latest remote updates:**
   ```bash
   git checkout main
   git pull origin main
   ```

3. **Create and switch to a new local branch:**
   Choose a clear branch name (e.g., `feature/custom-header` or `feature/testimonial-widget`):
   ```bash
   git checkout -b feature/your-feature-name
   ```

4. **Publish your local branch to the remote repository:**
   The first time you push your branch, run:
   ```bash
   git push -u origin feature/your-feature-name
   ```

---

## Asset Compilation Setup (NPM)

The theme uses Laravel Mix to compile Sass files. JS widgets are loaded as required.

### 1. Initial Setup
Make sure you have Node.js installed locally. In your theme's root directory, run:
```bash
npm install
```
*(Webpack and Sass-Loader versions are automatically overridden inside `package.json` to guarantee compatibility with `laravel-mix` v6).*

### 2. Compile CSS (SCSS)

- **Development / Watch Mode (Recommended during styling):**
  Compiles assets with sourcemaps enabled and watches for any file modifications in real-time to re-compile automatically.
  ```bash
  npm run dev
  ```
  *(or `npx mix watch`)*

- **Production Build:**
  Minifies, optimizes, and strips comments from assets to achieve optimal loading performance before deployment.
  ```bash
  npm run build
  ```

---

## Guide: Adding a New Custom Elementor Widget

Follow these steps to build and register a new custom Elementor widget (e.g., a "Testimonial Carousel"):

### Step 1: Create the Widget PHP File
1. Under `elementor/widgets/`, create a new folder named after your widget, e.g., `testimonial-carousel/`.
2. Inside that folder, create `widget.php`.
3. Set up the PHP class namespace and structure:
   ```php
   <?php
   namespace OupElementorWidgets\Widgets\TestimonialCarousel;

   use Elementor\Widget_Base;
   use Elementor\Controls_Manager;

   class Widget_TestimonialCarousel extends Widget_Base {
       public function get_name() {
           return 'testimonial-carousel';
       }
       public function get_title() {
           return __( 'Testimonial Carousel', 'oup' );
       }
       public function get_icon() {
           return 'eicon-slides';
       }
       public function get_categories() {
           return [ 'oup' ];
       }
       
       protected function register_controls() {
           // Define your widget content and style settings here
       }

       protected function render() {
           $settings = $this->get_settings_for_display();
           ?>
           <div class="testimonial-carousel-widget">
               <!-- Widget frontend HTML output goes here -->
           </div>
           <?php
       }
   }
   ```

### Step 2: Register the Widget in the Theme
1. Open [elementor/widgets-load.php](file:///Users/macbookpro/Documents/Work_Project/Onwards_Upwards_Psychology_dev/oup-theme/elementor/widgets-load.php).
2. Add your widget name to the `widgets_list()` array:
   ```php
   public function widgets_list() {
       $this->widgets = array(
           'sample-widget',
           'testimonial-carousel', // Add new entry here
       );
       return $this->widgets;
   }
   ```
3. Register the new widget instance in the `register_widgets()` function:
   ```php
   public function register_widgets() {
       $this->include_widgets_files();

       // Register Widgets
       \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SampleWidget\Widget_SampleWidget());
       \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\TestimonialCarousel\Widget_TestimonialCarousel()); // Register new instance here
   }
   ```

### Step 3: Add Custom Styles (SCSS)
1. Create a Sass partial inside `assets/scss/widgets/` called `_testimonial-carousel.scss`.
2. Write styling rules scoped to the widget selector:
   ```scss
   .testimonial-carousel-widget {
       background-color: #f9f9f9;
       border-radius: 8px;
   }
   ```
3. Import the widget styles into the main Sass manifest in [assets/scss/main.scss](file:///Users/macbookpro/Documents/Work_Project/Onwards_Upwards_Psychology_dev/oup-theme/assets/scss/main.scss):
   ```scss
   @import "./widgets/testimonial-carousel";
   ```

### Step 4: Add Custom Interactions (JS)
1. Create a script inside `assets/js/widgets/` called `testimonial-carousel.js`.
2. Add interactive frontend script (such as initializing a carousel):
   ```javascript
   (function ($) {
       'use strict';
       $(function () {
           // Code for initializing the testimonial carousel goes here
       });
   })(jQuery);
   ```
2. Ensure your interactive script is loaded or enqueued in `elementor/widgets-load.php`. Notice how `wp_register_script` handles the widget-specific JS.

---

## Development Notes & Caching

- **Asset Cache-Busting**: The child theme versioning constant (`OUP_THEME_VER`) defined in [functions.php](file:///Users/macbookpro/Documents/Work_Project/Onwards_Upwards_Psychology_dev/oup-theme/functions.php) appends a timestamp (`time()`) to styles and scripts. This ensures updates are immediately reflected in your browser during local development.
- **Production Build Prep**: When deploying the theme to production, make sure to compile assets using `npm run build` and consider modifying `OUP_THEME_VER` in [functions.php](file:///Users/macbookpro/Documents/Work_Project/Onwards_Upwards_Psychology_dev/oup-theme/functions.php) to a static version string (e.g. `'1.0.0'`) so production users benefit from browser caching.

---

## Author

**Beplus**
