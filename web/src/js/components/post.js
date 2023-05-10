'use strict';
import Swiper from 'swiper';

export default class Main{
	constructor(){
		var self = this;
		this.swipers_gal = new Array();
		this.swipers_rest = new Array();

		$('[data-action="show_phone"]').on('click', function () {
			$('.object_book_hidden').addClass('_active');
		});

		$('.object_gallery').on('click', '[data-gallery-view]', function() {
			$('body').toggleClass('_overflow');
			let slider = $(this).closest('[data-gallery-main]');
			let active = slider.find('.swiper-slide-active').attr('data-index');
			let slider_popup = $('[data-type="popup_img"]');

			slider_popup.find('.swiper-wrapper').html('');
			slider.find('.swiper-slide').not('.swiper-slide-duplicate').each(function () {
				let src = $(this).find('img').attr('data-img');
				slider_popup.find('.swiper-wrapper').append('<div class="swiper-slide"><div class="swiper-zoom-container"><img loading="lazy" src="' + src + '"></div></div>');
			});

			slider_popup.addClass('_active');
			self.initSwiperPopup(slider_popup.find('[data-popup-swiper]'), active);
		})

	    $('.object_gallery').each(function(iter,object) {

			var galleryThumbs = new Swiper($(this).find('.item_thumb_slider'), {
				spaceBetween: 5,
				slidesPerView: 5,
				// freeMode: true,
				// watchSlidesVisibility: true,
				// watchSlidesProgress: true,
				loop: false,
				watchSlidesProgress: true,
				normalizeSlideIndex: false,
				breakpoints: {
					767: {
						slidesPerView: 'auto',
						spaceBetween: 5
					}
				}
			});

			var galleryTop = new Swiper($(this).find('.item_top_slider'), {
				spaceBetween: 0,
				slidesPerView: 1,
				navigation: {
					nextEl: '.swiper-button-next',
					prevEl: '.swiper-button-prev',
				},
				thumbs: {
					swiper: galleryThumbs
				},
				on: {
					init: function () {
						this.thumbs.swiper.slideTo(this.activeIndex);
						$('.object_thumb.swiper-slide-active').attr('data-thumb-active', 1);
					},
					slideChange: function () {
						this.thumbs.swiper.slideTo(this.activeIndex);
						$('[data-thumb-active="1"]').attr('data-thumb-active', 0);
						$('.object_thumb.swiper-slide-active').attr('data-thumb-active', 1);
					},
				},
			});
		});
	}

	// ПРОСМОТР ИЗОБРАЖЕНИЙ
	initSwiperPopup($container, $start) {
		let swiper_popup = new Swiper($container, {
			slidesPerView: 1,
			spaceBetween: 10,
			grabCursor: true,
			zoom: true,
			zoom: {
				maxRatio: 5,
				minRation: 1
			},
			zoomedSlideClass: 'swiper-slide',
			// speed: 0,
			initialSlide: $start,
			on: {
				slideChange: function() {
					// если нет, то отключаем кнопку навигации вправо
					if (swiper_popup.isEnd)
						$container.find('.popup-img-button-next').disabled = true;
					else
						$container.find('.popup-img-button-next').disabled = false;

					// проверяем, есть ли еще слайды для листания влево
					if (swiper_popup.isBeginning)
						$container.find('.popup-img-button-prev').disabled = true;
					else
						$container.find('.popup-img-button-prev').disabled = false;
				}
			},
			navigation: {
				nextEl: '.popup-img-button-next',
				prevEl: '.popup-img-button-prev',
			}
		});
	}
}