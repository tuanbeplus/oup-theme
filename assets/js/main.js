/**
 * Main — JS
 *
 * @package Onwards-Upwards-Psychology-Theme
 */

// Widget imports
require('./widgets/sample-widget');

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
        const btnATCajax = $('.add_to_cart_button[data-product_id='+productId+']');
        const textBtn = currentBtn.find('.elementor-button-text').length ? currentBtn.find('.elementor-button-text') : currentBtn;

        currentBtn.addClass('loading');
        btnATCajax.click();

        // Track if the btnATCajax has added class "added"
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class' && btnATCajax.hasClass('added')) {
                    currentBtn.removeClass('loading');
                    textBtn.text('Added');
                    
                    // Change text back after 3 seconds
                    setTimeout(function() {
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

})(jQuery);