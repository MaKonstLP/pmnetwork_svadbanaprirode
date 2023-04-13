'use strict';
import Filter from './filter';
import YaMapAll from './map';

export default class Index{
	constructor($block){
		var self = this;
		this.block = $block;
		this.filter = new Filter($('[data-filter-wrapper]'));
		this.yaMap = new YaMapAll(this.filter);

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
			self.redirectToListing();
		});
	}

	redirectToListing(){
		this.filter.filterMainSubmit();
		this.filter.promise.then(
			response => {
				ym(64598434,'reachGoal','filter');
				gtag('event', 'filter');
				window.location.href = response;
			}
		);
	}
}