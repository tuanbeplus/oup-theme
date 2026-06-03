(function ($) {
    'use strict';

    function initSceSwipers(scope) {
        var $scope = scope ? $(scope) : $(document);

        $scope.find('.sce-swiper[data-swiper]').each(function () {
            var el = this;

            if (el.sceSwiper) return;

            var config = {};
            try {
                config = JSON.parse(el.dataset.swiper);
            } catch (e) {
                return;
            }

            if (config.navigation) {
                config.navigation = {
                    nextEl: el.querySelector('.swiper-button-next'),
                    prevEl: el.querySelector('.swiper-button-prev'),
                };
            }

            if (config.pagination) {
                config.pagination = {
                    el: el.querySelector('.swiper-pagination'),
                    clickable: true,
                };
            }

            if (typeof elementorFrontend !== 'undefined' && elementorFrontend.utils && elementorFrontend.utils.swiper) {
                new elementorFrontend.utils.swiper(el, config).then(function (instance) {
                    el.sceSwiper = instance;
                    if (config.autoplay && instance.autoplay) {
                        instance.autoplay.start();
                    }
                });
            } else if (typeof Swiper !== 'undefined') {
                el.sceSwiper = new Swiper(el, config);
            }
        });
    }

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/sugar-calendar-event.default', function ($scope) {
            initSceSwipers($scope[0]);
        });
    });

    $(document).ready(function () {
        if (typeof elementorFrontend === 'undefined') {
            initSceSwipers();
        }
    });

})(jQuery);