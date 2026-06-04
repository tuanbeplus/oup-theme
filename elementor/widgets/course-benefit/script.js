jQuery(document).ready(function($) {
    function initCourseBenefit($scope) {
    
    }

    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/course-benefit.default', initCourseBenefit);
    });

    $('.elementor-widget-course-benefit').each(function() {
        initCourseBenefit($(this));
    });
});
