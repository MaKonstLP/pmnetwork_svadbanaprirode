<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
//use frontend\modules\svadbanaprirode\assets\AppAsset;

frontend\modules\svadbanaprirode\assets\AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<?php //<meta name="robots" content="noindex, nofollow" />?>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="/img/svadbanaprirode.ico">
    <title><?php echo $this->title ?></title>
    <?php $this->head() ?>
    <?php if(Yii::$app->params['noindex_global'] === true){
        echo '<meta name="robots" content="noindex" />';
    } ?>
    <?php if (isset($this->params['desc']) and !empty($this->params['desc'])) echo "<meta name='description' content='".$this->params['desc']."'>";?>
<!--    --><?php //if (isset($this->params['canonical']) and !empty($this->params['canonical'])) echo "<link rel='canonical' href='".$this->params['canonical']."'>";?>
    <?php if (isset($this->params['kw']) and !empty($this->params['kw'])) echo "<meta name='keywords' content='".$this->params['kw']."'>";?>
    <script src="https://www.googleoptimize.com/optimize.js?id=OPT-W4RJZ95"></script>
    <?= Html::csrfMetaTags() ?>
</head>
<body>
<!--<script type="text/javascript">-->
<!--    var fired = false;-->
<!--    const REAL_USER_EVENT_TRIGGERS = [-->
<!--        'click',-->
<!--        'scroll',-->
<!--        'keypress',-->
<!--        'wheel',-->
<!--        'mousemove',-->
<!--        'touchmove',-->
<!--        'touchstart',-->
<!--    ];-->
<!--    REAL_USER_EVENT_TRIGGERS.forEach(event => {-->
<!--        window.addEventListener(event, () => {-->
<!--            if (fired === false) {-->
<!--                fired = true;-->
<!--                load_other();-->
<!--            }-->
<!--        });-->
<!--    }, {passive: true});-->
<!---->
<!--    function load_other() {-->
<!--        setTimeout(() => {-->
<!--            (function (m, e, t, r, i, k, a) {-->
<!--                m[i] = m[i] || function () {-->
<!--                    (m[i].a = m[i].a || []).push(arguments)-->
<!--                };-->
<!--                m[i].l = 1 * new Date();-->
<!--                k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)-->
<!--            })-->
<!--            (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");-->
<!---->
<!--            ym(67719148, "init", {-->
<!--                clickmap: true,-->
<!--                trackLinks: true,-->
<!--                accurateTrackBounce: true,-->
<!--                webvisor: true-->
<!--            });-->
<!---->
<!--            var googletagmanager_js = document.createElement('script');-->
<!--            googletagmanager_js.src = 'https://www.googletagmanager.com/gtag/js?id=UA-179040293-1';-->
<!--            document.body.appendChild(googletagmanager_js);-->
<!---->
<!--        }, 500);-->
<!--    }-->
<!---->
<!--    setTimeout(() => {-->
<!--        fired = true;-->
<!--        load_other();-->
<!--        gtag('event', 'read', {'event_category': '15 seconds'});-->
<!--    }, 15000);-->
<!--</script>-->
<?php $this->beginBody() ?>

    <div class="main_wrap">
        
        <header>
            <div class="header_wrap">
                <div class="header_wrap_back hidden"></div>
                <div class="header_logo">
                    <a href="/<?= Yii::$app->params['subdomen'] ?>">
                        <div class="header_logo_img"></div>
                        <span class="header_logo_subtitle">Банкетная служба <?= Yii::$app->params['subdomen_rod'] ?></span>
                    </a>
                    <div class="city_choose">
                        <div class="city_choose_select">
                            <img src="/img/city_choose.svg" alt="Выберите город">
                            <span class="header_menu_item"
                                  data-city-block
                                  data-alias="<?= Yii::$app->params['subdomen'] ?>"
                                  data-id="<?= Yii::$app->params['subdomen_id'] ?>"
                                  data-baseid="<?= Yii::$app->params['subdomen_baseid'] ?>"
                            ><?= Yii::$app->params['subdomen_name'] ?></span>
                        </div>
                        <div class="all_cities hidden">
                            <div class="all_cities_polygon"></div>
                            <div class="all_cities_wrap">
                                <ul>
                                    <?php foreach (Yii::$app->params['subdomen_list'] as $subdomen): ?>
                                        <li>
                                            <?php if($subdomen['alias'] != 'msk'): ?>
                                                <a href="/<? echo $subdomen['alias'] ?>/" class="<?= $subdomen['name'] == Yii::$app->params['subdomen_name'] ? 'active_city' : '' ?>"><? echo $subdomen['name'] ?></a>
                                            <?php else: ?>
                                                <a href="/" class="<?= $subdomen['name'] == Yii::$app->params['subdomen_name'] ? 'active_city' : '' ?>"><? echo $subdomen['name'] ?></a>
                                            <?php endif;?>
                                        </li>
                                    <?php endforeach;?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="header_menu">
                    <div class="header_menu_mobile_under hidden">
                        <div class="under_cities_wrap">
                            <div class="under_cities_back">
                                <img src="/img/widget_link_arrow.svg" alt="Назад">
                                <span>Назад</span>
                            </div>
                            <p>Выберите свой город:</p>
                            <ul>
                                <?php foreach (Yii::$app->params['subdomen_list'] as $subdomen): ?>
                                    <li>
                                        <?php if($subdomen['alias'] != 'msk'): ?>
                                            <a href="/<? echo $subdomen['alias'] ?>/" class="<?= $subdomen['name'] == Yii::$app->params['subdomen_name'] ? 'active_city_mobile' : '' ?>"><? echo $subdomen['name'] ?></a>
                                        <?php else: ?>
                                            <a href="/" class="<?= $subdomen['name'] == Yii::$app->params['subdomen_name'] ? 'active_city' : '' ?>"><? echo $subdomen['name'] ?></a>
                                        <?php endif;?>
                                    </li>
                                <?php endforeach;?>
                            </ul>
                        </div>
                    </div>

                    <div class="header_menu_top">
                        <div class="city_choose">
                            <div class="city_choose_select">
                                <img src="/img/city_choose.svg" alt="Выберите город">
                                <span class="header_menu_item"
                                      data-city-block
                                      data-alias="<?= Yii::$app->params['subdomen'] ?>"
                                      data-id="<?= Yii::$app->params['subdomen_id'] ?>"
                                      data-baseid="<?= Yii::$app->params['subdomen_baseid'] ?>"
                                ><?= Yii::$app->params['subdomen_name'] ?></span>
                            </div>
                        </div>
                    </div>

                    <?php foreach (Yii::$app->params['menu'] as $item): ?>
                        <div class="header_menu_item_wrap">
                            <a class="header_menu_item <?= (!empty($this->params['menu']) and $this->params['menu'] == $item['link']) ? '_active' : '' ?>" href="/<?= Yii::$app->params['subdomen'] ?><?= $item['link'] ?>/"><?= $item['title'] ?></a>
                            <?php if(!empty($item['submenu'])): ?>
                                 <img src="/img/arrow_dropdown.svg" alt="">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <div class="header_menu_item_wrap favorites">
                        <a href="/<?= Yii::$app->params['subdomen'] ?>favorites/" class="header_favorites_link" data-count="<?= Yii::$app->params['subdomen_favorite']['count'] ?>">
                            <svg class="favorites_header_icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13.9935 13.6594C12.7017 14.8972 11.3004 15.9965 9.81009 16.9422C9.22744 16.5781 7.31892 15.3293 5.44946 13.4951C3.25538 11.3425 1.3125 8.57646 1.3125 5.62499C1.3125 4.6396 1.60374 3.68485 2.13708 2.89619C2.66993 2.10827 3.41405 1.53009 4.25606 1.23096C5.09701 0.932201 6.00244 0.923132 6.84806 1.20466C7.69471 1.48653 8.44836 2.04912 8.99469 2.82549L9.8125 3.98763L10.6303 2.82549C11.1766 2.04912 11.9303 1.48653 12.7769 1.20466C13.6226 0.923132 14.528 0.932201 15.3689 1.23096C16.211 1.53009 16.9551 2.10828 17.4879 2.89619C18.0213 3.68485 18.3125 4.6396 18.3125 5.62499C18.3125 8.11273 16.962 10.813 13.9935 13.6594Z" stroke="#7ABF62" stroke-width="2"/>
                            </svg>

                            <span class="header_menu_favorites">Избранное</span>
                        </a>
