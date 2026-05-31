(function ($) {
    'use strict';

    $(function () {
        var $wrapper = $('.worksheet-filter-wrapper');
        if (!$wrapper.length) {
            return;
        }

        var postsPerPage = $wrapper.data('posts-per-page');
        var orderby = $wrapper.data('orderby');
        var order = $wrapper.data('order');
        var nonce = $wrapper.data('nonce');
        var ajaxurl = $wrapper.data('ajaxurl');

        var $resultsContainer = $('#worksheet-ajax-results');
        var $searchInput = $('#worksheet-search-input');
        var $filterBtns = $('.filter-btn');
        
        var currentSearch = '';
        var currentCategory = '*';
        var ajaxTimeout = null;

        function fetchWorksheets() {
            var skeletonCard = 
                '<div class="worksheet-card-skeleton">' +
                    '<div class="skeleton-image"></div>' +
                    '<div class="skeleton-content">' +
                        '<div class="skeleton-title"></div>' +
                        '<div class="skeleton-title short"></div>' +
                        '<div class="skeleton-badge"></div>' +
                    '</div>' +
                '</div>';
            
            var allSkeletons = '';
            var count = postsPerPage > 0 ? postsPerPage : 9;
            for (var i = 0; i < count; i++) {
                allSkeletons += skeletonCard;
            }
            
            $resultsContainer.html(allSkeletons);
            $resultsContainer.addClass('loading');

            $.ajax({
                url: ajaxurl, 
                type: 'POST',
                data: {
                    action: 'filter_worksheets',
                    search: currentSearch,
                    category: currentCategory,
                    posts_per_page: postsPerPage,
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

        $filterBtns.on('click', function (e) {
            e.preventDefault();
            var $this = $(this);
            
            $filterBtns.removeClass('active');
            $this.addClass('active');

            currentCategory = $this.data('filter');
            fetchWorksheets();
        });

        $searchInput.on('keyup', function () {
            currentSearch = $(this).val().trim();
            
            if (ajaxTimeout) {
                clearTimeout(ajaxTimeout);
            }
            
            ajaxTimeout = setTimeout(function () {
                fetchWorksheets();
            }, 500);
        });
    });

})(jQuery);
