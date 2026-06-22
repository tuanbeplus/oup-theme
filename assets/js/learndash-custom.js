/**
 * Custom JavaScript for LearnDash Logic
 */
(function ($) {
    'use strict';

    // 1. Clean up LearnDash URL parameters after registration errors
    $(window).on('load', function() {
        if (window.history && window.history.replaceState) {
            var url = new URL(window.location.href);
            var paramsToClean = ['ld_register_id', 'user_login', 'user_email', 'first_name', 'last_name', 'username_exists', 'email_exists'];
            var isDirty = false;
            
            paramsToClean.forEach(function(param) {
                if (url.searchParams.has(param)) {
                    url.searchParams.delete(param);
                    isDirty = true;
                }
            });
            
            if (isDirty) {
                window.history.replaceState({}, document.title, url.toString());
            }
        }
    });

    // 2. Password Show/Hide Toggle for LearnDash forms
    $(document).on('click', '.ld-button__password-visibility-toggle', function(e) {
        e.preventDefault();
        var $wrapper = $(this).closest('.ld-form__field-password-wrapper');
        var $input = $wrapper.find('input');
        
        if ($input.length) {
            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text');
                $(this).text('Hide');
            } else {
                $input.attr('type', 'password');
                $(this).text('Show');
            }
        }
    });

})(jQuery);
