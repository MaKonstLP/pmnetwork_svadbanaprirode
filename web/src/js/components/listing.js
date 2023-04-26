'use strict';
import Filter from './filter';
import YaMapAll from './map';
import Swiper from 'swiper';
import ItemSwiper from './item_swiper';

export default class Listing{
	constructor($block){
		var self = this;
		this.block = $block;
		this.filter = new Filter($('[data-filter-wrapper]'));
		this.yaMap = new YaMapAll(this.filter);

		// $('.help_with_choose').click(function () {
		// 	$('.popup_form .form_block').attr('data-type', 'main');
		// });

		$('.bottom_call').on('click', function() {
			ym(64598434,'reachGoal','zabronirovat_listing');
			gtag('event', 'zabronirovat_listing');
		})

		$('.bottom_call_mobile').on('click', function() {
			if ($(this).data('commission')) {
				ym(64598434, 'reachGoal', 'pozvonit_listing');
				gtag('event', 'pozvonit_listing');
			}
		})

		$('.add_favorites').on('click', function() {
			ym(64598434,'reachGoal','izbrannoe_listing');
			gtag('event', 'izbrannoe_listing');
		})

		$('.help_with_choose').on('click', function() {
			ym(64598434,'reachGoal','pomoch_float')
			gtag('event', 'pomoch_float');
		})

		//КЛИК ПО КНОПКЕ "ПОДОБРАТЬ"
		$('[data-filter-button]').on('click', function(){
			self.reloadListing();
		});

		//КЛИК ПО ПАГИНАЦИИ
		$('body').on('click', '[data-pagination-wrapper] [data-listing-pagitem]', function(){
			self.reloadListing($(this).data('page-id'));
		});

		console.log(this);
	}

	reloadListing(page = 1){
		let self = this;
		self.filter.filterClose();
		self.block.addClass('_loading');
		self.filter.filterListingSubmit(page);
		self.filter.promise.then(
			response => {
				ym(64598434,'reachGoal','filter');
				gtag('event', 'filter');
				//console.log(response);
				$('[data-listing-list]').html(response.listing);
				$('[data-listing-title]').html(response.title);
				$('[data-listing-text-top]').html(response.text_top);
				$('[data-listing-text-bottom]').html(response.text_bottom);
				$('[data-pagination-wrapper]').html(response.pagination);
				document.title = response.seo_title;
				// ItemSwiper.initSwiperListingGallery($('[data-item-swiper]'));
				self.block.removeClass('_loading');
				$('html,body').animate({scrollTop:0}, 400);
				history.pushState({}, '', '/'+this.filter.subdomen.data('alias')+'catalog/'+response.url);
				this.yaMap.refresh(this.filter);
			}
		);
	}
}