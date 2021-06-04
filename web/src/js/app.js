import $ from 'jquery';

import Listing from './components/listing';
import Item from './components/item';
import Main from './components/main';
import Index from './components/index';
import Widget from './components/widget';
import Form from './components/form';
import YaMap from './components/mapSingleObject';
import Errorpage from './components/error';

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
	    

	    var main = new Main();
	    var form = [];

	    $('form').each(function(){
	    	form.push(new Form($(this)))
	    });

  	});
})($);