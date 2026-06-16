(function ($) {
    'use strict';

    // Blog Search Widget
    function initBlogSearch() {
        const $widget = $('.blog-search-widget');
        if (!$widget.length) return;
        if ($widget.data('bs-init')) return;
        $widget.data('bs-init', true);

        const $input = $widget.find('.bs-input');
        const $wrap = $widget.find('.bs-wrap');
        const debounce = parseInt($widget.data('debounce')) || 400;
        let timer = null;

        const $clear = $(`
            <button class="bs-clear" type="button" aria-label="Clear search" tabindex="-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 14" fill="none">
                    <circle cx="7" cy="7" r="7"/>
                    <path d="M4.5 4.5L9.5 9.5M9.5 4.5L4.5 9.5" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </button>
        `);
        $wrap.append($clear);

        function updateClear() {
            const hasVal = $input.val().length > 0;
            $wrap.toggleClass('has-value', hasVal);
        }

        $input.on('input', function () {
            updateClear();
            const q = $(this).val().trim();
            clearTimeout(timer);
            timer = setTimeout(function () {
                $(document).trigger('apf:search', [q]);
            }, debounce);
        });

        $clear.on('click', function () {
            $input.val('').trigger('input').focus();
        });

        updateClear();
    }

    // Archive Posts Filter Widget
    function initArchivePostsFilter() {
        const $widget = $('.archive-posts-filter-widget');
        if (!$widget.length) return;
        if ($widget.data('apf-init')) return;
        $widget.data('apf-init', true);

        const ajaxUrl = (typeof ajax_object !== 'undefined') ? ajax_object.ajaxurl : '/wp-admin/admin-ajax.php';
        const postType = String($widget.data('post-type') || 'post');
        const taxonomy = String($widget.data('taxonomy') || 'category');
        const perPage = parseInt($widget.data('per-page')) || 6;
        const orderby = String($widget.data('orderby') || 'date');
        const order = String($widget.data('order') || 'DESC');

        const $grid = $widget.find('.apf-grid');
        const $sentinel = $widget.find('.apf-sentinel');
        const maxPagesMap = $widget.data('max-pages') || {};

        let preloaded = {};
        try { preloaded = JSON.parse($widget.find('.apf-preloaded-data').html() || '{}'); } catch (_) { }

        // pageState key is now a single string ('all' or a term_id string)
        const pageState = {};
        Object.keys(preloaded).forEach(key => {
            pageState[key] = { page: 2, maxPages: parseInt(maxPagesMap[key]) || 1 };
        });

        // Single active term — replaces the previous multi-select Set
        let activeTerm = 'all';
        let currentSearch = '';
        let currentXhr = null;
        let observer = null;
        let isLoading = false;

        // Cache key is just the active term string (null in search mode)
        function getCacheKey() {
            if (currentSearch) return null;
            return activeTerm;
        }

        function buildSkeletonCards(count) {
            const card = `
                <div class="apf-skeleton-card" aria-hidden="true">
                    <div class="apf-skeleton__thumb"></div>
                    <div class="apf-skeleton__body">
                        <div class="apf-skeleton__meta">
                            <div class="apf-skeleton__date"></div>
                            <div class="apf-skeleton__tag"></div>
                        </div>
                        <div class="apf-skeleton__title"></div>
                        <div class="apf-skeleton__title"></div>
                        <div class="apf-skeleton__excerpt"></div>
                        <div class="apf-skeleton__excerpt"></div>
                        <div class="apf-skeleton__excerpt"></div>
                        <div class="apf-skeleton__author">
                            <div class="apf-skeleton__author-name"></div>
                            <div class="apf-skeleton__author-role"></div>
                        </div>
                    </div>
                </div>`;
            return Array(count).fill(card).join('');
        }

        function updateTabUI() {
            $widget.find('.apf-tab').each(function () {
                const term = String($(this).data('term'));
                const isActive = term === activeTerm;
                $(this).toggleClass('active', isActive).attr('aria-selected', String(isActive));
            });
        }

        function animateCards($cards) {
            $cards.removeClass('apf-animate-in');
            requestAnimationFrame(() => requestAnimationFrame(() => $cards.addClass('apf-animate-in')));
        }

        function revealBootCards() {
            $grid.find('.apf-card').addClass('apf-animate-in');
        }

        function getCleanGridHTML() {
            return $grid.find('.apf-card').map(function () { return this.outerHTML; }).get().join('');
        }

        function abortXhr() {
            if (currentXhr) { currentXhr.abort(); currentXhr = null; }
        }

        function stopObserver() {
            if (observer) { observer.disconnect(); observer = null; }
        }

        function startObserver() {
            stopObserver();
            if (currentSearch) return; // no infinite scroll in search mode
            observer = new IntersectionObserver(entries => {
                if (!entries[0].isIntersecting) return;
                if (isLoading) return;
                const state = pageState[getCacheKey()];
                if (state && state.page <= state.maxPages) loadNextPage();
            }, { rootMargin: '200px', threshold: 0 });
            observer.observe($sentinel[0]);
        }

        // Select a single term — clicking the active tab does nothing
        function selectTerm(term) {
            if (term === activeTerm) return;
            activeTerm = term;
            updateTabUI();
            renderCurrentSelection();
        }

        function renderCurrentSelection() {
            abortXhr();
            stopObserver();
            isLoading = false;

            const key = getCacheKey();

            if (currentSearch) {
                $grid.html(buildSkeletonCards(perPage));
                isLoading = true;
                fetchPage(1, null, (html) => {
                    isLoading = false;
                    $grid.html(html);
                    animateCards($grid.find('.apf-card'));
                });
                return;
            }

            if (!pageState[key]) {
                pageState[key] = { page: 2, maxPages: 1 };
            }

            if (preloaded[key] !== undefined) {
                $grid.html(preloaded[key]);
                animateCards($grid.find('.apf-card'));
                startObserver();
            } else {
                $grid.html(buildSkeletonCards(perPage));
                isLoading = true;
                fetchPage(1, key, (html, maxPages) => {
                    preloaded[key] = html;
                    pageState[key] = { page: 2, maxPages };
                    isLoading = false;
                    $grid.html(html);
                    animateCards($grid.find('.apf-card'));
                    startObserver();
                });
            }
        }

        // terms param is now a single term string, not a comma-separated list
        function fetchPage(paged, keySnapshot, onSuccess) {
            const termsParam = keySnapshot !== null ? keySnapshot : 'all';

            currentXhr = $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'oup_load_archive_posts',
                    paged,
                    per_page: perPage,
                    terms: termsParam,
                    post_type: postType,
                    taxonomy,
                    orderby,
                    order,
                    search: currentSearch,
                },
                success(response) {
                    if (keySnapshot !== null && getCacheKey() !== keySnapshot) return;
                    if (!response.success) return;
                    onSuccess(response.data.html, parseInt(response.data.max_pages) || 1);
                },
                complete(_, status) {
                    if (status !== 'abort') {
                        currentXhr = null;
                        $grid.find('.apf-append-skeleton').remove();
                    }
                },
            });
        }

        function loadNextPage() {
            const key = getCacheKey();
            const state = pageState[key];
            const keySnapshot = key;
            isLoading = true;
            abortXhr();
            $grid.append($(buildSkeletonCards(perPage)).addClass('apf-append-skeleton'));

            fetchPage(state.page, keySnapshot, (html, maxPages) => {
                $grid.find('.apf-append-skeleton').remove();
                const $newCards = $(html);
                $grid.append($newCards);
                animateCards($newCards);
                preloaded[key] = getCleanGridHTML();
                state.page++;
                state.maxPages = maxPages;
                requestAnimationFrame(() => { isLoading = false; });
            });
        }

        // Single-choice: selectTerm replaces the old multi-select toggleTerm
        $widget.on('click', '.apf-tab', function () {
            selectTerm(String($(this).data('term')));
        });

        $(document).on('apf:search', function (e, query) {
            currentSearch = query;
            if (query) {
                activeTerm = 'all'; // reset tab to All when searching
                updateTabUI();
            }
            renderCurrentSelection();
        });

        updateTabUI();
        revealBootCards();
        startObserver();

        const urlCatSlug = new URLSearchParams(location.search).get('archive_cat');
        if (urlCatSlug) {
            setTimeout(() => {
                $widget.find('.apf-tab').filter(function () {
                    return $(this).text().trim().toLowerCase() === urlCatSlug.toLowerCase();
                }).trigger('click');
            }, 100);
        }
    }

    function init() {
        initBlogSearch();
        initArchivePostsFilter();
    }

    $(document).ready(init);
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/blog-search.default', () => initBlogSearch());
        elementorFrontend.hooks.addAction('frontend/element_ready/archive-posts-filter.default', () => initArchivePostsFilter());
    });

}(jQuery));