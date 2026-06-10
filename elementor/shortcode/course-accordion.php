<?php
/**
 * Template part for displaying the Course Accordion shortcode.
 *
 * @package Onwards-Upwards-Psychology-Theme
 */

defined( 'ABSPATH' ) || exit;

$lessons_list  = $args['lessons_list'] ?? [];
$default_state = $args['default_state'] ?? 'first_expanded';
$max_items     = $args['max_items'] ?? 'one';
$anim_duration = $args['anim_duration'] ?? 400;

if ( empty( $lessons_list ) ) {
    return;
}
?>
<div class="course-accordion-wrapper oup-course-accordion-container" data-max-items="<?php echo esc_attr($max_items); ?>" data-anim-duration="<?php echo esc_attr($anim_duration); ?>">
    <?php foreach ( $lessons_list as $index => $lesson ) :
        $lesson_post = $lesson['post'];
        $title       = $lesson_post->post_title;
        $topics      = isset( $lesson['topics'] ) ? $lesson['topics'] : [];
        $quizzes     = isset( $lesson['quizzes'] ) ? $lesson['quizzes'] : [];

        if ( empty( $title ) ) continue;

        $is_active = false;
        if ($default_state === 'all_expanded') {
            $is_active = true;
        } elseif ($default_state === 'first_expanded' && $index === 0) {
            $is_active = true;
        }
        
        $active_class = $is_active ? 'active' : '';
        $aria_expanded = $is_active ? 'true' : 'false';
        $display = $is_active ? 'block' : 'none';
    ?>
        <div class="course-accordion-item">
            <button class="course-accordion-header <?php echo esc_attr($active_class); ?>" aria-expanded="<?php echo esc_attr($aria_expanded); ?>">
                <h2 class="course-accordion-title"><?php echo esc_html( $title ); ?></h2>
                <span class="course-accordion-icon">
                    <span class="elementor-accordion-icon-closed">
                        <svg width="19" height="10" viewBox="0 0 19 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.5 1.5L8.8415 7.92381C9.21852 8.25371 9.78148 8.25371 10.1585 7.92381L17.5 1.5" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                    </span>
                </span>
            </button>
            <div class="course-accordion-content" style="display: <?php echo esc_attr($display); ?>;">
                <div class="course-accordion-content-inner">
                    <?php if ( empty( $topics ) && empty( $quizzes ) ) : ?>
                        <p class="no-steps-msg"><?php esc_html_e( 'No steps found for this lesson.', 'oup' ); ?></p>
                    <?php else : ?>
                        <ul class="course-accordion-steps">
                            <?php if ( ! empty( $topics ) ) : ?>
                                <?php foreach ( $topics as $topic ) :
                                    $topic_post = is_a( $topic, 'WP_Post' ) ? $topic : ( isset( $topic['post'] ) ? $topic['post'] : null );
                                    if ( ! $topic_post ) continue;
                                ?>
                                    <li class="course-accordion-step-item course-accordion-topic">
                                        <a href="<?php echo esc_url( get_permalink( $topic_post->ID ) ); ?>">
                                            <span class="step-icon">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                            </span>
                                            <span class="step-title"><?php echo esc_html( $topic_post->post_title ); ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php if ( ! empty( $quizzes ) ) : ?>
                                <?php foreach ( $quizzes as $quiz ) :
                                    $quiz_post = is_a( $quiz, 'WP_Post' ) ? $quiz : ( isset( $quiz['post'] ) ? $quiz['post'] : null );
                                    if ( ! $quiz_post ) continue;
                                ?>
                                    <li class="course-accordion-step-item course-accordion-quiz">
                                        <a href="<?php echo esc_url( get_permalink( $quiz_post->ID ) ); ?>">
                                            <span class="step-icon">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path><path d="m9 12 2 2 4-4"></path></svg>
                                            </span>
                                            <span class="step-title"><?php echo esc_html( $quiz_post->post_title ); ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
