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

    // Icon facebook share
    $(document).on('click', '.oup-share-facebook', function (e) {
        e.preventDefault();

        var shareUrl =
            window.location.origin +
            window.location.pathname;

        window.open(
            'https://www.facebook.com/sharer/sharer.php?u=' +
            encodeURIComponent(shareUrl),
            '_blank',
            'width=600,height=500'
        );
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

    // Product cards zoom in on scroll
    function initProductZoomIn() {
        const products = document.querySelectorAll('.zoom-products li.product');
        if (!products.length) return;
        const delay = performance.now() < 1000 ? 500 : 0;
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(({ target, isIntersecting }) => {
                if (!isIntersecting) return;
                setTimeout(() => target.classList.add('zoom-in'), delay);
                observer.unobserve(target);
            });
        }, { threshold: 0 });
        products.forEach(product => observer.observe(product));
    }

    // Split text into words/spans for split-text-reveal animation
    function initSplitTextReveal() {
        if (document.body.classList.contains('elementor-editor-active')) return;

        const splitTextElements = document.querySelectorAll('.split-text-reveal');
        if (!splitTextElements.length) return;

        splitTextElements.forEach(el => {
            let targetEl = el.querySelector('.elementor-heading-title, h1, h2, h3, h4, h5, h6, p, .elementor-widget-container');
            if (!targetEl) targetEl = el;

            let wordIndex = 0;

            function splitTextNodes(node) {
                const childNodes = Array.from(node.childNodes);

                childNodes.forEach(child => {
                    if (child.nodeType === Node.TEXT_NODE) {
                        const text = child.nodeValue;
                        if (!text.trim()) return;

                        // Split by whitespace but keep spaces
                        const words = text.split(/(\s+)/);
                        const fragment = document.createDocumentFragment();

                        words.forEach(word => {
                            if (word.trim() === '') {
                                fragment.appendChild(document.createTextNode(word));
                            } else {
                                const mask = document.createElement('span');
                                mask.className = 'split-word-mask';

                                const wordSpan = document.createElement('span');
                                wordSpan.className = 'split-word';
                                wordSpan.textContent = word;
                                wordSpan.style.transitionDelay = `${wordIndex * 0.05}s`;
                                wordIndex++;

                                mask.appendChild(wordSpan);
                                fragment.appendChild(mask);
                            }
                        });

                        child.parentNode.replaceChild(fragment, child);
                    } else if (child.nodeType === Node.ELEMENT_NODE) {
                        if (!child.classList.contains('split-word-mask')) {
                            splitTextNodes(child);
                        }
                    }
                });
            }

            splitTextNodes(targetEl);
        });
    }

    // Reveal animations on scroll
    function initHeroReveal() {
        const revealElements = document.querySelectorAll('.hero-reveal');
        if (!revealElements.length) return;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(({ target, isIntersecting }) => {
                if (!isIntersecting) return;
                target.classList.add('is-revealed');
                observer.unobserve(target);
            });
        }, { threshold: 0.1 });

        revealElements.forEach(el => observer.observe(el));
    }

    $(function () {
        initProductZoomIn();
        initSplitTextReveal();
        initHeroReveal();
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
    $('.post-type-archive-product .product .quantity, .archive.tax-product_cat .product .quantity, .single-product .product .quantity').each(function () {
        $(this).prepend('<button type="button" class="qty-minus">-</button>');
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

    // Handle add to cart on single product page
    $('.single-product form.cart').on('submit', function (e) {
        e.preventDefault();
        const $form = $(this);
        const $button = $form.find('.single_add_to_cart_button');

        if ($button.hasClass('loading') || $button.hasClass('disabled')) {
            return;
        }

        $button.addClass('loading');

        let formData = $form.serializeArray();
        let hasAddToCart = false;

        $.each(formData, function (i, field) {
            if (field.name === 'add-to-cart') {
                hasAddToCart = true;
            }
        });

        if (!hasAddToCart && $button.val()) {
            formData.push({ name: 'add-to-cart', value: $button.val() });
        }

        $.ajax({
            type: 'POST',
            url: window.location.toString(),
            data: $.param(formData),
            success: function (response) {
                const $html = $(response);
                const $error = $html.find('.woocommerce-error');

                if ($error.length > 0) {
                    $('.woocommerce-error').remove();
                    $form.before($error);
                } else {
                    $('.woocommerce-error').remove();

                    if ($button.parent().find('.added_to_cart').length === 0) {
                        let cartUrl = '/cart/';
                        let cartText = 'View cart';
                        if (typeof wc_add_to_cart_params !== 'undefined') {
                            cartUrl = wc_add_to_cart_params.cart_url;
                            cartText = wc_add_to_cart_params.i18n_view_cart;
                        }
                        $button.after(' <a href="' + cartUrl + '" class="added_to_cart wc-forward" title="' + cartText + '">' + cartText + '</a>');
                    }

                    // Trigger WooCommerce fragment refresh to update the cart
                    $(document.body).trigger('wc_fragment_refresh');
                    $(document.body).trigger('added_to_cart', [
                        null,
                        null,
                        $button
                    ]);
                }
            },
            complete: function () {
                $button.removeClass('loading');
            }
        });
    });

    // Handle add to cart added success on single product page
    $(document.body).on('added_to_cart', function () {
        const $cart = $('header .elementor-menu-cart__toggle_button');
        $cart.addClass('cart-added');
        setTimeout(function () {
            $cart.removeClass('cart-added');
        }, 800);
    });

})(jQuery);
