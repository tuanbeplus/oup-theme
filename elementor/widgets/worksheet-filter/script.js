(function ($) {
    'use strict';

    $(function () {
        var $wrapper = $('.worksheet-filter-wrapper');
        if (!$wrapper.length) return;

        var postsPerPage = $wrapper.data('posts-per-page');
        var orderby = $wrapper.data('orderby');
        var order = $wrapper.data('order');
        var nonce = $wrapper.data('nonce');
        var ajaxurl = $wrapper.data('ajaxurl');
        var skeletonCount = postsPerPage > 0 ? postsPerPage : 9;

        var $resultsContainer = $('#worksheet-ajax-results');
        var $searchInput = $('#worksheet-search-input');
        var $filterBtns = $('.filter-btn');

        var currentSearch = '';
        var currentCategory = '*';
        var ajaxTimeout = null;

        var skeletonCard =
            '<div class="worksheet-card-skeleton">' +
            '<div class="skeleton-image"></div>' +
            '<div class="skeleton-content">' +
            '<div class="skeleton-title"></div>' +
            '<div class="skeleton-title short"></div>' +
            '<div class="skeleton-badge"></div>' +
            '</div>' +
            '</div>';

        function buildSkeletons() {
            var html = '';
            for (var i = 0; i < skeletonCount; i++) html += skeletonCard;
            return html;
        }

        function fetchWorksheets() {
            $resultsContainer.html(buildSkeletons()).addClass('loading');

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
                    nonce: nonce,
                },
                success: function (response) {
                    $resultsContainer.html(response).removeClass('loading');
                },
                error: function (xhr, status, err) {
                    console.error('AJAX Error:', status, err);
                    $resultsContainer.removeClass('loading');
                },
            });
        }

        $filterBtns.on('click', function (e) {
            e.preventDefault();
            $filterBtns.removeClass('active');
            $(this).addClass('active');
            currentCategory = $(this).data('filter');
            fetchWorksheets();
        });

        $searchInput.on('keyup', function () {
            currentSearch = $(this).val().trim();
            clearTimeout(ajaxTimeout);
            ajaxTimeout = setTimeout(fetchWorksheets, 500);
        });
    });

})(jQuery);