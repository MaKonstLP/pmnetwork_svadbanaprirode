'use strict';
import Swiper from 'swiper';

export default class Widget {
	constructor() {
		let self = this;
		this.swiperArr = [];

		// let favorites_widget = $('.favorites-empty:not(.hidden)').find('.error_widget').length;
		// console.log(favorites_widget);

		if ($(window).width() <= 1650) {
			$('[data-widget-wrapper]').each(function() {
				self.initSwiper($(this).find('[data-listing-wrapper]'));
			});
		} else {
			$('[data-item-widget]').find('[data-widget-wrapper]').each(function() {
				self.initSwiper($(this).find('[data-listing-wrapper]'));
			});
		}

		$(window).on('resize', function() {
			console.log(self.swiperArr.length);
			if($(window).width() <= 1650) {
				if(self.swiperArr.length == 0) {
					$('[data-widget-wrapper]').each(function() {
						self.initSwiper($(this).find('[data-listing-wrapper]'));
					});
				}
			} else {
				$.each(self.swiperArr, function() {
					this.destroy(true, true);
				});
				self.swiperArr = [];
			}
		});
	}

	initSwiper($container) {
		let swiper = new Swiper($container, {
	        slidesPerView: 4,
	        spaceBetween: 30,
			observer: true,
			observeParents: true,
			centerInsufficientSlides: true,
			watchOverflow: true,
	        navigation: {
              nextEl: '.listing_widget_arrow._next',
              prevEl: '.listing_widget_arrow._prev',
            },
            pagination: {
              el: '.listing_widget_pagination',
              type: 'bullets',
            },
	        breakpoints: {
	        	1200:{
	        		slidesPerView: 3,
					spaceBetween: 30,
	        	},
	        	768:{
	        		slidesPerView: 2,
					spaceBetween: 20,
	        		// navigation: false,
	        	},
				540:{
					slidesPerView: 1.45,
					spaceBetween: 10,
					// navigation: false,
				},
				360:{
					slidesPerView: 1.2,
					spaceBetween: 10,
				}
	        }
	    });

	    let swiper_var = $container.swiper;
		this.swiperArr.push(swiper);
	}
}