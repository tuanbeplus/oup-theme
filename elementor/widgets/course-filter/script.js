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

        function toggleClearButton() {
            var hasActiveFilter = false;
            $wrapper.find('.course-filters select').each(function() {
                if ($(this).val() !== '*') {
                    hasActiveFilter = true;
                    return false;
                }
            });
            
            if (hasActiveFilter) {
                $wrapper.find('.filter-clear-group').addClass('is-visible');
            } else {
                $wrapper.find('.filter-clear-group').removeClass('is-visible');
            }
        }
        toggleClearButton();

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

    

        // Trigger AJAX on native select change
        $wrapper.find('.course-filters select').on('change', function() {
            if (ajaxTimeout) {
                clearTimeout(ajaxTimeout);
            }
            ajaxTimeout = setTimeout(function () {
                fetchCourses();
            }, 300);
            
            toggleClearButton();
        });

        $wrapper.find('.course-clear-filters').on('click', function(e) {
            e.preventDefault();
      
            $wrapper.find('.course-filters select').val('*');
            toggleClearButton();
  
            fetchCourses();
        });
    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/course-filter.default', WidgetCourseFilterHandler);
    });

})(jQuery);
