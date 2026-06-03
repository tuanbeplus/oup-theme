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
			'blog-detail-breadcrumb',
			'blog-detail-toc',
			'worksheet-accordion',
			'sugar-calendar-event',
			'course-filter',
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

		// Swiper
		wp_register_style(
			'swiper-bundle',
			'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
			array(),
			'11',
			'all'
		);

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
			array(),
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
			'oup-course-filter-style',
			get_stylesheet_directory_uri() . '/elementor/widgets/course-filter/style.css',
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

		wp_register_style(
			'oup-blog-detail-breadcrumb-style',
			get_stylesheet_directory_uri() . '/elementor/widgets/blog-detail-breadcrumb/style.css',
			array(),
			OUP_THEME_VER,
			'all'
		);

		wp_register_style(
			'oup-worksheet-accordion-style',
			get_stylesheet_directory_uri() . '/elementor/widgets/worksheet-accordion/style.css',
			array(),
			OUP_THEME_VER,
			'all'
		);

		wp_register_style(
			'oup-blog-detail-toc-style',
			get_stylesheet_directory_uri() . '/elementor/widgets/blog-detail-toc/style.css',
			array(),
			OUP_THEME_VER,
			'all'
		);

		wp_register_style(
			'oup-sugar-calendar-event-style',
			get_stylesheet_directory_uri() . '/elementor/widgets/sugar-calendar-event/style.css',
			array('swiper-bundle'),
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

		// Swiper
		wp_register_script(
			'swiper-bundle',
			'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
			array(),
			'11',
			true
		);

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
			array('jquery'),
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

		wp_register_script(
			'oup-course-filter-script',
			get_stylesheet_directory_uri() . '/elementor/widgets/course-filter/script.js',
			array('jquery', 'elementor-frontend'),
			filemtime(get_stylesheet_directory() . '/elementor/widgets/course-filter/script.js'),
			true
		);

		wp_register_script(
			'oup-worksheet-accordion-script',
			get_stylesheet_directory_uri() . '/elementor/widgets/worksheet-accordion/script.js',
			array('jquery'),
			OUP_THEME_VER,
			true
		);

		wp_register_script(
			'oup-blog-detail-toc-script',
			get_stylesheet_directory_uri() . '/elementor/widgets/blog-detail-toc/script.js',
			array('jquery'),
			OUP_THEME_VER,
			true
		);

		wp_register_script(
			'oup-sugar-calendar-event-script',
			get_stylesheet_directory_uri() . '/elementor/widgets/sugar-calendar-event/script.js',
			array('jquery', 'swiper-bundle'),
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
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\CourseFilter\Widget_CourseFilter());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\BlogSearch\Widget_BlogSearch());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\BlogDetailBreadcrumb\Widget_BlogDetailBreadcrumb());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\BlogDetailToc\Widget_BlogDetailToc());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\WorksheetAccordion\Widget_WorksheetAccordion());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SugarCalendarEvent\Widget_SugarCalendarEvent());
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