<?php
namespace app\modules\svadbanaprirode\controllers;

use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use frontend\modules\svadbanaprirode\models\ElasticItems;
use frontend\components\ParamsFromQuery;
use common\widgets\FilterWidget;
use frontend\modules\svadbanaprirode\models\ItemsWidgetElastic;
//use common\models\elastic\ItemsWidgetElastic;
use common\models\elastic\ItemsFilterElastic;
use common\models\Seo;
use common\models\Filter;
use common\models\Slices;
use common\models\RoomsModule;
use common\models\RoomsUniqueId;
use backend\models\Pages;
use common\models\siteobject\SiteObjectSeo;

class SiteController extends Controller
{
    //public function getViewPath()
    //{
    //    return Yii::getAlias('@app/modules/svadbanaprirode/views/site');
    //}

    public function actionIndex()
    {
        //$rooms_mod = RoomsModule::find()->all();
       //foreach ($rooms_mod as $key => $value) {
       //    $room_uid = new RoomsUniqueId();
       //    $room_uid->id = $value->id;
       //    $room_uid->unique_id = $value->unique_id;
       //    $room_uid->save();
       //}

        $elastic_model = new ElasticItems;

        $filter_model = Filter::find()->with('items')->all();
        $slices_model = Slices::find()->all();

        $itemsWidget = new ItemsWidgetElastic;
        $apiMain = $itemsWidget->getMain($filter_model, $slices_model, 'rooms', $elastic_model);
        $items = new ItemsFilterElastic([], 1, 1, false, 'rooms', $elastic_model, false, false, false, false, false, true);
        $apiMain['total'] = $items->total;

        $seo = new Seo('index', 1, $apiMain['total']);
        $this->setSeo($seo->seo);

        $filter = FilterWidget::widget([
            'filter_active' => [],
            'filter_model' => $filter_model
        ]);

        foreach ($apiMain['widgets'] as $key => $items) {
//            $items_widget = [];
            $items_widget = $this->sortBeaty($items['items']);

            //КРАФТОВЫЙ ВЫВОД
            if ($items->slice->alias=="na-verande") {
                $apiMain['widgets'][$key]['items'] = array_slice($this->sortBeaty($this->getVerandsAndTerras()), 0, 8);
            } elseif ($items->slice->alias=="v-sharte") {
                $apiMain['widgets'][$key]['items'] = array_slice($this->sortBeaty($this->getShaters()), 0, 8);
            } else {
                $apiMain['widgets'][$key]['items'] = $items_widget;
            }
        }

//        $x = $this->getShaters();
//
//        echo '<pre>';
//        print_r($apiMain['widgets']);
//        die();


        return $this->render('index.twig', [
            'filter' => $filter,
            'widgets' => $apiMain['widgets'],
            'count' => $apiMain['total'],
            'seo' => $seo->seo,
            'city_dec' => Yii::$app->params['subdomen_dec'],
            'city_rod' => Yii::$app->params['subdomen_rod'],
            'city' => Yii::$app->params['subdomen_name'],
//            'rests_properties' => $rests_properties,
        ]);
    }

    public function actionError()
    {
        $elastic_model = new ElasticItems;

        $filter_model = Filter::find()->with('items')->all();
        $slices_model = Slices::find()->all();

        $itemsWidget = new ItemsWidgetElastic;
        $apiMain = $itemsWidget->getMain($filter_model, $slices_model, 'rooms', $elastic_model);

        $seo = new Seo('error', 1, 0);
        $this->setSeo($seo->seo);

        $filter = FilterWidget::widget([
            'filter_active' => [],
            'filter_model' => $filter_model
        ]);

        return $this->render('error.twig', [
            'filter' => $filter,
            'widgets' => $apiMain['widgets'],
            'count' => $apiMain['total'],
            'seo' => $seo->seo,
            'subdomen' => Yii::$app->params['subdomen']
        ]);
    }

    private function parseGetQuery($getQuery, $filter_model, $slices_model)
    {
        $return = [];
        if(isset($getQuery['page'])){
            $return['page'] = $getQuery['page'];
        }
        else{
            $return['page'] = 1;
        }

        $temp_params = new ParamsFromQuery($getQuery, $filter_model, $slices_model);
        $slice = Slices::findOne(['alias'=>str_replace('/','',$temp_params->listing_url)]);
        if (empty($slice)){
            unset($getQuery['msk']);
            $temp_params = new ParamsFromQuery($getQuery, $filter_model, $slices_model);
//            echo $temp_params->listing_url;
//            die();
        }

        $return['params_api'] = $temp_params->params_api;
        $return['params_filter'] = $temp_params->params_filter;
        $return['listing_url'] = $temp_params->listing_url;
        $return['canonical'] = $temp_params->canonical;

        return $return;
    }

    private function setSeo($seo){
        $this->view->title = $seo['title'];
        $this->view->params['desc'] = $seo['description'];
        $this->view->params['kw'] = $seo['keywords'];
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
}
