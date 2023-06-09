'use strict';
import Cookies from 'js-cookie';
import Swiper from 'swiper';
import ItemSwiper from './item_swiper';
import Img from './img';

export default class Main {
    constructor() {
        let self = this;
        this.subdomen = $('[data-city-block]');
        let imgLoader = new Img();

        $('.bottom_call').click(function(e) {
            e.preventDefault();
        });

        // === записываем в куки данные для отправки Calltracking в БД горько START ===
        //запись в куки только внешнего реферера
        let pageReferrer = '';
        //проверяем что это внешний реферер, а не переход внутри страниц сайта
        // if (document.referrer.indexOf(window.location.origin) != -1) { //в этом случае поддомены (например samara.arendazala.net) тоже считаются внешним реферером
        if (document.referrer.indexOf('svadbanaprirode.com') == -1) { // отсекаем так же поддомена, как внешний реферер
            if (document.referrer) {
                pageReferrer = document.referrer;
            }
            if (Cookies.get('a_ref_0')) {
                Cookies.set('a_ref_1', pageReferrer, {expires: 365});
            } else {
                Cookies.set('a_ref_0', pageReferrer, {expires: 365});
            }
        }

        //запись в куки utm_details
        let currentUrl = '';
        if (window.location.href) {
            currentUrl = window.location.href;
        }
        let patternUtm = RegExp('utm_details=([^\&]*)', 'g');
        let utmExist = patternUtm.exec(currentUrl);
        let utm = {};
        if (utmExist) {
            let rows = utmExist[1].split('|');

            for (let i = 0; i < rows.length; i++) {
                let a = rows[i].split(':');
                utm[a[0]] = a[1];
            }
        }

        if (Object.keys(utm).length != 0) {
            let utmJson = JSON.stringify(utm);
            if (Cookies.get('a_utm_0') && Cookies.get('a_utm_0') != '{}') {
                Cookies.set('a_utm_1', utmJson, {expires: 365});
            } else {
                Cookies.set('a_utm_0', utmJson, {expires: 365});
            }
        }
        // === записываем в куки данные для отправки Calltracking в БД горько END ===

        $('body').on('click', '[data-seo-control]', function () {
            $(this).closest('[data-seo-text]').addClass('_active');
        });

        $('body').on('click', '[data-open-popup-form]', function () {
            let type = $(this).data('type');

            $('.popup_wrap[data-type="'+type+'"]').addClass('_active');
            $('body').toggleClass('_overflow');

            // if ($('[data-page-type="index"]') || $('[data-page-type="listing"]')) {
            //     $('.form_block').attr('id', 'form_mobile_listing');
            // } else if ($('[data-page-type="item"]')) {
            //     $('.form_block').attr('id', 'form_mobile_item');
            // }

            ym(64598434, 'reachGoal', 'header_button');
            gtag('event', 'header_button');
        });

        $('body').on('click', '[data-close-popup]', function () {
            $('.popup_wrap._active').removeClass('_active');
            $('body').removeClass('_overflow');
        });

        $('.header_burger').on('click', function () {
            $('.header_menu').toggleClass('_active');
            $('.header_burger').toggleClass('_active');
            $('header').toggleClass('_active');
            $('.header_menu_mobile_under').addClass('hidden');
            $('body').removeClass('overflow_hidden');
        });

        $(window).scroll(function () {
            $('.header_menu').removeClass('_active');
            $('.header_burger').removeClass('_active');
            $('header').removeClass('_active');
        })

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

        //СКРОЛЛ К ФОРМЕ НА СТРАНИЦЕ ЗАВЕДЕНИЯ
        $('[data-item-book]').on('click', function () {
            let form = $('.item-book-form');
            let form_offset_top = form.offset().top;
            let header_height = $('header').height();
            let scroll_length = form_offset_top - header_height - 50;
            $('html,body').animate({scrollTop: scroll_length}, 400);
            ym(64598434, 'reachGoal', 'scroll_form');
            gtag('event', 'scroll_form');
            console.log('scroll_form');
        });

        //НАВИГАЦИЯ ПО СТРАНИЦЕ ЗАВЕДЕНИЯ
        $('body').on('click', '[data-navigation]', function (e) {
            let attr = $(this).data('item-navigation');
            let block = $('[data-item="' + attr + '"]');

            $('[data-navigation]').removeClass('_active_nav_item');
            $(this).addClass('_active_nav_item');

            let block_offset_top = block.offset().top;
            let header_height = $('header').height();
            let scroll_length = block_offset_top - header_height;
            $('html,body').animate({scrollTop: scroll_length}, 400);
        })

        //ДОБАВЛЕНИЕ В ИЗБРАННОЕ
        $('body').on('click', '.add_favorites', function () {
            $.ajax({
                type: 'get',
                url: '/' + self.subdomen.data('alias') + 'ajax/favorites/',
                data: {'room_id': $(this).data('room-id'), 'type': !$(this).hasClass('_active') ? 'add' : 'del'},
                success: function (response) {
                    let res = JSON.parse(response);
                    $('.header_favorites_link').attr('data-count', res.count);
                },
                error: function (response) {
                }
            });

            if ($('[data-page-type="item"]').length > 0 && $(this).hasClass('add_favorites_book')) {
                $('.add_favorites_book').toggleClass('_active');
            } else {
                $(this).toggleClass('_active');
            }

            $(this).find('.add_favorite').toggleClass('hidden');
            $(this).find('.in_favorite').toggleClass('hidden');
        });




        //КЛИК ПО ВЫБОРУ ГОРОДА
        if ($(window).width()>= 1440) {
            $('.city_choose_select').on('click', function(e){
                $('.all_cities').toggleClass('hidden');
                $('.header_wrap_back').toggleClass('hidden');
            });

            $('.header_wrap_back').on('click', function(e){
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

        //КНОПКПА "ПОМОЧЬ С ВЫБОРОМ" НА ЛИСТИНГЕ
        if ($(window).width()<= 767) {
            window.addEventListener('scroll', () => {
                if ($(window).scrollTop() > 100) {
                    $('.help_with_choose').show();
                } else {
                    $('.help_with_choose').hide();
                }
            });

            // $('.help_with_choose').click(function () {
            // 	$('.popup_wrap').addClass('_active');
            // 	ym(64598434, 'reachGoal', 'header_button');
            // 	gtag('event', 'header_button');
            // });
        }

    }

    script(url) {
        if (Array.isArray(url)) {
            let self = this;
            let prom = [];
            url.forEach(function (item) {
                prom.push(self.script(item));
            });
            return Promise.all(prom);
        }

        return new Promise(function (resolve, reject) {
            let r = false;
            let t = document.getElementsByTagName('script')[0];
            let s = document.createElement('script');

            s.type = 'text/javascript';
            s.src = url;
            s.async = true;
            s.onload = s.onreadystatechange = function () {
                if (!r && (!this.readyState || this.readyState === 'complete')) {
                    r = true;
                    resolve(this);
                }
            };
            s.onerror = s.onabort = reject;
            t.parentNode.insertBefore(s, t);
        });
    }

    init() {
        setTimeout(function () {
            (function (m, e, t, r, i, k, a) {
                m[i] = m[i] || function () {
                    (m[i].a = m[i].a || []).push(arguments)
                };
                m[i].l = 1 * new Date();
                k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
            })
            (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

            ym(64598434, "init", {
                clickmap: true,
                trackLinks: true,
                accurateTrackBounce: true,
                webvisor: true
            });
        }, 100);

        this.script('https://www.googletagmanager.com/gtag/js?id=UA-175581738-1').then(() => {
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            global.gtag = gtag;
            gtag('js', new Date());

            gtag('config', 'UA-175581738-1');
        });

        // ItemSwiper.initSwiperListingGallery($('[data-item-swiper]'));

    }
}