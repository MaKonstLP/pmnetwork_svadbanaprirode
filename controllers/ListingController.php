<?php

namespace app\modules\svadbanaprirode\controllers;

use http\Exception;
use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\widgets\FilterWidget;
use frontend\widgets\PaginationWidget;
use frontend\components\ParamsFromQuery;
use frontend\components\QueryFromSlice;
use frontend\components\Breadcrumbs;
use common\models\ItemsFilter;
use common\models\elastic\ItemsFilterElastic;
//use frontend\modules\svadbanaprirode\models\ItemsFilterElastic;
use frontend\modules\svadbanaprirode\models\ElasticItems;
use backend\models\Filter;
use backend\models\Slices;
use common\models\GorkoApi;
use common\models\GorkoApiTest;
use common\models\Seo;

class ListingController extends Controller
{
    protected $per_page = 24;

    public $filter_model,
        $slices_model,
        $url;

    public function beforeAction($action)
    {
        $this->filter_model = Filter::find()->with('items')->all();
        $this->slices_model = Slices::find()->all();

        return parent::beforeAction($action);
    }

    public function actionSlice($city = '', $slice)
    {
//	    if (Yii::$app->params['subdomen_alias'] !== $city)
//            throw new \yii\web\NotFoundHttpException();

//        echo '<pre>';
//        print_r($_GET);
//        die();

        $slice_obj = new QueryFromSlice($slice);

        if ($slice_obj->flag) {
            $this->view->params['menu'] = $slice;
            $params = $this->parseGetQuery($slice_obj->params, $this->filter_model, $this->slices_model);

            isset($_GET['page']) ? $params['page'] = $_GET['page'] : $params['page'];

            $canonical = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];
            if ($params['page'] > 1) {
                $canonical .= $params['canonical'];
            }

            return $this->actionListing(
                $page = $params['page'],
                $per_page = $this->per_page,
                $params_filter = $params['params_filter'],
                $breadcrumbs = Breadcrumbs::get_breadcrumbs(2),
                $canonical = $canonical,
                $type = $slice
            );
        } else {
            throw new \yii\web\NotFoundHttpException();
        }
    }

    public function actionIndex($city = '')
    {
        $getQuery = $_GET;
        unset($getQuery['q']);
        if (count($getQuery) > 0) {
            $params = $this->parseGetQuery($getQuery, $this->filter_model, $this->slices_model);
            $canonical = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];
            if ($params['page'] > 1) {
                $canonical .= $params['canonical'];
            }

            return $this->actionListing(
                $page = $params['page'],
                $per_page = $this->per_page,
                $params_filter = $params['params_filter'],
                $breadcrumbs = Breadcrumbs::get_breadcrumbs(1),
                $canonical = $canonical,
                $type = 'catalog'
            );

        } else {
            $canonical = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];

            return $this->actionListing(
                $page = 1,
                $per_page = $this->per_page,
                $params_filter = [],
                $breadcrumbs = Breadcrumbs::get_breadcrumbs(1),
                $canonical = $canonical,
                $type = 'listing'
            );
        }
    }

    public function actionListing($page, $per_page, $params_filter, $breadcrumbs, $canonical, $type = false)
    {
        $elastic_model = new ElasticItems;
        $items = new ItemsFilterElastic($params_filter, $per_page, $page, false, 'rooms', $elastic_model, false, false, false, false, false, true);

//        $items_beatiful = $this->getBeatifulRooms();
//        $items = $this->mergeArrays($items, $items_beatiful, $page, $per_page);

        if ($page > 1) {
            $seo['text_top'] = '';
            $seo['text_bottom'] = '';
        }

        $filter = FilterWidget::widget([
            'filter_active' => $params_filter,
            'filter_model' => $this->filter_model
        ]);

        $pagination = PaginationWidget::widget([
            'total' => $items->pages,
            'current' => $page,
        ]);

        $seo_type = $type ? $type : 'listing';

        $get = $_GET;
        unset($get['city']);
        unset($get['q']);
        if (empty($get) and $seo_type == 'catalog')
            $seo_type = 'listing';

        $badge_type['y-vody'] = !empty($params_filter['y-vody']) ? true : false;

        // КРАФТОВЫЙ ВЫВОД ДЛЯ ВЕРАНД/ТЕРРАС НАЧАЛО
        $total = $items->total;
        $items = $items->items;

        $isShater = false;
        if (!empty($get['slice']))
            $isShater = preg_match('/v-sharte/', $get['slice']);

        if ($isShater) {
            $items = $this->getShaters();
            $items = $this->sortBeaty($items);
            $total = count($items);
            $items = array_slice($items, ($page-1)*$per_page, $per_page);
            $pages = ceil($total / $per_page);

            $pagination = PaginationWidget::widget([
                'total' => $pages,
                'current' => $page,
            ]);
        }

        if (!empty($get['slice']) && $get['slice'] == 'na-verande') {
            $elastic_model = new ElasticItems;
            $items_craft =  $this->getVerandsAndTerras();

            $elastic_model = new ElasticItems;
            $items_tmp = new ItemsFilterElastic($params_filter, 9999, 0, false, 'rooms', $elastic_model, false, false, false, false, false, true);
            $items_tmp = $items_tmp->items;

            $items = $this->mergeArrays($items_craft, $items_tmp);
            $items = $this->sortBeaty($items);
            $items = array_values($items);
            $total = count($items);
            $items = array_slice($items, ($page-1)*$per_page, $per_page);

            $pagination = PaginationWidget::widget([
                'total' => ceil($total / $per_page),
                'current' => $page,
            ]);
        }
        // КРАФТОВЫЙ ВЫВОД ДЛЯ ВЕРАНД/ТЕРРАС КОНЕЦ

        $seo = $this->getSeo($seo_type, $page, $total);

        $seo['breadcrumbs'] = $breadcrumbs;
        $this->setSeo($seo, $page, $canonical);

        if ($seo_type == 'listing' and count($params_filter) > 0) {
            $seo['text_top'] = '';
            $seo['text_bottom'] = '';
        }

//        $items_beatiful = $this->getBeatifulRooms();
//        $items = $this->mergeArrays($items, $items_beatiful, $page, $per_page);
        $items = $this->sortBeaty($items);

//        echo '<pre>';
//        print_r($items);
//        die();

        return $this->render('index.twig', array(
            'items' => $items,
            'filter' => $filter,
            'pagination' => $pagination,
            'seo' => $seo,
            'badge_type' => $badge_type,
            'count' => $total,
            'isShater' => $isShater
//            'rests_properties' => $rests_properties,
        ));
    }

    public function actionAjaxFilter()
    {
        $params = $this->parseGetQuery(json_decode($_GET['filter'], true), $this->filter_model, $this->slices_model);

        $elastic_model = new ElasticItems;
        $items = new ItemsFilterElastic($params['params_filter'], $this->per_page, $params['page'], false, 'rooms', $elastic_model, false, false, false, false, false, true);

        substr($params['listing_url'], 0, 1) == '?' ? $breadcrumbs = Breadcrumbs::get_breadcrumbs(1) : $breadcrumbs = Breadcrumbs::get_breadcrumbs(2);
        $get = json_decode($_GET['filter'], true);

        // КРАФТОВЫЙ ВЫВОД ДЛЯ ВЕРАНД/ТЕРРАС НАЧАЛО
        $total = $items->total;
        $pages = $items->pages;
        $items = $items->items;

        $isVeranda = false;
        if (!empty($params['listing_url']))
            $isVeranda = preg_match('/na-verande\//', $params['listing_url']);

        $isShater = false;
        if (!empty($params['listing_url']))
            $isShater = preg_match('/v-sharte\//', $params['listing_url']);
        
        if ($isShater) {
            $items = $this->getShaters();
            $items = $this->sortBeaty($items);
            $total = count($items);
            $items = array_slice($items, ($params['page']-1)*$this->per_page, $this->per_page);
            $pages = ceil($total / $this->per_page);

            $pagination = PaginationWidget::widget([
                'total' => $pages,
                'current' => $params['page'],
            ]);
        }

        if ($isVeranda) {
            $items_craft =  $this->getVerandsAndTerras();
            $elastic_model = new ElasticItems;
            $items_tmp = new ItemsFilterElastic($params['params_filter'], 9999, 0, false, 'rooms', $elastic_model, false, false, false, false, false, true);
            $items_tmp = $items_tmp->items;

            $items = $this->mergeArrays($items_craft, $items_tmp);
            $items = $this->sortBeaty($items);
            $items = array_values($items);
            $total = count($items);
            $items = array_slice($items, ($params['page']-1)*$this->per_page, $this->per_page);
            $pages = ceil($total / $this->per_page);

            $pagination = PaginationWidget::widget([
                'total' => $pages,
                'current' => $params['page'],
            ]);
        }
        // КРАФТОВЫЙ ВЫВОД ДЛЯ ВЕРАНД/ТЕРРАС КОНЕЦ

        $pagination = PaginationWidget::widget([
            'total' => $pages,
            'current' => $params['page'],
        ]);

        unset($get['page']);
        $slice_url = ParamsFromQuery::isSlice($get);
        if (!empty($get['msk']) and empty(ParamsFromQuery::isSlice($get))) {
            unset($get['msk']);
            $slice_url = ParamsFromQuery::isSlice($get);
        }

        $seo_type = $slice_url ? $slice_url : 'listing';
        if (!empty($get) and $seo_type == 'listing')
            $seo_type = 'catalog';

        $seo = $this->getSeo($seo_type, $params['page'], $total);
        $seo['breadcrumbs'] = $breadcrumbs;

        $title = $this->renderPartial('//components/generic/title.twig', array(
            'seo' => $seo,
            'count' => $total,
            'isShater' => $isShater
        ));

        if ($params['page'] == 1) {
            $text_top = $this->renderPartial('//components/generic/text.twig', array('text' => $seo['text_top']));
            $text_bottom = $this->renderPartial('//components/generic/text.twig', array('text' => $seo['text_bottom']));
        } else {
            $text_top = '';
            $text_bottom = '';
        }

        if ($seo_type == 'listing' and count($params['params_filter']) > 0) {
            $text_top = '';
            $text_bottom = '';
        }

        $badge_type['y-vody'] = !empty($params['params_filter']['y-vody']) ? true : false;

        $items = $this->sortBeaty($items);

        return json_encode([
            'listing' => $this->renderPartial('//components/generic/listing.twig', array(
                'items' => $items,
                'badge_type' => $badge_type,
                'img_alt' => $seo['img_alt'],
            )),
            'pagination' => $pagination,
            'url' => $params['listing_url'],
            'title' => $title,
            'text_top' => $text_top,
            'text_bottom' => $text_bottom,
            'badge_type' => $badge_type,
            'seo_title' => $seo['title']
        ]);
    }

    public function actionAjaxFilterSlice()
    {
        $get = json_decode($_GET['filter'], true);
        $slice_url = ParamsFromQuery::isSlice($get);
        $slice = Slices::findOne(['alias' => str_replace('/', '', $slice_url)]);

        if (empty($slice)) {
            unset($get['msk']);
            $slice_url = ParamsFromQuery::isSlice($get);
        }

        return $slice_url;
    }

    private function parseGetQuery($getQuery, $filter_model, $slices_model)
    {
        $return = [];
        if (isset($getQuery['page'])) {
            $return['page'] = $getQuery['page'];
        } else {
            $return['page'] = 1;
        }

        $temp_params = new ParamsFromQuery($getQuery, $filter_model, $this->slices_model);
        $slice = Slices::findOne(['alias' => str_replace('/', '', $temp_params->listing_url)]);
        if (empty($slice)) {
            unset($getQuery['msk']);
            $temp_params = new ParamsFromQuery($getQuery, $filter_model, $this->slices_model);
//            echo $temp_params->listing_url;
//            die();
        }

        $return['params_api'] = $temp_params->params_api;
        $return['params_filter'] = $temp_params->params_filter;
        $return['listing_url'] = $temp_params->listing_url;
        $return['canonical'] = $temp_params->canonical;

        return $return;
    }

    private function getShaters() {
        $elastic_model = new ElasticItems;
        $items = ElasticItems::find()->query([
            'bool' => [
                'must' => [
                    [
                        ['match' => ['city_id' => \Yii::$app->params['subdomen_id']]],
                    ],
                    [
                        'bool' => [
                            'should' => [
                                [
                                    'wildcard' => [
                                        'name' => '*шатер*'
                                    ]
                                ],
                                [
                                    'wildcard' => [
                                        'name' => '*шатёр*'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ])->limit(9999)->all();

        return $items;
    }

    private function getVerandsAndTerras() {
        $elastic_model = new ElasticItems;
        $items = ElasticItems::find()->query([
            'bool' => [
                'must' => [
                    [
                        ['match' => ['city_id' => \Yii::$app->params['subdomen_id']]],
                    ],
                    [
                        'bool' => [
                            'should' => [
                                [
                                    'wildcard' => [
                                        'name' => '*веранд*'
                                    ]
                                ],
                                [
                                    'wildcard' => [
                                        'name' => '*террас*'
                                    ]
                                ],
                                [
                                    'wildcard' => [
                                        'name' => '*terrace*'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ])->limit(9999)->all();

        return $items;
    }

    private function getBeatifulRooms() {
        $elastic_model = new ElasticItems;
        $items = ElasticItems::find()->query([
            'bool' => [
                'must' => [
                    [
                        ['match' => ['city_id' => \Yii::$app->params['subdomen_id']]],
                        ['match' => ['sort_type' => 1]],
                    ],
                ]
            ]
        ])->limit(9999)->all();

        return $items;
    }

    private function getSeo($type, $page, $count = 0)
    {
        $seo = new Seo($type, $page, $count);

        return $seo->seo;
    }

    private function setSeo($seo, $page, $canonical)
    {
        $this->view->title = $seo['title'];
        $this->view->params['desc'] = $seo['description'];
        if ($page != 1) {
//			$this->view->params['canonical'] = $canonical;
        }
        $this->view->params['kw'] = $seo['keywords'];
    }

    private function sortBeaty($items) {
        if (empty($items))
            return [];

        $beaty_commission = array_filter($items, function ($item){
            return ($item->sort_type == 1 and $item->restaurant_commission == 2);
        });

        $beaty_uncommission = array_filter($items, function ($item){
            return ($item->sort_type == 1 and $item->restaurant_commission != 2);
        });

        $unbeaty_commission = array_filter($items, function ($item){
            return ($item->sort_type == 0 and $item->restaurant_commission == 2);
        });

        $unbeaty_uncommission = array_filter($items, function ($item){
            return ($item->sort_type == 0 and $item->restaurant_commission != 2);
        });

        return $beaty_commission + $unbeaty_commission + $beaty_uncommission + $unbeaty_uncommission;
    }

    private function mergeArrays($items, $items_beatiful, $page = '', $per_page = '') {
        $items_res = [];
        foreach ($items_beatiful as $item) {
            $items_res[$item['unique_id']] = $item;
        }
        foreach ($items as $item) {
            if (!isset($items_res[$item['unique_id']])) {
                $items_res[$item['unique_id']] = $item;
            }
        }

        if (!empty($page) and !empty($per_page))
            $items_res = array_slice($items_res, ($page-1)*$per_page, $per_page);

        return $items_res;
    }

}