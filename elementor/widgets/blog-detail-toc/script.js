(function ($) {
    'use strict';

    function initBlogDetailToc($scope) {
        var $widgets = $scope ? $scope.find('.table-of-content-all.all') : $('.table-of-content-all.all');

        $widgets.each(function () {
            var $toc = $(this);

            var isEditor = Boolean(
                typeof elementorFrontend !== 'undefined' &&
                elementorFrontend.isEditMode &&
                elementorFrontend.isEditMode()
            );

            // Skip re-init on frontend; always re-render in editor
            if (!isEditor) {
                if ($toc.data('toc-initialized')) return;
                $toc.data('toc-initialized', true);
            }

            var tocId = $toc.attr('id');

            // Remove previous scroll listener before rebuilding
            $(window).off('scroll.tocall-' + tocId);

            // Read settings from data attributes
            var includeTags = ($toc.attr('data-toc-tags') || 'h2').split(',').map(Function.prototype.call, String.prototype.trim).filter(Boolean);
            var excludeTags = ($toc.attr('data-toc-exclude') || '').split(',').map(Function.prototype.call, String.prototype.trim).filter(Boolean);
            var containerSel = ($toc.attr('data-toc-container') || '').trim();
            var marker = ($toc.attr('data-toc-marker') || 'bullets').trim();
            var iconClass = ($toc.attr('data-toc-icon') || '').trim();
            var noHeadingsMsg = $toc.attr('data-toc-noheadings') || 'No headings were found on this page.';

            var $list = $toc.find('.table-of-content-all__list');

            // Determine search root (body or custom container)
            var $root = $('body');
            if (containerSel) {
                var $container = $(containerSel).first();
                if ($container.length) {
                    $root = $container;
                } else {
                    console.warn('[TOC] Container "' + containerSel + '" not found, falling back to body.');
                }
            }

            // Build active tag list (include minus exclude)
            var activeTags = includeTags.filter(function (tag) {
                return excludeTags.indexOf(tag) === -1;
            });

            if (!activeTags.length) {
                $list.html('<li class="table-of-content-all__no-headings">' + $('<span>').text(noHeadingsMsg).html() + '</li>');
                return;
            }

            // Collect headings & auto-assign IDs
            var headings = [];
            var idCounter = {};

            $root.find(activeTags.join(',')).each(function () {
                var $h = $(this);

                // Skip headings inside the TOC widget itself
                if ($h.closest('.table-of-content-all').length) return;

                var text = $h.text().trim();
                if (!text) return;

                var id = $h.attr('id');

                if (!id) {
                    var slug = text
                        .toLowerCase()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-|-$/g, '')
                        .substring(0, 60) || 'heading';

                    if (idCounter[slug] === undefined) {
                        idCounter[slug] = 0;
                    } else {
                        slug = slug + '-' + (++idCounter[slug]);
                    }

                    id = slug;
                    $h.attr('id', id);
                }

                headings.push({ id: id, text: text, $el: $h });
            });

            // Remove duplicate IDs
            headings = headings.filter(function (h, idx, arr) {
                return arr.findIndex(function (x) { return x.id === h.id; }) === idx;
            });

            if (!headings.length) {
                $list.html('<li class="table-of-content-all__no-headings">' + $('<span>').text(noHeadingsMsg).html() + '</li>');
                return;
            }

            // Render TOC list
            var html = '';
            headings.forEach(function (h, i) {
                html += '<li class="table-of-content-all__item" data-toc-id="' + h.id + '">';

                if (marker === 'number') {
                    html += '<span class="table-of-content-all__marker">' + (i + 1) + '.</span>';
                } else if (marker === 'bullets' && iconClass) {
                    html += '<span class="table-of-content-all__icon"><i class="' + iconClass + '"></i></span>';
                }

                html += '<a class="table-of-content-all__heading" href="#' + h.id + '">'
                    + $('<span>').text(h.text).html()
                    + '</a></li>';
            });

            $list.html(html);

            // Editor only needs the list rendered, no scroll interactions
            if (isEditor) return;

            var $items = $list.find('.table-of-content-all__item');
            var $links = $list.find('.table-of-content-all__heading');

            var isScrollingFromClick = false;
            var scrollEndTimer = null;
            var rafPending = false;

            function getScrollOffset() {
                return parseInt(getComputedStyle(document.documentElement).getPropertyValue('--header-height')) || 0;
            }

            function setActiveById(id) {
                $items.removeClass('active');
                $items.filter('[data-toc-id="' + id + '"]').addClass('active');
            }

            function applyActiveScroll(id) {
                setActiveById(id);
            }

            function scrollToHeading(id, pushState) {
                var target = document.getElementById(id);
                if (!target) return;

                var top = target.getBoundingClientRect().top + window.pageYOffset - getScrollOffset() - 50;

                isScrollingFromClick = true;
                clearTimeout(scrollEndTimer);

                window.scrollTo({ top: top, behavior: 'smooth' });
                setActiveById(id);

                if (pushState && window.history && window.history.pushState) {
                    window.history.pushState(null, null, '#' + id);
                }

                if ('onscrollend' in window) {
                    window.addEventListener('scrollend', function onEnd() {
                        isScrollingFromClick = false;
                        window.removeEventListener('scrollend', onEnd);
                    }, { once: true });
                } else {
                    scrollEndTimer = setTimeout(function () {
                        isScrollingFromClick = false;
                    }, 800);
                }
            }

            // Click: scroll + push URL hash
            $links.on('click', function (e) {
                e.preventDefault();
                scrollToHeading($(this).attr('href').replace('#', ''), true);
            });

            // On page load: scroll to hash or auto-active first
            var initialHash = window.location.hash.replace('#', '');
            if (initialHash) {
                setTimeout(function () {
                    var match = headings.filter(function (h) { return h.id === initialHash; });
                    if (match.length) scrollToHeading(initialHash, false);
                }, 300);
            } else {
                setTimeout(function () {
                    setActiveById(headings[0].id);
                }, 100);
            }

            // Browser back/forward
            $(window).off('popstate.tocall-' + tocId).on('popstate.tocall-' + tocId, function () {
                var hash = window.location.hash.replace('#', '');
                if (hash) {
                    var match = headings.filter(function (h) { return h.id === hash; });
                    if (match.length) scrollToHeading(hash, false);
                } else {
                    setActiveById(headings[0].id);
                }
            });

            // Scroll listener (RAF-throttled), never touches URL
            function updateActiveOnScroll() {
                if (isScrollingFromClick) return;

                var scrollOffset = getScrollOffset();
                var scrollPos = window.pageYOffset + scrollOffset + 60;

                var lastEl = headings[headings.length - 1].$el[0];
                var lastBottom = lastEl.getBoundingClientRect().bottom + window.pageYOffset;
                if (window.pageYOffset > lastBottom) return;

                var found = false;
                for (var i = headings.length - 1; i >= 0; i--) {
                    var top = headings[i].$el[0].getBoundingClientRect().top + window.pageYOffset;
                    if (top <= scrollPos) {
                        var currentId = $items.filter('.active').attr('data-toc-id');
                        if (currentId !== headings[i].id) applyActiveScroll(headings[i].id);
                        found = true;
                        break;
                    }
                }

                if (!found) {
                    var currentId = $items.filter('.active').attr('data-toc-id');
                    if (currentId !== headings[0].id) applyActiveScroll(headings[0].id);
                }
            }

            function onScroll() {
                if (rafPending) return;
                rafPending = true;
                requestAnimationFrame(function () {
                    updateActiveOnScroll();
                    rafPending = false;
                });
            }

            $(window).on('scroll.tocall-' + tocId, onScroll);
            updateActiveOnScroll();
        });
    }

    // Elementor hooks
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction(
            'frontend/element_ready/blog-detail-toc.default',
            initBlogDetailToc
        );
    });

    // Fallback: init on DOM ready (non-Elementor pages / plain WP)
    $(document).ready(function () {
        if (typeof elementorFrontend === 'undefined') {
            initBlogDetailToc(null);
        }
    });

})(jQuery);