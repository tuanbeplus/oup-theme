<?php
/**
 * LearnDash LD30 Displays the listing of course content
 *
 * Available Variables:
 * $course_id                  : (int) ID of the course
 * $course                     : (object) Post object of the course
 * $course_settings            : (array) Settings specific to current course
 *
 * $courses_options            : Options/Settings as configured on Course Options page
 * $lessons_options            : Options/Settings as configured on Lessons Options page
 * $quizzes_options            : Options/Settings as configured on Quiz Options page
 *
 * $user_id                    : Current User ID
 * $logged_in                  : User is logged in
 * $current_user               : (object) Currently logged in user object
 *
 * $course_status              : Course Status
 * $has_access                 : User has access to course or is enrolled.
 * $materials                  : Course Materials
 * $has_course_content         : Course has course content
 * $lessons                    : Lessons Array
 * $quizzes                    : Quizzes Array
 * $lesson_progression_enabled : (true/false)
 * $has_topics                 : (true/false)
 * $lesson_topics              : (array) lessons topics
 *
 * @since 3.0.0
 *
 * @package LearnDash\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Display lessons if they exist
 *
 * @var $lessons [array]
 * @since 3.0.0
 */

if ( ! empty( $lessons ) || ! empty( $quizzes ) ) :

	/**
	 * Filters LearnDash Course table CSS class.
	 *
	 * @since 3.0.0
	 *
	 * @param string $course_table_class CSS classes for course table.
	 */
	$table_class = apply_filters( 'learndash_course_table_class', 'ld-item-list-items ' . ( isset( $lesson_progression_enabled ) && $lesson_progression_enabled ? 'ld-lesson-progression' : '' ) );

	$table_class .= ' ld-item-list-' . absint( $course_id );

	/**
	 * Display the expand button if lesson has topics
	 *
	 * @var $lessons [array]
	 * @since 3.0.0
	 */
	?>

	<div class="<?php echo esc_attr( $table_class ); ?>" id="<?php echo esc_attr( 'ld-item-list-' . $course_id ); ?>" data-ld-expand-id="<?php echo esc_attr( 'ld-item-list-' . $course_id ); ?>" data-ld-expand-list="true">
		<?php
		/**
		 * Fires before the course listing.
		 *
		 * @since 3.0.0
		 *
		 * @param int $course_id Course ID.
		 * @param int $user_id   User ID.
		 */
		do_action( 'learndash-course-listing-before', $course_id, $user_id );

		if ( $lessons && ! empty( $lessons ) ) :

			/**
			 * Loop through each lesson and output a row
			 *
			 * @var $lessons [array]
			 * @since 3.0.0
			 */

			$sections = learndash_30_get_course_sections( $course_id );
			
			if ( $has_access ) :
				// -- ENROLLED VIEW: Show normal LearnDash lessons list --
				$i        = 0;

				foreach ( $lessons as $lesson ) :
					learndash_get_template_part(
						'lesson/partials/row.php',
						array(
							'count'                => $i,
							'sections'             => $sections,
							'lesson'               => $lesson,
							'course_id'            => $course_id,
							'user_id'              => $user_id,
							'lesson_topics'        => ! empty( $lesson_topics ) ? $lesson_topics : [],
							'has_access'           => $has_access,
							'course_pager_results' => $course_pager_results,
						),
						true
					);
					$i++;
				endforeach;
			else:
				// -- NON-ENROLLED VIEW: Show section step counts only --
				$section_counts = array();
				
				foreach ( $lessons as $lesson ) {
					$lesson_id = $lesson->ID;
					$current_section = isset( $sections[ $lesson_id ] ) ? $sections[ $lesson_id ] : '';
					
					if ( ! isset( $section_counts[ $current_section ] ) ) {
						$section_counts[ $current_section ] = 0;
					}
					$section_counts[ $current_section ]++;
				}

				echo '<div class="oup-course-no-access">';
				foreach ( $section_counts as $sec_title => $count ) {
					if ( ! empty( $sec_title ) ) {
						// Output native LearnDash section header markup
						echo '<div class="ld-item-list-item ld-item-list-item-section-heading">';
						echo '<div class="ld-item-list-item-preview">';
						echo '<div class="ld-item-title"><span class="ld-item-name">' . esc_html( $sec_title ) . '</span></div>';
						echo '</div></div>';
					}
					
					$step_label = sprintf( _n( '%d Step', '%d Steps', $count, 'oup-theme' ), $count );
					?>
					<div class="oup-course-section-steps">
						<svg class="oup-course-section-steps__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M3.81815 4C3.3663 4 3 4.35258 3 4.7875V16.6C3 17.0349 3.3663 17.3875 3.81815 17.3875H9.54516C9.97914 17.3875 10.3953 17.5534 10.7022 17.8488C11.0091 18.1442 11.1815 18.5448 11.1815 18.9625C11.1815 19.3974 11.5478 19.75 11.9996 19.75C12.4514 19.75 12.8185 19.3974 12.8185 18.9625C12.8185 18.5448 12.9909 18.1442 13.2978 17.8488C13.6047 17.5534 14.0209 17.3875 14.4548 17.3875H20.1819C20.6337 17.3875 21 17.0349 21 16.6V4.7875C21 4.35258 20.6337 4 20.1819 4H15.273C14.1881 4 13.1476 4.41484 12.3804 5.15327C12.2426 5.28594 12.1156 5.4271 12 5.57549C11.8844 5.4271 11.7574 5.28594 11.6196 5.15327C10.8524 4.41484 9.81195 4 8.72702 4H3.81815ZM11.1815 7.9375V16.2345C10.6882 15.9604 10.1246 15.8125 9.54516 15.8125H4.63629V5.575H8.72702C9.37798 5.575 10.0023 5.82391 10.4626 6.26696C10.9229 6.71001 11.1815 7.31093 11.1815 7.9375ZM14.4548 15.8125C13.8754 15.8125 13.3118 15.9604 12.8185 16.2345V7.9375C12.8185 7.31093 13.0771 6.71001 13.5374 6.26696C13.9977 5.82391 14.622 5.575 15.273 5.575H19.3637V15.8125H14.4548Z"/>
						</svg>
						<span class="oup-course-section-steps__text"><?php echo esc_html( $step_label ); ?></span>
					</div>
					<?php
				}
				echo '</div>'; // close .oup-course-no-access
			endif;

		endif;

		/**
		 * Determine if we should show course quizzes at this point or not
		 *
		 * @var $show_course_quizzes boolean
		 * @since 3.0.0
		 */
		$show_course_quizzes = true;
		if ( isset( $course_pager_results['pager'] ) && ! empty( $course_pager_results['pager'] ) && 0 !== absint( $course_pager_results['pager']['total_pages'] ) ) :
			$show_course_quizzes = ( $course_pager_results['pager']['paged'] == $course_pager_results['pager']['total_pages'] ? true : false );
		endif;
		/**
		 * Filters whether to show course quizzes while listing the course content
		 *
		 * @since 3.0.0
		 *
		 * @param boolean $show_course_quizzes Whether to show course quizzes in course listing or not.
		 * @param int     $course_id           Course ID.
		 * @param int     $user_id             User ID.
		 */
		$show_course_quizzes = apply_filters( 'learndash-show-course-quizzes', $show_course_quizzes, $course_id, $user_id );

		if ( $show_course_quizzes && ! empty( $quizzes ) ) :
			foreach ( $quizzes as $quiz ) :
				learndash_get_template_part(
					'quiz/partials/row.php',
					array(
						'course_id'  => $course_id,
						'user_id'    => $user_id,
						'context'    => 'course',
						'quiz'       => $quiz,
						'has_access' => $has_access,
					),
					true
				);
			endforeach;
		endif;

		/**
		 * Fires after the course listing.
		 *
		 * @since 3.0.0
		 *
		 * @param int $course_id Course ID.
		 * @param int $user_id   User ID.
		 */
		do_action( 'learndash-course-listing-after', $course_id, $user_id );

		/**
		 * Fires before the course pagination.
		 *
		 * @since 3.0.0
		 *
		 * @param int $course_id Course ID.
		 * @param int $user_id   User ID.
		 */
		do_action( 'learndash-course-pagination-before', $course_id, $user_id );

		if ( isset( $course_pager_results['pager'] ) ) :
			learndash_get_template_part(
				'modules/pagination.php',
				array(
					'pager_results' => $course_pager_results['pager'],
					'pager_context' => ( isset( $context ) ? $context : 'course_lessons' ),
					'course_id'     => $course_id,
				),
				true
			);
		endif;

		/**
		 * Fires after the course pagination.
		 *
		 * @since 3.0.0
		 *
		 * @param int $course_id Course ID.
		 * @param int $user_id   User ID.
		 */
		do_action( 'learndash-course-pagination-after', $course_id, $user_id );
		?>
	</div> <!--/.ld-item-list-items-->
<?php endif; ?>
