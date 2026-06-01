jQuery(document).ready(function($) {
    function initWorksheetAccordion($scope) {
        var $wrapper = $scope.find('.worksheet-accordion-wrapper');
        if (!$wrapper.length) {
            return;
        }

        $wrapper.find('.worksheet-accordion-header').on('click', function(e) {
            e.preventDefault();
            var $this = $(this);
            var $content = $this.next('.worksheet-accordion-content');
            var isActive = $this.hasClass('active');

            if (isActive) {
                $this.removeClass('active');
                $this.attr('aria-expanded', 'false');
                $content.slideUp(300);
            } else {
                $this.addClass('active');
                $this.attr('aria-expanded', 'true');
                $content.slideDown(300);
            }
        });
    }

    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/worksheet-accordion.default', initWorksheetAccordion);
    });
    
    $('.elementor-widget-worksheet-accordion').each(function() {
        initWorksheetAccordion($(this));
    });
});
