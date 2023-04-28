'use strict';
import Swiper from 'swiper';
// import 'slick-carousel';
// import * as Lightbox from '../../../node_modules/lightbox2/dist/js/lightbox.js';

export default class Item {
	constructor($item) {
		var self = this;
		this.subdomen = $('[data-city-block]');
		self.mobileMode = self.getScrollWidth() < 768 ? true : false;

		var fired = false;

		window.addEventListener('click', () => {
			if (fired === false) {
				fired = true;
				load_other();
			}
		}, {passive: true});

		window.addEventListener('scroll', () => {
			if (fired === false) {
				fired = true;
				load_other();
			}
		}, {passive: true});

		window.addEventListener('mousemove', () => {
			if (fired === false) {
				fired = true;
				load_other();
			}
		}, {passive: true});

		window.addEventListener('touchmove', () => {
			if (fired === false) {
				fired = true;
				load_other();
			}
		}, {passive: true});

		function load_other() {
			setTimeout(function () {
				self.init();
			}, 100);
		}

		//НАВИГАЦИОННАЯ ПАНЕЛЬ
		if ($(window).width()<= 767) {
			$('.navigation').addClass('hidden');
			window.addEventListener('scroll', () => {
				if ($(window).scrollTop() > 100) {
					$('.navigation').removeClass('hidden');
					$('.navigation').addClass('navigation_mobile');
					$('.navigation').show();
				} else {
					$('.navigation_wrap').scrollLeft(0);
					$('.navigation').addClass('hidden');
					$('.navigation').removeClass('navigation_mobile');
					$('.navigation').hide();
				}
			});
		}

		//ДЛЯ МОБИЛОК ОТОБРАЖАЕМ КАРТУ В ДРУГОМ МЕСТЕ
		if (self.mobileMode) {
			// let map = $('.object_map_desktop');
			// $('.object_map_desktop').remove();
			// $('.object_map_mobile').show();
			// $('.object_map_mobile').append(map);

			var block1 = $('.object_map_mobile');
			var block2 = $('.object_map_desktop');
			block1.replaceWith(block2);
		}

		$('[data-action="show_phone"]').on('click', function () {
			$('.object_book_hidden').addClass('_active');
			ym(64598434, 'reachGoal', 'show_phone');
			gtag('event', 'show_phone');
			if ($(this).data('commission')) {
				ym(64598434, 'reachGoal', 'pozvonit_listing');
				gtag('event', 'pozvonit_listing');
				
				// ==== Gorko-calltracking ====
				let phone = $(this).closest('.object_book_hidden').find('.object_real_phone').text();
				self.sendCalltracking(phone);
			}
		});

		$('.add_favorites').on('click', function () {
			ym(64598434, 'reachGoal', 'izbrannoe_zal');
			gtag('event', 'izbrannoe_zal');
		})

		$('.bottom_call_mobile').on('click', function () {
			if ($(this).data('commission')) {
				ym(64598434, 'reachGoal', 'pozvonit_zal');
				gtag('event', 'pozvonit_zal');
			}
		})

		$('.info-bottom__desc span').on('click', function () {
			ym(64598434, 'reachGoal', 'rashet');
			gtag('event', 'rashet');
		})

		$('.popup_img').on('click', '.swiper-slide', function(event) {
			console.log('Клик на блок');
			$('.popup_wrap._active').removeClass('_active');
			$('body').removeClass('_overflow');
			event.stopPropagation();
		})

		$('.popup_img').on('click', '.swiper-slide img', function(event) {
			console.log('Клик на img');
			event.stopPropagation();
		})

		$('[data-gallery-view]').on('click', function() {
			$('body').toggleClass('_overflow');
			let slider = $(this).closest('[data-gallery-main]');
			let active = slider.find('.swiper-slide-active').attr('data-index');
			let slider_popup = $('[data-type="popup_img"]');

			slider.find('.swiper-slide').not('.swiper-slide-duplicate').each(function () {
				let src = $(this).find('img').attr('data-img');
				slider_popup.find('.swiper-wrapper').append('<div class="swiper-slide"><img loading="lazy" src="' + src + '"></div');
			});

			slider_popup.addClass('_active');
			self.initSwiperPopup(slider_popup.find('[data-popup-swiper]'), active);
		})

		var galleryThumbs = new Swiper('.item_thumb_slider', {
			spaceBetween: 5,
			slidesPerView: 5,
			// freeMode: true,
			// watchSlidesVisibility: true,
			// watchSlidesProgress: true,
			loop: false,
			watchSlidesProgress: true,
			normalizeSlideIndex: false,
			mouseWheel: true,
			breakpoints: {
				767: {
					slidesPerView: 'auto',
					spaceBetween: 5
				}
			}
		});

		var galleryTop = new Swiper('.item_top_slider', {
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

					console.log('this.activeIndex', this.activeIndex);
					console.log('this.thumbs.swiper', this.thumbs.swiper);
				},
			},
		});

		$('.item_thumb_slider').find('.object_thumb').on('click', function () {
			galleryTop.slideTo($(this).data('thumb-number'), 0);
		})
	}

	init() {
		let self = this;

		$('[data-title-address]').on('click', function () {
			let map_offset_top = $('#map').offset().top;
			let map_height = $('#map').height();
			let header_height = $('header').height();
			let window_height = $(window).height();
			let scroll_length = map_offset_top - header_height - ((window_height - header_height) / 2) + map_height / 2;
			$('html,body').animate({ scrollTop: scroll_length }, 400);
		});

		const nav_panel_observer = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					let scrollLeft = 0;
					let animateTime = 200;
					let attr = $(entry.target).data('nav-label');

					$('.navigation_item._active_nav_item').removeClass('_active_nav_item');
					$('[data-item-navigation="'+ attr +'"]').addClass('_active_nav_item');

					$('.navigation_item').each(function () {
						if ($(this).hasClass('_active_nav_item'))
							return false;

						scrollLeft += $(this).width() + 40;
					})

					// $('.navigation_wrap').scrollLeft(scrollLeft);
					$('.navigation_wrap').animate({ scrollLeft: scrollLeft }, animateTime);
				}else {
					// console.log($(entry.target).data('nav-label') + ' HIDE');
				}
			});
		}, {
			rootMargin: '-46px 0px 0px 0px',
			treshold: 0.7
		});

		const bottom_btns_observer = new IntersectionObserver(entries => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					$('.item_mobile_btns').addClass('hidden');
					$('.footer_wrap').css('margin-bottom', '0px');
				} else {
					$('.item_mobile_btns').removeClass('hidden');
					if (self.mobileMode) {
						$('.footer_wrap').css('margin-bottom', '80px');
					}
				}
			});
		}, {
			rootMargin: '-46px 0px 0px 0px',
			threshold: 0.5
		});

		//ПЕРЕКЛЮЧАЛКА ПУНКТОВ В НАВИГАЦИОННОЙ ПАНЕЛИ ПРИ СКРОЛЛЕ
		document.querySelectorAll('.nav-label').forEach(
			(section) => nav_panel_observer.observe(section),
		);

		//СКРЫТЬ/ПОКАЗАТЬ КНОПКИ СНИЗУ НА МОБИЛКЕ
		// const bottom_btns_el = document.querySelector('.object_book .item_info_bottom');
		const bottom_btns_el = document.querySelector('.item_info_bottom');
		bottom_btns_observer.observe(bottom_btns_el);

		$('[data-book-button]').on('click', function () {
			let form = $('[data-type="item"]').closest('.form_wrapper');
			let form_offset_top = form.offset().top;
			let header_height = $('header').height();
			let scroll_length = form_offset_top - header_height - 50;
			$('html,body').animate({ scrollTop: scroll_length }, 400);
			ym(64598434, 'reachGoal', 'scroll_form');
			gtag('event', 'scroll_form');
		});

		$('[data-book-open]').on('click', function () {
			$(this).closest('.object_book_email').addClass('_form');
		})

		$('[data-img-link]').on('click', function (event) {
			event.preventDefault();
		})

		$('[data-book-email-reload]').on('click', function () {
			$(this).closest('.object_book_email').removeClass('_success');
			$(this).closest('.object_book_email').addClass('_form');
		})

		// $('.item_thumb_slider').find('.object_thumb').on('mousemove', function () {
		// 	galleryTop.slideTo($(this).data('thumb-number'), 0);
		// })
	}

	// ПРОСМОТР ИЗОБРАЖЕНИЙ
	initSwiperPopup($container, $start) {
		let swiper_popup = new Swiper($container, {
			slidesPerView: 1,
			spaceBetween: 10,
			grabCursor: true,
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
			},
			breakpoints: {
				767: {
					zoom: true
				}
			}
		});
	}

	getScrollWidth() {
		return Math.max(
			document.body.scrollWidth, document.documentElement.scrollWidth,
			document.body.offsetWidth, document.documentElement.offsetWidth,
			document.body.clientWidth, document.documentElement.clientWidth
		);
	};

	sendCalltracking(phone) {
		let clientId = '';
		ga.getAll().forEach((tracker) => {
			clientId = tracker.get('clientId');
		})

		const data = new FormData();

		if (this.mobileMode) {
			data.append('isMobile', 1);
		}
		data.append('phone', phone);
		data.append('clientId', clientId);

		$.ajax({
			type: 'post',
			url: '/'+this.subdomen.data('alias')+'ajax/send-calltracking/',
			data: data,
			processData: false,
			contentType: false,
			success: function (response) {
				// response = $.parseJSON(response);
				// response = JSON.parse(response);
				// self.resolve(response);
				console.log('calltracking sent');
			},
			error: function (response) {
				console.log('calltracking ERROR');
			}
		});
	}
}