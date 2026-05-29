<?php 
namespace OupElementorWidgets\Widgets\SampleWidget;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Widget_SampleWidget extends Widget_Base {
    public function get_name() {
        return 'sample-widget';
    }
    public function get_title() {
        return __( 'Sample Widget', 'oup' );
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_categories() {
        return [ 'oup' ];
    }

    public function get_script_depends() {
        return [ 'oup-sample-widget-script' ];
    }

    public function get_style_depends() {
        return [ 'oup-sample-widget-style' ];
    }


    protected function register_content_section_controls() {

        $this->end_controls_section();

    }

    protected function register_controls() {
        $this->register_content_section_controls();
    }

    protected function render() {
        ?>

        <div class="sample-widget">
            <h2>This is Sample Widget</h2>
        </div>
        <?php
    }

    protected function content_template() {
        // Template for Elementor editor preview
    }
}
?>
