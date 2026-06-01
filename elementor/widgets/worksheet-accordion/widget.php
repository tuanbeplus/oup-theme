<?php 
namespace OupElementorWidgets\Widgets\WorksheetAccordion;

use Elementor\Widget_Base;

class Widget_WorksheetAccordion extends Widget_Base {
    public function get_name() {
        return 'worksheet-accordion';
    }
    public function get_title() {
        return __( 'Worksheet Accordion', 'oup' );
    }

    public function get_icon() {
        return 'eicon-accordion';
    }

    public function get_categories() {
        return [ 'oup' ];
    }

    public function get_script_depends() {
        return [ 'oup-worksheet-accordion-script' ];
    }

    public function get_style_depends() {
        return [ 'oup-worksheet-accordion-style' ];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content Data', 'oup' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'acf_notice',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => __( 'Widget này tự động lấy dữ liệu từ ACF Field có tên <b>worksheet_accordion</b>.<br><br>Vui lòng nhập dữ liệu trong phần chỉnh sửa Worksheet (bài viết) để thấy nội dung.', 'oup' ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {

        $accordion_items = get_field('worksheet_accordion');
        
        if ( ! $accordion_items || ! is_array( $accordion_items ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div class="oup-alert" style="padding: 20px; background: #fff3cd; color: #856404; border-radius: 8px; border: 1px dashed #ffeeba;">[Worksheet Accordion] Chưa có dữ liệu ACF. Vui lòng nhập dữ liệu vào ô "worksheet_accordion" trong bài viết này.</div>';
            }
            return;
        }
        
        ?>
        <div class="worksheet-accordion-wrapper">
            <?php foreach ( $accordion_items as $index => $item ) : 
                $title = isset($item['title']) ? $item['title'] : '';
                $content = isset($item['content']) ? $item['content'] : '';
                
                if ( empty( $title ) && empty( $content ) ) continue;
            ?>
                <div class="worksheet-accordion-item">
                    <button class="worksheet-accordion-header" aria-expanded="false">
                        <h3 class="worksheet-accordion-title"><?php echo esc_html( $title ); ?></h3>
                        <span class="worksheet-accordion-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 9L12 15L18 9" stroke="#1F2937" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </button>
                    <div class="worksheet-accordion-content" style="display: none;">
                        <div class="worksheet-accordion-content-inner">
                            <?php echo wp_kses_post( $content ); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
}
?>
