<?php
/**
 * View: Course Accordion - Lessons (Theme Override).
 *
 * @since 4.21.0
 * @version 4.21.0
 *
 * @var array<int, string>                               $sections   Section titles indexed by lesson IDs.
 * @var Course                                           $course     Course model object.
 * @var Lesson[]                                         $lessons    Array of lesson model objects.
 * @var array{lessons: array{paged: int, per_page: int}} $pagination Pagination data.
 * @var Template                                         $this       Current Instance of template engine rendering this template.
 *
 * @package LearnDash\Core
 */

use LearnDash\Core\Models\Course;
use LearnDash\Core\Models\Lesson;
use LearnDash\Core\Template\Template;

if ( empty( $lessons ) ) {
	return;
}

$user_id   = get_current_user_id();
$course_id = $course->get_id();
$has_access = $user_id && function_exists( 'sfwd_lms_has_access' ) && sfwd_lms_has_access( $course_id, $user_id );
?>
<div
	class="ld-accordion__section ld-accordion__section--lessons <?php echo ! $has_access ? 'oup-course-no-access' : ''; ?>"
	data-ld-pagination-target="<?php echo esc_attr( LDLMS_Post_Types::LESSON ); ?>"
>
	<div class="ld-accordion__items ld-accordion__items--lessons">
		<?php
		if ( $has_access ) {
			// -- ENROLLED VIEW: Show normal LearnDash lessons list --
			foreach ( $lessons as $lesson ) : 
				$this->template(
					'modern/course/accordion/section',
					[
						'title' => $sections[ $lesson->get_id() ] ?? '',
					]
				);

				$this->template( 'modern/course/accordion/lessons/lesson', [ 'lesson' => $lesson ] );
			endforeach;

			$this->template( 'modern/course/accordion/lessons/pagination' );

		} else {
			// -- NON-ENROLLED VIEW: Show section step counts only --
			
			// Group lessons by section to count steps per section
			$section_counts = [];
			$current_section = '';
			
			foreach ( $lessons as $lesson ) {
				$lesson_id = $lesson->get_id();
				if ( ! empty( $sections[ $lesson_id ] ) ) {
					$current_section = $sections[ $lesson_id ];
				}
				
				if ( ! isset( $section_counts[ $current_section ] ) ) {
					$section_counts[ $current_section ] = 0;
				}
				$section_counts[ $current_section ]++;
			}

			// Output the sections and their step counts
			foreach ( $section_counts as $sec_title => $count ) {
				if ( ! empty( $sec_title ) ) {
					$this->template(
						'modern/course/accordion/section',
						[ 'title' => $sec_title ]
					);
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
		}
		?>
	</div>
</div>
