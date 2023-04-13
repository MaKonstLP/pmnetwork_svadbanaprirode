'use strict';

export default class Filter{
	constructor($filter){
		let self = this;
		this.subdomen = $('[data-city-block]');
		this.$filter = $filter;
		this.state = {};

		this.init(this.$filter);

		//КЛИК ПО БЛОКУ С СЕЛЕКТОМ
		this.$filter.find('[data-filter-select-current]').on('click', function(){
			let $parent = $(this).closest('[data-filter-select-block]');
			self.selectBlockClick($parent);
		});

		//КЛИК ПО СТРОКЕ В СЕЛЕКТЕ
		this.$filter.find('[data-filter-select-item]').on('click', function(){
			$(this).toggleClass('_active');
			// self
			self.selectStateRefresh($(this).closest('[data-filter-select-block]'));
		});

		//КЛИК ПО ЧЕКБОКСУ
		this.$filter.find('[data-filter-checkbox-item]').on('click', function(){
			$(this).toggleClass('_checked');
			self.checkboxStateRefresh($(this));
		});

		//ОТКРЫТЬ МОБИЛЬНЫЙ ФИЛЬТР
		$('body').find('[data-filter-open]').on('click', (e) => {
			this.$filter.addClass('_active');
		});

		//
		if (this.subdomen.data('id') == '4400'){
			let msk = this.$filter.find('[data-type="msk"]');
			msk.addClass('_checked');
			self.checkboxStateRefresh(msk);
		}

		//ЗАКРЫТЬ МОБИЛЬНЫЙ ФИЛЬТР
		this.$filter.find('[data-filter-close]').on('click', (e) => {
			this.$filter.removeClass('_active');
		});
		this.$filter.on('click', function(e){
			if(!$(e.target).hasClass('filter_mobile_button') && !$(e.target).hasClass('filter_wrapper') && !$(e.target).closest('.filter_wrapper').length)
				if(self.$filter.hasClass('_active'))
					self.filterClose();
		});

		//КЛИК ВНЕ БЛОКА С СЕЛЕКТОМ
		$('body').click(function(e) {
			if (!$(e.target).closest('.filter_select_block').length){
				self.selectBlockActiveClose();
			}
		});

		var observer = new IntersectionObserver(function(entries) {
			console.log(entries[0].intersectionRatio);
			if (entries[0].intersectionRatio === 0)
				document.querySelector(".filter_mobile_wrapper").classList.add("_sticky");
			else if (entries[0].intersectionRatio === 1)
				document.querySelector(".filter_mobile_wrapper").classList.remove("_sticky");
		}, {
			threshold: [0, 1]
		});

		if (document.querySelector(".filter_mobile_flag"))
			observer.observe(document.querySelector(".filter_mobile_flag"));
	}

	init(){
		let self = this;

		this.$filter.find('[data-filter-select-block]').each(function(){
			self.selectStateRefresh($(this));
		});

		this.$filter.find('[data-filter-checkbox-item]').each(function(){
			self.checkboxStateRefresh($(this));
		});
	}

	filterClose(){
		this.$filter.removeClass('_active');
	}

	filterListingSubmit(page = 1){
		let self = this;
		self.state.page = page;

		let data = {
			'filter' : JSON.stringify(self.state)
		}

		this.promise = new Promise(function(resolve, reject) {
			self.reject = reject;
			self.resolve = resolve;
		});

		$.ajax({
			type: 'get',
			url: '/'+self.subdomen.data('alias')+'ajax/filter/',
			data: data,
			success: function(response) {
				response = $.parseJSON(response);
				self.resolve(response);
			},
			error: function(response) {

			}
		});
	}

	filterMainSubmit(){
		let self = this;
		let data = {
			'filter' : JSON.stringify(self.state)
		}

		this.promise = new Promise(function(resolve, reject) {
			self.reject = reject;
			self.resolve = resolve;
		});

		$.ajax({
			type: 'get',
			url: '/'+self.subdomen.data('alias')+'ajax/filter-main/',
			data: data,
			success: function(response) {
				if(response){
					// console.log('response', response);
					self.resolve('/'+self.subdomen.data('alias')+'catalog/'+response);
				}
				else{
					// console.log('response', response);
					self.resolve(self.filterListingHref());
				}
			},
			error: function(response) {

			}
		});
	}

	selectBlockClick($block){
		if($block.hasClass('_active')){
			this.selectBlockClose($block);
		}
		else{
			this.selectBlockOpen($block);
		}
	}

	selectBlockClose($block){
		$block.removeClass('_active');
	}

	selectBlockOpen($block){
		this.selectBlockActiveClose();
		$block.addClass('_active');
	}

	selectBlockActiveClose(){
		this.$filter.find('[data-filter-select-block]._active').each(function(){
			$(this).removeClass('_active');
		});
	}

	selectStateRefresh($block){
		let self = this;
		let blockType = $block.data('type');
		let $items = $block.find('[data-filter-select-item]._active');
		let selectText = '-';

		if($items.length > 0){
			self.state[blockType] = '';
			$items.each(function(){
				if(self.state[blockType] !== ''){
					self.state[blockType] += ','+$(this).data('value');
					selectText = 'Выбрано ('+$items.length+')';
				}
				else{
					self.state[blockType] = $(this).data('value');
					selectText = $(this).text();
				}
			});
		}
		else{
			delete self.state[blockType];
		}

		$block.find('[data-filter-select-current] p').text(selectText);
	}

	checkboxStateRefresh($item){
		let blockType = $item.closest('[data-type]').data('type');
		if($item.hasClass('_checked')){
			this.state[blockType] = 1;
		}
		else{
			delete this.state[blockType];
		}
	}

	filterListingHref(){
		if(Object.keys(this.state).length > 0){
			var href ='/'+ this.subdomen.data('alias')+'catalog/?';
			$.each(this.state, function(key, value){
				href += '&' + key + '=' + value;
			});
		}
		else {
			var href = '/'+this.subdomen.data('alias')+'catalog/';
		}

		return href;
	}
}