'use strict';
import Cookies from 'js-cookie';
import Swiper from 'swiper';

export default class Favorites {
    constructor() {
        let self = this;
        this.subdomen = $('[data-city-block]');
        this.swiper_objects = [];

        self.swiperInit();
        self.swiperItemInit();

        let fixed_swiper_height = $('[data-swiper="favorites"]').outerHeight();
        $('.compare-wrap').css('height', $('.compare-wrap').height());

        //
        // $('form').on('submit', function () {
        //     if ($(this).closest(['data-page-type="favorites"'])) {
        //         console.log('bbb')
        //
        //     }
        // });

        //УДАЛЕНИЕ ИЗ ИЗБРАННОГО
        $('body').on('click', '[data-del-favorites]', function (e) {
            $.ajax({
                type: 'get',
                url: '/' + self.subdomen.data('alias') + 'ajax/del-favorites/',
                data: {'room_id': $(this).data('del-favorites'), 'type': $(this).data('del-all') ? 'all' : 'one'},
                success: function (response) {

                    let res = JSON.parse(response);
                    $('.header_favorites_link').attr('data-count', res.count);

                    //скрываем одно заведение
                    if (res.room_id != ''){
                        $('.favorites_block div[data-room-id="' + res.room_id + '"]').remove();
                    }

                    //скрываем все заведения
                    if (res.count == 0) {
                        $('.favorites_block').remove();
                        $('.favorites-form').remove();
                        $('.favorites-empty.hidden').removeClass('hidden');
                    }

                    self.swiperUpdate();
                },
                error: function (response) { }
            });
        });

        //ПОКАЗЫВАТЬ ТОЛЬКО ОТЛИЧИЯ
        $('[data-diff]').on('click', function () {
            $('[data-diff]').toggleClass('active_radio');
            let diff = 0;
            $(this).toggleClass('_active');

            if ($(this).hasClass('_active'))
                diff = 1;

            $.ajax({
                type: 'get',
                url: '/' + self.subdomen.data('alias') + 'ajax/diff-favorites/',
                data: {'diff': diff},
                success: function (response) {
                    let res = JSON.parse(response);
                    $('[data-favorites-block]').html(res.favorites);
                    self.swiperInit();
                    self.swiperItemInit();
                },
                error: function (response) { }
            });
        })

        $(window).scroll(function() {
            if ($('.favorites-form').offset())
                var blockOffset = $('.favorites-form').offset().top;
            var scrollTop = $(window).scrollTop();
            var position = blockOffset - scrollTop;
            var favorites_list_height = $('.favorites-list').height();
            var header_height = $('header').height();
            var compare_wrap = $('.compare-wrap');
            var data_swiper_favorites = $('.favorites-list-wrap');

            console.log(fixed_swiper_height);
            if (position - favorites_list_height - 100 > header_height) {
                if (scrollTop >= $('.scroll-detect').offset().top - header_height + 235) {
                    data_swiper_favorites.css('display', 'block');
                    data_swiper_favorites.addClass('_fixed_top');
                    compare_wrap.css('padding-top', fixed_swiper_height);
                } else {
                    data_swiper_favorites.removeClass('_fixed_top');
                    compare_wrap.css('padding-top', 'unset');
                }
            } else {
                data_swiper_favorites.removeClass('_fixed_top');
                compare_wrap.css('padding-top', 'unset');
            }
        });

        //СКРОЛЛ К ФОРМЕ НА СТРАНИЦЕ ИЗБРАННОЕ
        $('[data-favorite-book]').on('click', function () {
            let form = $('.favorites-form');
            let form_offset_top = form.offset().top;
            let header_height = $('header').height();
            let scroll_length = form_offset_top - header_height - 50;
            $('html,body').animate({scrollTop: scroll_length}, 400);
            // ym(64598434, 'reachGoal', 'scroll_form');
            // gtag('event', 'scroll_form');
            // console.log('scroll_form');
        });
    }

    bindSwipers(swiperList) {
        for (const swiper of swiperList) {
            swiper.setTranslate = function (translate, byController, doNotPropagate) {
                if (doNotPropagate) {
                    Swiper.prototype.setTranslate.apply(this, arguments);
                } else {
                    for (const swiper of swiperList) {
                        swiper.setTranslate(translate, byController, true);
                    }
                }
            };
            swiper.setTransition = function (duration, byController, doNotPropagate) {
                if (doNotPropagate) {
                    Swiper.prototype.setTransition.apply(this, arguments);
                } else {
                    for (const swiper of swiperList) {
                        swiper.setTransition(duration, byController, true);
                    }
                }
            };
        }
    }

    swiperItemInit() {
        $('.item_swiper').each(function () {
            let item_swiper = new Swiper($(this), {
                loop: true,
                navigation: {
                    nextEl: ".item-button-next",
                    prevEl: ".item-button-prev",
                },
                pagination: {
                    el: ".item-pagination",
                    type: 'bullets',
                    dynamicBullets: true,
                    dynamicMainBullets: 1,
                    clickable: true,
                },
            });

            // window.addEventListener('load', function() {
            //     item_swiper.update();
            // });
        });

    }

    swiperInit() {
        let countSlides = $('[data-swiper="favorites"]').find('.swiper-slide.favorites-item').length;
        // console.log('swiperInit');
        // console.log('countSlides', countSlides);

        if (countSlides < 5) {
            $('.wrap_swipe').addClass('hidden');
        }

        let swiper_settings = {
            // watchSlidesVisibility: true,
            // observer: true,
            // observeParents: true,
            // autoHeight: true,
            slidesPerView: 4,
            spaceBetween: 20,
            watchSlidesProgress: true,
            watchSlidesVisibility: true,
            // loop: false,
            // freeMode: false,
            // autoResize: true,
            allowTouchMove: countSlides < 5 ? false : true,
            // hashNavigation: true,
            navigation: {
                nextEl: ".favorites-button-next",
                prevEl: ".favorites-button-prev",
            },
            breakpoints: {
                540: {
                    slidesPerView: countSlides < 2 ? 1 : 1.2,
                    allowTouchMove: countSlides < 2 ? false : true,
                },
                768: {
                    slidesPerView: 2,
                    allowTouchMove: countSlides < 3 ? false : true
                },
                1540: {
                    slidesPerView: 3,
                    allowTouchMove: countSlides < 4 ? false : true
                },
                1920: {
                    slidesPerView: 4,
                    allowTouchMove: countSlides < 5 ? false : true
                }
            }
        };

        let swiper_arr = [];
        $('[data-swiper]').each(function () {
            let attr = $(this).data('swiper');
            let swiper = new Swiper('[data-swiper="' + attr + '"]', swiper_settings);
            swiper_arr.push(swiper);
            delete swiper_settings.navigation;
            // delete swiper_settings.spaceBetween;
        })

        this.swiper_objects = swiper_arr;
        this.bindSwipers(this.swiper_objects);
    }

    swiperUpdate() {
        let countSlides = $('[data-swiper="favorites"]').find('.swiper-slide.favorites-item').length;
        let window_width = $(window).width();

        console.log('countSlides', countSlides)
        console.log('window_width', window_width)

        if (countSlides < 5 && window_width > 1540) {
            $('.wrap_swipe').addClass('hidden');
            this.swiper_objects.forEach(function(swiper) {
                swiper.allowTouchMove = false;
            });
        }

        if (countSlides < 2 && window_width < 540) {
            $('.wrap_swipe').addClass('hidden');
            this.swiper_objects.forEach(function(swiper) {
                swiper.allowTouchMove = false;
            });
        }
    }
}