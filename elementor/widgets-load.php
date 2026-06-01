<?php
namespace OupElementorWidgets;

/**
 * Class ElementorWidgets
 *
 * Main ElementorWidgets class
 * @since 1.0.0
 */
class ElementorWidgets {

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @var ElementorWidgets The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return ElementorWidgets An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public $widgets = array();

	public function widgets_list() {

		$this->widgets = array(
			'sample-widget',
			'archive-posts-filter',
			'worksheet-filter',
			'blog-search',
		);

		return $this->widgets;
	}

	/**
	 * widget_styles
	 *
	 * Load required core files.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function widget_styles() {
		wp_register_style( 
			'oup-sample-widget-style',
			get_stylesheet_directory_uri() . '/elementor/widgets/sample-widget/style.css',
			array(),
			OUP_THEME_VER,
			'all'
		);

		wp_register_style(
            'oup-archive-posts-filter-style',
            get_stylesheet_directory_uri() . '/elementor/widgets/archive-posts-filter/style.css',
            [],
            OUP_THEME_VER,
            'all'
        );
		wp_register_style( 
			'oup-worksheet-filter-style',
			get_stylesheet_directory_uri() . '/elementor/widgets/worksheet-filter/style.css',
			array(),
			OUP_THEME_VER,
			'all'
		);
		wp_register_style( 
			'oup-blog-search-style',
			get_stylesheet_directory_uri() . '/elementor/widgets/blog-search/style.css',
			array(),
			OUP_THEME_VER,
			'all'
		);
	}

	/**
	 * widget_scripts
	 *
	 * Load required core files.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function widget_scripts() {
		wp_register_script( 
			'oup-sample-widget-script',
			get_stylesheet_directory_uri() . '/elementor/widgets/sample-widget/script.js',
			array(),
			OUP_THEME_VER,
			true
		);
		
		wp_register_script(
            'oup-archive-posts-filter-script',
            get_stylesheet_directory_uri() . '/elementor/widgets/archive-posts-filter/script.js',
            [ 'jquery' ],
            OUP_THEME_VER,
            true
        );
		wp_register_script( 
			'oup-worksheet-filter-script',
			get_stylesheet_directory_uri() . '/elementor/widgets/worksheet-filter/script.js',
			array('jquery'),
			OUP_THEME_VER,
			true
		);
	}

	/**
	 * Include Widgets files
	 *
	 * Load widgets files
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function include_widgets_files() {

		foreach( $this->widgets_list() as $widget ) {
			require_once( get_stylesheet_directory() . '/elementor/widgets/'. $widget .'/widget.php' );

			foreach( glob( get_stylesheet_directory() . '/elementor/widgets/'. $widget .'/skins/*.php') as $filepath ) {
				include $filepath;
			}
		}

	}

	/**
	 * Register categories
	 *
	 * Register new Elementor category.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_categories( $elements_manager ) {

		$elements_manager->add_category(
			'oup',
			[
				'title' => esc_html__( 'Onwards & Upwards Psychology', 'oup' )
			]
		);

	}

	/**
	 * Register Widgets	
	 *
	 * Register new Elementor widgets.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_widgets() {
		// Its is now safe to include Widgets files
		$this->include_widgets_files();

		// Register Widgets
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SampleWidget\Widget_SampleWidget());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ArchivePostsFilter\Widget_ArchivePostsFilter());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\WorksheetFilter\Widget_WorksheetFilter());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\BlogSearch\Widget_BlogSearch());
	}

	/**
	 *  ElementorWidgets class constructor
	 *
	 * Register action hooks and filters
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		// Register widget styles
		add_action( 'elementor/frontend/after_register_styles', [ $this, 'widget_styles' ] );

		// Register widget scripts
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'widget_scripts' ] );

		// Register categories
		add_action( 'elementor/elements/categories_registered', [ $this, 'register_categories' ] );

		// Register widgets
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );
	}

}

// Instantiate ElementorWidgets Class
ElementorWidgets::instance();