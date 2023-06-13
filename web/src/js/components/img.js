'use strict';

export default class Img {
    constructor() {
        let self = this;
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

        window.addEventListener('touchstart', () => {
            if (fired === false) {
                fired = true;
                load_other();
            }
        }, {passive: true});

        function load_other() {
            self.constructor.imgInit();
        }

    }

    // ОТЛОЖЕННАЯ ЗАГРУЗКА КАРТИНОК
    static imgInit(){
        let images = $('img._lazy');
        if(images.length > 0){
            images.each(function(){
                let img = $(this);
                img.attr('src', img.data('src') );
                img.on('load', function() {
                    img.attr('alt', img.data('alt') );
                    $(this).removeAttr('data-alt');
                    $(this).removeAttr('data-src');
                    $(this).removeClass('_lazy');
                });
            });
        }
    }

}