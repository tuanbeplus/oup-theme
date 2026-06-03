jQuery(document).ready(function($) {
    function initCourseAccordion($scope) {
        var $wrapper = $scope.find('.course-accordion-wrapper');
        if (!$wrapper.length) {
            return;
        }

        var maxItems = $wrapper.data('max-items') || 'one';
        var animDuration = parseInt($wrapper.data('anim-duration'), 10);
        if (isNaN(animDuration)) {
            animDuration = 400;
        }

        $wrapper.find('.course-accordion-header').on('click', function(e) {
            e.preventDefault();
            var $this = $(this);
            var $content = $this.next('.course-accordion-content');
            var isActive = $this.hasClass('active');

            if (maxItems === 'one') {
                $wrapper.find('.course-accordion-header').not($this).removeClass('active').attr('aria-expanded', 'false');
                $wrapper.find('.course-accordion-content').not($content).slideUp(animDuration);
            }
            
            if (isActive) {
                $this.removeClass('active');
                $this.attr('aria-expanded', 'false');
                $content.slideUp(animDuration);
            } else {
                $this.addClass('active');
                $this.attr('aria-expanded', 'true');
                $content.slideDown(animDuration);
            }
        });
    }

    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/course-accordion.default', initCourseAccordion);
    });
    
    $('.elementor-widget-course-accordion').each(function() {
        initCourseAccordion($(this));
    });
});
