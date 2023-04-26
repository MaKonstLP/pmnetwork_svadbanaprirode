'use strict';
import Swiper from 'swiper';

export default class ItemSwiper {

    static initSwiperListingGallery($container) {

        $container.each(function () {
            let swiper = new Swiper($(this), {
                // loop: true,
                // lazy: true,
                // preloadImages: false,
                // lazy: {
                //     loadPrevNext: true,
                // },
                pagination: {
                    el: ".swiper-pagination",
                    type: 'bullets'
                },
            });

            $(this).find('.swiper-slide-nav').on('mousemove', function () {
                swiper.slideTo($(this).data('swiper-change'), 0);
            })

            $(this).find('.swiper-slide-nav').on('mouseleave', function () {
                swiper.slideTo(0, 0);
            })
        })
    }
}