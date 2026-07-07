<?php
$lesson_type = get_field( 'lesson_type', $lesson->get_id() );

$icon = '<i class="fa-regular fa-file-lines"></i>';

switch ( $lesson_type ) {
    case 'video':
        $icon = '<i class="fa-solid fa-play"></i>';
        break;

    case 'audio':
        $icon = '<i class="fa-solid fa-headphones"></i>';
        break;

    case 'article':
        $icon = '<i class="fa-regular fa-file-lines"></i>';
        break;

    case 'worksheet':
        $icon = '<i class="fa-regular fa-file-pdf"></i>';
        break;
}
?>

<div class="ld-accordion__item-title-wrapper ld-tooltip ld-tooltip--modern">
    <a
        <?php if ( ! $has_access && ! $lesson->is_sample() ) : ?>
            aria-describedby="ld-accordion__tooltip--lesson-<?php echo esc_attr( (string) $lesson->get_id() ); ?>"
        <?php endif; ?>
        class="ld-accordion__item-title ld-accordion__item-title--lesson"
        href="<?php echo esc_url( $lesson->get_permalink() ); ?>"
    >
        <?php echo $icon; ?>
        <?php echo wp_kses_post( $lesson->get_title() ); ?>
    </a>

    <?php if ( ! $has_access && ! $lesson->is_sample() ) : ?>
        <div
            class="ld-tooltip__text"
            id="ld-accordion__tooltip--lesson-<?php echo esc_attr( (string) $lesson->get_id() ); ?>"
            role="tooltip"
        >
            <?php esc_html_e( "You don't currently have access to this content", 'learndash' ); ?>
        </div>
    <?php endif; ?>
</div>