<?php

namespace app\modules\svadbanaprirode\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Rooms;
use common\models\Seo;
use common\models\RoomsUniqueId;
use app\modules\svadbanaprirode\models\ItemSpecials;
use frontend\modules\svadbanaprirode\models\ElasticItems;
use frontend\modules\svadbanaprirode\models\RoomsUniqueIdOld;
use frontend\components\Breadcrumbs;
use common\models\elastic\ItemsWidgetElastic;

class ItemController extends Controller
{

    public function actionIndex($id)
    {
//	    echo '<pre>';
//	    echo $id;
//	    die();

        $elastic_model = new ElasticItems;
        $item = $elastic_model::find()
            ->query([
                'bool' => [
                    'must' => [
                        ['match' => ['unique_id' => $id]],
                        ['match' => ['city_id' => \Yii::$app->params['subdomen_id']]],
                    ]
                ]
            ])
            ->limit(1)
            ->search();

        if (empty($item) or !isset($item['hits']['hits'][0])) {
            //КОСТЫЛЬ ДЛЯ РЕДИРЕКТОВ НА СТАРЫЕ ID, ЕСЛИ ОН СБИЛСЯ
            $old_id = RoomsUniqueIdOld::find('id')->where(['unique_id' => $id])->one();

            if (empty($old_id))
                throw new \yii\web\NotFoundHttpException();

            $unique_id_temp = RoomsUniqueId::find('unique_id')->where(['id' => $old_id['id']])->one();

            if (empty($unique_id_temp['unique_id']))
                throw new \yii\web\NotFoundHttpException();

            $unique_id_temp = $unique_id_temp['unique_id'];

            $item_tmp = $elastic_model::find()
                ->query([
                    'bool' => [
                        'must' => [
                            ['match' => ['unique_id' => $unique_id_temp]],
                            ['match' => ['city_id' => \Yii::$app->params['subdomen_id']]],
                        ]
                    ]
                ])
                ->limit(1)
                ->search();

            if (!empty($unique_id_temp) and $id != $unique_id_temp and !empty($item_tmp['hits']['hits'])) {
                $redirect_url = Yii::$app->params['subdomen'].'catalog/'.$unique_id_temp.'/';

                header('Location: https://svadbanaprirode.com/'.$redirect_url, true,301);
                exit;
//                return $this->redirect([$redirect_url], 302);
            } else {
                throw new \yii\web\NotFoundHttpException();
            }
        }

        $item = $item['hits']['hits'][0];

        $seo = new Seo('item', 1, 0, $item);
        $seo = $seo->seo;
        $this->setSeo($seo);

        $seo['h1'] = $item->name;
        $seo['breadcrumbs'] = Breadcrumbs::get_breadcrumbs(2);
        $seo['address'] = $item->restaurant_address;
        $seo['desc'] = $item->restaurant_name;

        $special_obj = new ItemSpecials($item->restaurant_special);
        $item->restaurant_special = $special_obj->special_arr;

        $itemsWidget = new ItemsWidgetElastic;
        $other_rooms = $this->getOther($item->restaurant_id, $item->unique_id);
        $similar_rooms = $itemsWidget->getSimilar($item, 'rooms', $elastic_model);

        return $this->render('index.twig', array(
            'item' => $item,
            'queue_id' => $id,
            'seo' => $seo,
            'other_rooms' => $other_rooms,
            'similar_rooms' => $similar_rooms,
            'city_link' => Yii::$app->params['subdomen']
        ));
    }

    private function setSeo($seo)
    {
        $this->view->title = $seo['title'];
        $this->view->params['desc'] = $seo['description'];
        $this->view->params['kw'] = $seo['keywords'];
    }

    public function getOther($restaurant_id, $room_id, $elastic_model = false)
    {
        $items = ElasticItems::find()->query([
            'bool' => [
                'must' => [
                    ['match' => ['restaurant_id' => $restaurant_id]]
                ],
                'must_not' => [
                    ['match' => ['unique_id' => $room_id]]
                ],
            ],
        ])->all();

        return $items;
    }
}
