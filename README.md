# Onwards & Upwards Psychology WordPress Theme

This is a custom WordPress child theme created for **Onwards & Upwards Psychology**, built on top of the [Hello Elementor] theme.

## Features

- **Elementor Integrated**: Designed to work seamlessly with the Elementor page builder.
- **Custom Assets**: Enqueues custom CSS (`assets/css/main.css`) and JavaScript (`assets/js/main.js`).
- **WooCommerce Ready**: Includes custom functions, such as forcing the WooCommerce currency symbol to display as `AUD`.
- **AJAX Support**: Pre-localizes `ajaxurl` for custom JavaScript interactions.

## Installation

1. Ensure the parent theme **Hello Elementor** is installed in your WordPress environment.
2. Upload the `oup-theme` folder to your `/wp-content/themes/` directory.
3. Navigate to **Appearance > Themes** in your WordPress admin dashboard.
4. Locate the **Onwards & Upwards Psychology WordPress Theme** and click **Activate**.

## Project Structure

```text
oup-theme/
├── assets/
│   ├── css/
│   │   └── main.css      # Custom styles
│   └── js/
│       └── main.js       # Custom scripts
├── functions.php         # Theme functions and definitions
├── README.md             # This file
├── screenshot.png        # Theme thumbnail in the WordPress dashboard
└── style.css             # Theme metadata and stylesheet
```

## Development

- The theme version (`OUP_THEME_VER`) currently appends a timestamp to the version for cache-busting during development. For production, you may want to remove `time()` in `functions.php`.
- Custom logic can be added in `functions.php`.

## Author

**Beplus**
