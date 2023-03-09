import $ from 'jquery';

import Listing from './components/listing';
import Item from './components/item';
import Main from './components/main';
import Index from './components/index';
import Widget from './components/widget';
import Form from './components/form';
import YaMap from './components/mapSingleObject';
import Errorpage from './components/error';
import Post from './components/post';

window.$ = $;

(function($) {
  	$(function() {

  		if ($('[data-page-type="listing"]').length > 0) {
	    	var listing = new Listing($('[data-page-type="listing"]'));
	    }

	    if ($('[data-page-type="item"]').length > 0) {
	    	var item = new Item($('[data-page-type="item"]'));
	    }

	    if ($('[data-page-type="index"]').length > 0) {
	    	var index = new Index($('[data-page-type="index"]'));
	    }

	    if ($('[data-widget-wrapper]').length > 0) {
	    	var widget = new Widget();
	    }

	    if ($('.map').length > 0) {
			if($('[data-page-type="item"]').length > 0) {
				var yaMap = new YaMap();
			}
		}

	    if ($('[data-page-type="error"]').length > 0) {
	    	var error = new Errorpage();
	    }

	    if ($('[data-page-type="post"]').length > 0) {
	    	var post = new Post();
	    }
	    

	    var main = new Main();
	    var form = [];

	    $('form').each(function(){
	    	form.push(new Form($(this)))
	    });

		//КЛИК ПО ВЫБОРУ ГОРОДА
		if ($(window).width()>= 1440) {
			$('.city_choose_select').on('click', function(e){
				$('.all_cities').toggleClass('hidden');
				$('.header_wrap_back').toggleClass('hidden');
			});

			$('.header_wrap_back').on('click', function(e){
				console.log('back');
				$('.all_cities').addClass('hidden');
				$('.header_wrap_back').addClass('hidden');
			});
		} else {
			$('.city_choose_select').on('click', function(e){
				$('.header_menu_mobile_under').toggleClass('hidden');
				$('body').toggleClass('overflow_hidden');
			});
			$('.under_cities_back').on('click', function(e) {
				$('.header_menu_mobile_under').toggleClass('hidden');
				$('body').toggleClass('overflow_hidden');
			});
		}

		window.addEventListener('scroll', () => {
			$('.all_cities').addClass('hidden');
			$('.header_wrap_back').addClass('hidden');
		});

		//КНОПКПА "ПОМОЧЬ С ВЫБОРОМ" на листинге
		if ($(window).width()<= 767) {
			window.addEventListener('scroll', () => {
				if ($(window).scrollTop() > 100) {
					$('.scroll_top_btn').show();
				} else {
					$('.scroll_top_btn').hide();
				}
			});

			$('.scroll_top_btn').click(function () {
				$('html, body').animate({scrollTop: 0}, 600); 
				return false;
			});
		}
	});
})($);