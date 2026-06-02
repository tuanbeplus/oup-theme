(function ($) {
    'use strict';

    var WidgetCourseFilterHandler = function ($scope, $) {
        var $wrapper = $scope.find('.course-filter-wrapper');
        if (!$wrapper.length) {
            return;
        }

        var orderby = $wrapper.data('orderby');
        var order = $wrapper.data('order');
        var nonce = $wrapper.data('nonce');
        var ajaxurl = $wrapper.data('ajaxurl');

        var $resultsContainer = $scope.find('#course-ajax-results');
        
        var $audienceSelect = $scope.find('#course_audience');
        var $subjectSelect = $scope.find('#course_subject');
        var $levelSelect = $scope.find('#course_learning_level');
        
        var ajaxTimeout = null;

        function fetchCourses() {
            var getResponsivePostsPerPage = function() {
                var w = $(window).width();
                if (w <= 767) {
                    return $wrapper.data('posts-per-page-mobile');
                } else if (w <= 1024) {
                    return $wrapper.data('posts-per-page-tablet');
                } else {
                    return $wrapper.data('posts-per-page');
                }
            };
            
            var currentPostsPerPage = getResponsivePostsPerPage();

            var skeletonCard = 
                '<div class="course-card-skeleton">' +
                    '<div class="skeleton-image"></div>' +
                    '<div class="skeleton-content">' +
                        '<div class="skeleton-title"></div>' +
                        '<div class="skeleton-desc"></div>' +
                        '<div class="skeleton-meta"></div>' +
                        '<div class="skeleton-btn"></div>' +
                    '</div>' +
                '</div>';
            
            var allSkeletons = '';
            var count = currentPostsPerPage > 0 ? currentPostsPerPage : 3;
            for (var i = 0; i < count; i++) {
                allSkeletons += skeletonCard;
            }
            
            $resultsContainer.html(allSkeletons);
            $resultsContainer.addClass('loading');

            var audienceVal = $audienceSelect.val();
            var subjectVal = $subjectSelect.val();
            var levelVal = $levelSelect.val();

            $.ajax({
                url: ajaxurl, 
                type: 'POST',
                data: {
                    action: 'filter_courses',
                    course_audience: audienceVal,
                    course_subject: subjectVal,
                    course_learning_level: levelVal,
                    posts_per_page: currentPostsPerPage,
                    orderby: orderby,
                    order: order,
                    nonce: nonce
                },
                success: function (response) {
                    $resultsContainer.html(response);
                    $resultsContainer.removeClass('loading');
                },
                error: function (error) {
                    console.error('AJAX Error:', error);
                    $resultsContainer.removeClass('loading');
                }
            });
        }

        // Avoid re-initializing if already initialized
        if ($wrapper.hasClass('custom-select-initialized')) {
            return;
        }
        $wrapper.addClass('custom-select-initialized');

        // Progressively enhance native selects into custom UI selects
        $wrapper.find('.course-filters .select-wrapper').each(function() {
            var $wrapperDiv = $(this);
            var $select = $wrapperDiv.find('select');
            
            var $customWrapper = $('<div class="custom-select-wrapper"></div>');
            $wrapperDiv.hide().after($customWrapper);
            
            var $trigger = $('<div class="custom-select-trigger"></div>');
            var $text = $('<span></span>').text($select.find('option:selected').text());
            var $icon = $('<svg width="9" height="5" viewBox="0 0 9 5" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.500001 0.5L3.79289 3.79289C4.18342 4.18342 4.81658 4.18342 5.20711 3.79289L8.5 0.500001" stroke="#9677D7" stroke-linecap="round"/></svg>');
            
            $trigger.append($text, $icon);
            $customWrapper.append($trigger);
            
            var $options = $('<div class="custom-select-options"></div>');
            $select.find('option').each(function() {
                var $opt = $(this);
                var $optionDiv = $('<div class="custom-option"></div>')
                    .text($opt.text())
                    .attr('data-value', $opt.val());
                    
                if ($opt.is(':selected')) {
                    $optionDiv.addClass('selected');
                }
                
                $optionDiv.on('click', function(e) {
                    e.stopPropagation();
                    $select.val($opt.val()).trigger('change');
                    $text.text($opt.text());
                    $options.find('.custom-option').removeClass('selected');
                    $(this).addClass('selected');
                    $customWrapper.removeClass('open');
                });
                
                $options.append($optionDiv);
            });
            
            $customWrapper.append($options);
            
            $trigger.on('click', function(e) {
                e.stopPropagation();
                $('.custom-select-wrapper').not($customWrapper).removeClass('open');
                $customWrapper.toggleClass('open');
            });
        });

        $(document).on('click', function() {
            $('.custom-select-wrapper').removeClass('open');
        });

        // Trigger AJAX on native select change
        $wrapper.find('.course-filters select').on('change', function() {
            if (ajaxTimeout) {
                clearTimeout(ajaxTimeout);
            }
            ajaxTimeout = setTimeout(function () {
                fetchCourses();
            }, 300);
        });
    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/course-filter.default', WidgetCourseFilterHandler);
    });

})(jQuery);