<!--                        <span class="header_menu_favorites_count"></span>-->
                    </div>

                    <div class="header_menu_item_mobile header_phone_button" data-open-popup-form data-type="help">
                        <p class="_link">Подберите мне зал для свадьбы</p>
                    </div>
                </div>
                <div class="header_phone">
                    <a href="tel:<?=Yii::$app->params['subdomen_phone']?>"><p><?=Yii::$app->params['subdomen_phone_pretty']?></p></a>
                    <div class="header_phone_button" data-open-popup-form data-type="help">
                        <p class="_link">Подберите мне зал для свадьбы</p>
                    </div>
                </div>


                <div class="header_menu_item_wrap favorites mobile_header_favorites">
                    <a href="/<?= Yii::$app->params['subdomen'] ?>favorites/" class="header_favorites_link" data-count="<?= Yii::$app->params['subdomen_favorite']['count'] ?>">
                        <svg class="favorites_header_icon" width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.7746 18.0045C16.1096 19.5998 14.3001 21.0126 12.3734 22.2225C11.8495 21.9024 9.27029 20.2739 6.74612 17.7974C3.99155 15.0948 1.5 11.5753 1.5 7.78124C1.5 6.5007 1.87837 5.25829 2.57364 4.23019C3.26841 3.20284 4.24103 2.44559 5.34576 2.05312C6.44943 1.66103 7.63847 1.64909 8.74842 2.01863C9.8594 2.3885 10.8447 3.1255 11.5572 4.13799L12.375 5.30013L13.1928 4.13799C13.9053 3.1255 14.8906 2.3885 16.0016 2.01863C17.1115 1.64909 18.3006 1.66103 19.4042 2.05312C20.509 2.44559 21.4816 3.20284 22.1764 4.23019C22.8716 5.25829 23.25 6.5007 23.25 7.78124C23.25 10.9861 21.5103 14.4222 17.7746 18.0045Z" stroke="#7ABF62" stroke-width="2"/>
                        </svg>
                    </a>
                </div>

                <div class="header_burger">
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
        </header>

        <div class="content_wrap">
            <?= $content ?>
        </div>

        <footer>
            <div class="footer_wrap">
                <div class="footer_row">
                    <div class="footer_block _left">
                        <a href="/<?= Yii::$app->params['subdomen'] ?>" class="footer_logo">
                            <div class="footer_logo_img"></div>
                        </a>
                        <div class="footer_info">
                            <p class="footer_copy">© <?php echo date("Y");?> Свадьба на природе</p>
                            <a href="/privacy/" target="_blank" class="footer_pc _link">Политика конфиденциальности</a>
                        </div>                        
                    </div>
                    <div class="footer_block _right">
                        <div class="footer_phone">
                            <a href="tel:<?=Yii::$app->params['subdomen_phone']?>"><p><?=Yii::$app->params['subdomen_phone_pretty']?></p></a>
                        </div>
                        <div class="footer_phone_button" data-open-popup-form data-type="help">
                            <p class="_link">Подберите мне зал для свадьбы</p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

    </div>

    <div class="popup_wrap" data-type="main">
        <div class="popup_layout" data-close-popup></div>
        <div class="popup_form">
            <?=$this->render('//components/generic/form.twig', ['type' => 'main'])?>
        </div>
    </div>

    <div class="popup_wrap" data-type="help">
        <div class="popup_layout" data-close-popup></div>
        <div class="popup_form">
            <?=$this->render('//components/generic/form_help.twig', ['type' => 'help'])?>
        </div>
    </div>

<?php $this->endBody() ?>
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700&display=swap&subset=cyrillic" rel="stylesheet">
<!-- Yandex.Metrika counter -->
<noscript><div><img src="https://mc.yandex.ru/watch/64598434" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<!-- Global site tag (gtag.js) - Google Analytics -->
<!-- Global site tag (gtag.js) - Google Analytics -->
</body>
</html>
<?php $this->endPage() ?>
