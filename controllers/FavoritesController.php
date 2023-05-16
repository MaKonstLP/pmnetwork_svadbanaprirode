<?php
namespace app\modules\svadbanaprirode\controllers;

use frontend\components\Breadcrumbs;
use frontend\modules\svadbanaprirode\models\ElasticItems;
use common\models\elastic\ItemsWidgetElastic;
use common\models\Filter;
use common\models\Slices;

use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Cookie;

class FavoritesController extends Controller
{
    public $filter_model, $slices_model;

    public function actionIndex() {
//        $seo = (new Seo('favorite'))->seo;
//        $seo['breadcrumbs'] = Breadcrumbs::get_breadcrumbs(5);
//        $this->setSeo($seo);

        $favorite = array_values(Yii::$app->params['subdomen_favorite']['items']);
        $subdomen = Yii::$app->params['subdomen'];
        $subdomen_id = Yii::$app->params['subdomen_id'];

        $rooms = ElasticItems::find()->query([
            'bool' => [
                'must' => [
                    ['terms' => ['unique_id' => $favorite]]
                ],
            ],

        ])->limit(100)->all();

        $params = [
            'rating' => true,
            'district' => true,
            'capacity' => true,
            'price' => true,
            'price_with_food' => true,
            'price_without_food' => true,
            'kitchen' => true,
            'parking' => true,
            'outside_registration' => true,
            'alcohol' => true,
            'firework' => true,
            'own_fruits' => true,
        ];

        if (\Yii::$app->request->isAjax ) {

            if (count($rooms) > 1) {
                if (!empty($_GET['diff'])) {
                    $params = [
                        'rating' => false,
                        'district' => false,
                        'capacity' => false,
                        'price' => false,
                        'price_with_food' => false,
                        'price_without_food' => false,
                        'kitchen' => false,
                        'parking' => false,
                        'outside_registration' => false,
                        'alcohol' => false,
                        'firework' => false,
                        'own_fruits' => false,
                    ];
                    $params = $this->diffParams($params, $rooms);
                }

                return json_encode([
                    'favorites' => $this->renderPartial('//components/generic/favorites.twig', array(
                        'items' => $rooms,
                        'difference' => $params
                    )),
                ]);
            }
        }

        $have_diff = [];

        if (!empty($rooms) and count($rooms) > 0) {
            $params_diff = $this->diffParams($params, $rooms);

            $have_diff = !empty(array_diff($params, $params_diff));
        }

//        var_dump($have_diff);
//        echo $have_diff;

//        echo '1';die();

        $seo['breadcrumbs'] = Breadcrumbs::get_breadcrumbs(2);

        $itemsWidget = new ItemsWidgetElastic;

        $elastic_model = new ElasticItems;
        $filter_model = Filter::find()->with('items')->all();
        $slices_model = Slices::find()->all();

        $apiMain = $itemsWidget->getMain($filter_model, $slices_model, 'rooms', $elastic_model);
        
//        echo '<pre>';
//        print_r($seo);
//        die();

        //временное решение для вставки тайтла (пока нет сео для страницы избранного)
        $this->setSeo('Избранное', '', '');
        

        return $this->render('index.twig', [
//            'seo' => $seo,
            'have_diff' => $have_diff,
            'items' => $rooms,
            'difference' => $params,
            'seo' => $seo,
            'widgets' => $apiMain['widgets'],
            'subdomen' => $subdomen
        ]);
    }

    public function diffParams($params, $rooms)
    {
        $first_room = $rooms[0];
        foreach ($rooms as $room){
            if ($first_room['restaurant_rev_ya']['rate'] !== $room['restaurant_rev_ya']['rate'])
                $params['rating'] = true;

            if ($first_room['restaurant_district'] !== $room['restaurant_district'])
                $params['district'] = true;

            if (
                $first_room['restaurant_min_capacity'] !== $room['restaurant_min_capacity'] or
                $first_room['restaurant_max_capacity'] !== $room['restaurant_max_capacity']
            )
                $params['capacity'] = true;

            if ($first_room['payment_model'] != 3) {
                if ($first_room['price'] !== $room['price'] or $first_room['rent_room_only'] !== $room['rent_room_only'])
                    $params['price_with_food'] = true;
            } else {
                if ($first_room['banquet_price_person'] !== $room['banquet_price_person'] or $first_room['rent_room_only'] !== $room['rent_room_only'])
                    $params['price_without_food'] = true;
            }

            if ($first_room['banquet_price'] !== $room['banquet_price'])
                $params['price'] = true;

            if ($first_room['restaurant_cuisine'] !== $room['restaurant_cuisine'])
                $params['kitchen'] = true;

            if ($first_room['restaurant_parking'] !== $room['restaurant_parking'])
                $params['parking'] = true;

            if ($first_room['outside_registration'] !== $room['outside_registration'])
                $params['outside_registration'] = true;

            if ($first_room['restaurant_alcohol'] !== $room['restaurant_alcohol'])
                $params['alcohol'] = true;

            if ($first_room['restaurant_firework'] !== $room['restaurant_firework'])
                $params['firework'] = true;

            if ($first_room['own_fruits'] !== $room['own_fruits'])
                $params['own_fruits'] = true;
        }

        return $params;
    }


    //ДОБАВЛЕНИЕ В ИЗБРАННОЕ
    public function actionAjaxFavorite($city = '', $room_id, $type) {
        $favorite = Yii::$app->params['subdomen_favorite'];
        $cookie_name = Yii::$app->params['subdomen_favorite_cookie_name'];

        if ($type == 'add') {
            array_push($favorite['items'], $room_id);
        } else {
            $favorite['items'] = array_filter($favorite['items'], function ($item) use ($room_id) {
                return $item != $room_id;
            });
        }
        $favorite['count'] = count($favorite['items']);

        Yii::$app->response->cookies->remove($cookie_name);
        Yii::$app->response->cookies->add(new Cookie([
            'name' => $cookie_name,
            'value' => $favorite,
            'expire' => time() + 3600 * 24 * 30 * 6,
        ]));

        Yii::$app->params['subdomen_favorite'] = $favorite;

        return json_encode(['count' => $favorite['count']]);
    }

    //УДАЛЕНИЕ ИЗ ИЗБРАННОГО
    public function actionAjaxDelFavorites($room_id, $type) {
        $favorite = Yii::$app->params['subdomen_favorite'];
        $cookie_name = Yii::$app->params['subdomen_favorite_cookie_name'];
        //удаляем все заведения из избранного если type == 'all'
        //иначе удаляем одно конкретное
        if ($type == 'all') {
            Yii::$app->response->cookies->remove($cookie_name);
            return json_encode(['count' => 0]);
        } else {
            $favorite['items'] = array_filter($favorite['items'], function ($item) use ($room_id) {
                return $item != $room_id;
            });

            $favorite['count'] = count($favorite['items']);

            Yii::$app->response->cookies->remove($cookie_name);
            Yii::$app->response->cookies->add(new Cookie([
                'name' => $cookie_name,
                'value' => $favorite,
                'expire' => time() + 3600 * 24 * 30 * 6,
            ]));

            Yii::$app->params['subdomen_favorite'] = $favorite;
            return json_encode(['count' => $favorite['count'], 'items' => $favorite['items'], 'room_id' => $room_id]);
        }
    }

    private function setSeo($seo,  $page, $canonical) {
        $this->view->title = $seo;
        $this->view->params['desc'] = $page;
        $this->view->params['kw'] =  $canonical;
    }

}
