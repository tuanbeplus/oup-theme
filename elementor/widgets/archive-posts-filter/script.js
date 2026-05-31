(function ($) {
    'use strict';

    function initArchivePostsFilter() {
        const $widget = $('.archive-posts-filter-widget');
        if (!$widget.length) return;

        const SPINNER_HTML = `<div class="apf-spinner-wrap"><div class="apf-spinner"></div></div>`;
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

        const pageState = {};
        Object.keys(preloaded).forEach(key => {
            pageState[key] = {
                page: 2,
                maxPages: parseInt(maxPagesMap[key]) || 1,
            };
        });

        let activeTerms = new Set(['all']);
        let currentXhr = null;
        let observer = null;
        let isLoading = false;

        function getCacheKey() {
            if (activeTerms.has('all')) return 'all';
            return [...activeTerms].map(Number).sort((a, b) => a - b).join(',');
        }

        // UI
        function updateTabUI() {
            $widget.find('.apf-tab').each(function () {
                const term = String($(this).data('term'));
                const isActive = activeTerms.has(term);
                $(this).toggleClass('active', isActive).attr('aria-selected', String(isActive));
            });
        }

        function animateCards($cards) {
            $cards.removeClass('apf-animate-in');
            requestAnimationFrame(() => requestAnimationFrame(() => $cards.addClass('apf-animate-in')));
        }

        // Only collect .apf-card elements — never spinners or empty notices
        function getCleanGridHTML() {
            return $grid.find('.apf-card').map(function () { return this.outerHTML; }).get().join('');
        }

        function abortXhr() {
            if (currentXhr) { currentXhr.abort(); currentXhr = null; }
        }

        function stopObserver() {
            if (observer) { observer.disconnect(); observer = null; }
        }

        // Start ONE observer per tab-switch — never called inside loadNextPage
        function startObserver() {
            stopObserver();
            observer = new IntersectionObserver(entries => {
                if (!entries[0].isIntersecting) return;
                if (isLoading) return; // hard guard against concurrent loads
                const state = pageState[getCacheKey()];
                if (state && state.page <= state.maxPages) loadNextPage();
            }, { rootMargin: '200px', threshold: 0 });
            observer.observe($sentinel[0]);
        }

        // Term selection
        function toggleTerm(term) {
            if (term === 'all') {
                activeTerms = new Set(['all']);
            } else {
                activeTerms.delete('all');
                if (activeTerms.has(term)) {
                    activeTerms.delete(term);
                    if (activeTerms.size === 0) activeTerms = new Set(['all']);
                } else {
                    activeTerms.add(term);
                }
            }
            updateTabUI();
            renderCurrentSelection();
        }

        // Render grid
        function renderCurrentSelection() {
            abortXhr();
            stopObserver();
            isLoading = false;

            const key = getCacheKey();

            if (!pageState[key]) {
                pageState[key] = { page: 2, maxPages: 1 };
            }

            if (preloaded[key] !== undefined) {
                $grid.html(preloaded[key]);
                animateCards($grid.find('.apf-card'));
                startObserver();
            } else {
                // Multi-term combo not yet cached — fetch page 1
                $grid.html(SPINNER_HTML);
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

        // AJAX helper
        function fetchPage(paged, keySnapshot, onSuccess) {
            currentXhr = $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'oup_load_archive_posts',
                    paged,
                    per_page: perPage,
                    terms: keySnapshot,
                    post_type: postType,
                    taxonomy,
                    orderby,
                    order,
                },
                success(response) {
                    if (getCacheKey() !== keySnapshot) return; // stale guard
                    if (!response.success) return;
                    onSuccess(response.data.html, parseInt(response.data.max_pages) || 1);
                },
                complete(_, status) {
                    if (status !== 'abort') {
                        currentXhr = null;
                        $grid.find('.apf-append-spinner').remove();
                    }
                },
            });
        }

        // Infinite scroll 
        function loadNextPage() {
            const key = getCacheKey();
            const state = pageState[key];
            const keySnapshot = key;
            isLoading = true;
            abortXhr();
            $grid.append($(SPINNER_HTML).addClass('apf-append-spinner'));

            fetchPage(state.page, keySnapshot, (html, maxPages) => {
                $grid.find('.apf-append-spinner').remove();
                const $newCards = $(html);
                $grid.append($newCards);
                animateCards($newCards);
                preloaded[key] = getCleanGridHTML();
                state.page++;
                state.maxPages = maxPages;

                requestAnimationFrame(() => {
                    isLoading = false;
                });
            });
        }

        // Events
        $widget.on('click', '.apf-tab', function () {
            toggleTerm(String($(this).data('term')));
        });

        // Boot
        updateTabUI();
        animateCards($grid.find('.apf-card'));
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

    $(document).ready(initArchivePostsFilter);
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction(
            'frontend/element_ready/archive-posts-filter.default',
            () => initArchivePostsFilter()
        );
    });

}(jQuery));