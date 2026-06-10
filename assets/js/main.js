/**
 * Main — JS
 *
 * @package Onwards-Upwards-Psychology-Theme
 */

(function ($) {
    'use strict';

    const siteHeader = $('header.elementor-location-header');

    // Set header height for --header-height CSS variable
    function setHeaderHeight() {
        if (siteHeader.length) {
            var h = siteHeader.outerHeight();
            document.documentElement.style.setProperty('--header-height', h + 'px');
        }
    }

    // Handle sticky header
    function handleStickyHeader() {
        if (siteHeader.length) {
            if ($(window).scrollTop() > 0) {
                siteHeader.addClass('sticky');
            } else {
                siteHeader.removeClass('sticky');
            }
        }
    }

    $(function () {
        setHeaderHeight();
        handleStickyHeader();

        $(window).on('resize', setHeaderHeight);
        $(window).on('scroll', handleStickyHeader);
    });

    $(document).on('click', '.carousel-btn-pre', function (e) {
        e.preventDefault();
        const section = $(this).closest('.testimonial-section');
        section.find('.elementor-swiper-button-prev').click();
    });

    $(document).on('click', '.carousel-btn-next', function (e) {
        e.preventDefault();
        const section = $(this).closest('.testimonial-section');
        section.find('.elementor-swiper-button-next').click();
    });

    $(document).on('click', '.custom-add-to-cart', function (e) {
        e.preventDefault();
        const currentBtn = $(this);
        const productId = currentBtn.attr('id');
        const btnATCajax = $('.add_to_cart_button[data-product_id=' + productId + ']');
        const textBtn = currentBtn.find('.elementor-button-text').length ? currentBtn.find('.elementor-button-text') : currentBtn;

        currentBtn.addClass('loading');
        btnATCajax.click();

        // Track if the btnATCajax has added class "added"
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (mutation.attributeName === 'class' && btnATCajax.hasClass('added')) {
                    currentBtn.removeClass('loading');
                    textBtn.text('Added');

                    // Change text back after 3 seconds
                    setTimeout(function () {
                        textBtn.text('Add to cart');
                        btnATCajax.removeClass('added'); // Reset for future clicks
                    }, 5000);

                    observer.disconnect();
                }
            });
        });

        if (btnATCajax.length) {
            observer.observe(btnATCajax[0], { attributes: true });
        }
    });

    $(document).on('click', '.elementor-nav-menu .menu-item-has-children > a', function (e) {
        // If the user clicks on the chevron (sub-arrow), prevent the default link navigation
        if ($(e.target).closest('.sub-arrow').length) {
            e.preventDefault();
        }
    });

    // Copy Link button
    $(document).on('click', '.oup-copy-link', function (e) {
        e.preventDefault();

        var $btn = $(this);
        if ($btn.data('copying')) return;
        $btn.data('copying', true);

        var url = window.location.origin + window.location.pathname;
        var $label = $btn.find('.elementor-button-text');
        var originalText = $label.length ? $label.text() : $btn.text();

        function fallbackCopy() {
            var $temp = $('<input>').val(url).appendTo('body');
            $temp.select();
            document.execCommand('copy');
            $temp.remove();
        }

        function doSuccess() {
            $btn.addClass('oup-copy-link--copied');
            if ($label.length) $label.text('Copied!');

            setTimeout(function () {
                $btn.removeClass('oup-copy-link--copied');
                if ($label.length) $label.text(originalText);
                $btn.data('copying', false);
            }, 2500);
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(doSuccess).catch(function () {
                fallbackCopy();
                doSuccess();
            });
        } else {
            fallbackCopy();
            doSuccess();
        }
    });

    // Course Accordion Shortcode Toggle
    $(document).on('click', '.oup-course-accordion-container .course-accordion-header', function (e) {
        e.preventDefault();
        var $header = $(this);
        var $wrapper = $header.closest('.oup-course-accordion-container');
        var $content = $header.next('.course-accordion-content');
        var maxItems = $wrapper.data('max-items') || 'one';
        var animDuration = parseInt($wrapper.data('anim-duration'), 10) || 400;
        var isActive = $header.hasClass('active');

        if (maxItems === 'one' && !isActive) {
            $wrapper.find('.course-accordion-header').not($header).removeClass('active').attr('aria-expanded', 'false');
            $wrapper.find('.course-accordion-content').not($content).slideUp(animDuration);
        }

        if (isActive) {
            $header.removeClass('active').attr('aria-expanded', 'false');
            $content.slideUp(animDuration);
        } else {
            $header.addClass('active').attr('aria-expanded', 'true');
            $content.slideDown(animDuration);
        }
    });

    function doSearchRedirect($input) {
        var query = $.trim($input.val());
        var base = window.location.origin + '/';
        if (!query) {
            window.location.href = base + '?s=';
            return;
        }
        window.location.href = base + '?s=' + encodeURIComponent(query);
    }

    // Submit button click
    $(document).on('click', '.e-search-submit', function (e) {
        e.preventDefault();
        var $input = $(this).closest('.e-search-form').find('input[type="search"]');
        doSearchRedirect($input);
    });

    // Enter key press  
    $(document).on('keydown', '.e-search-form input[type="search"]', function (e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            doSearchRedirect($(this));
        }
    });

    // Scroll to section
    $('.post-type-archive-product a[href^="#"]').on('click', function (e) {
        const targetId = this.hash || $(this).attr('href');
        // Ignore empty anchors
        if (!targetId || targetId === '#') return;

        const target = document.querySelector(targetId);
        if (!target) return;

        e.preventDefault();

        const header = document.querySelector('header.elementor-location-header');
        const headerHeight = header ? header.offsetHeight : 0;
        const offset = headerHeight + 30;
        const targetPosition =
            target.getBoundingClientRect().top +
            window.scrollY -
            offset;

        window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
        });

    });

    // Add qty minus and plus buttons
    $('.post-type-archive-product .product .quantity').each(function () {
        $(this).prepend('<button type="button" class="qty-minus">−</button>');
        $(this).append('<button type="button" class="qty-plus">+</button>');
    });

    // Qty plus button
    $(document).on('click', '.product .qty-plus', function () {
        const $input = $(this).siblings('input[type="number"]');
        let value = parseInt($input.val()) || 0;
        const max = parseInt($input.attr('max'));

        if (!max || value < max) {
            $input.val(value + 1).trigger('change');
        }
    });

    // Qty minus button
    $(document).on('click', '.product .qty-minus', function () {
        const $input = $(this).siblings('input[type="number"]');
        let value = parseInt($input.val()) || 0;
        const min = parseInt($input.attr('min')) || 1;

        if (value > min) {
            $input.val(value - 1).trigger('change');
        }
    });

})(jQuery);
