<?php
namespace frontend\modules\svadbanaprirode\models;

use Yii;
use common\models\Restaurants;
use common\models\RestaurantsTypes;
use yii\helpers\ArrayHelper;
use common\models\Subdomen;
use common\models\RoomsUniqueId;
use common\models\RestaurantsSpec;
use common\models\RestaurantsSpecial;
use common\models\RestaurantsExtra;
use common\models\RestaurantsLocation;
use common\models\ImagesModule;
use common\models\DistrictGlobal;
use common\components\AsyncRenewImages;

class ElasticItems extends \yii\elasticsearch\ActiveRecord
{
    public function attributes()
    {
        return [
            'restaurant_id',
            'restaurant_gorko_id',
            'restaurant_price',
            'restaurant_city_id',
            'restaurant_min_capacity',
            'restaurant_max_capacity',
            'restaurant_district',
            'restaurant_region',
            'restaurant_parent_district',
            'restaurant_alcohol',
            'restaurant_firework',
            'restaurant_name',
            'restaurant_address',
            'restaurant_cover_url',
            'restaurant_latitude',
            'restaurant_longitude',
            'restaurant_own_alcohol',
            'restaurant_cuisine',
            'restaurant_parking',
            'restaurant_extra_services',
            'restaurant_actual_extra_services',
            'restaurant_payment',
            'restaurant_special',
            'restaurant_actual_special',
            'restaurant_phone',
            'restaurant_location',
            'restaurant_location_voda',
            'restaurant_location_city',
            'restaurant_commission',
            'restaurant_rating',
            'restaurant_type',
            'kotteg',
            'baza',
            'id',
            'gorko_id',
            'restaurant_id',
            'price',
            'capacity_reception',
            'capacity',
            'type',
            'city_id',
            'alias',
            'alias_rus',
            'rent_only',
            'rent_room_only',
            'banquet_price',
            'banquet_price_min',
            'banquet_price_person',
            'bright_room',
            'separate_entrance',
            'type_name',
            'name',
            'features',
            'cover_url',
            'images',
            'unique_id',
            'description',
            'outside_registration',
            'restaurant_rev_ya',
            'payment_model',
            'own_fruits',
            'own_ba_drinks',
        ];
    }

    public static function index() {
        return 'pmn_sp_rooms';
//        return 'pmn_dev_1';
    }
    
    public static function type() {
        return 'items';
    }

    /**
     * @return array This model's mapping
     */
    public static function mapping()
    {
        return [
            static::type() => [
                'properties' => [
                    'restaurant_id'                    => ['type' => 'integer'],
                    'restaurant_gorko_id'              => ['type' => 'integer'],
                    'restaurant_price'                 => ['type' => 'integer'],
                    'restaurant_min_capacity'          => ['type' => 'integer'],
                    'restaurant_max_capacity'          => ['type' => 'integer'],
                    'restaurant_district'              => ['type' => 'integer'],
                    'restaurant_parent_district'       => ['type' => 'integer'],
                    'restaurant_alcohol'               => ['type' => 'integer'],
                    'restaurant_firework'              => ['type' => 'integer'],
                    'restaurant_city_id'               => ['type' => 'integer'],
                    'restaurant_name'                  => ['type' => 'text'],
                    'own_fruits'                       => ['type' => 'text'],
                    'own_ba_drinks'                    => ['type' => 'text'],
                    'restaurant_address'               => ['type' => 'text'],
                    'restaurant_region'                => ['type' => 'text'],
                    'restaurant_cover_url'             => ['type' => 'text'],
                    'restaurant_latitude'              => ['type' => 'text'],
                    'restaurant_longitude'             => ['type' => 'text'],
                    'restaurant_own_alcohol'           => ['type' => 'text'],
                    'restaurant_cuisine'               => ['type' => 'text'],
                    'restaurant_parking'               => ['type' => 'text'],
                    'restaurant_extra_services'        => ['type' => 'text'],
                    'restaurant_actual_extra_services'        => ['type' => 'text'],
                    'restaurant_payment'               => ['type' => 'text'],
                    'restaurant_special'               => ['type' => 'text'],
                    'restaurant_actual_special'               => ['type' => 'text'],
                    'restaurant_phone'                 => ['type' => 'text'],
                    'restaurant_location'              => ['type' => 'nested', 'properties' =>[
                        'id'                                => ['type' => 'integer'],
                        'text'                              => ['type' => 'text'],
                    ]],
                    'restaurant_location_voda'         => ['type' => 'text'],
                    'restaurant_location_city'         => ['type' => 'text'],
                    'restaurant_type'              => ['type' => 'nested', 'properties' =>[
                        'id'                                => ['type' => 'integer'],
                    ]],
                    'unique_id'                        => ['type' => 'integer'],
                    'restaurant_commission'            => ['type' => 'integer'],
                    'restaurant_rating'                => ['type' => 'integer'],
                    'kotteg'                           => ['type' => 'integer'],
                    'baza'                             => ['type' => 'integer'],
                    'id'                               => ['type' => 'integer'],
                    'gorko_id'                         => ['type' => 'integer'],
                    'price'                            => ['type' => 'integer'],
                    'capacity_reception'               => ['type' => 'integer'],
                    'capacity'                         => ['type' => 'integer'],
                    'type'                             => ['type' => 'integer'],
                    'rent_only'                        => ['type' => 'integer'],
                    'rent_room_only'                   => ['type' => 'integer'],
                    'banquet_price'                    => ['type' => 'integer'],
                    'banquet_price_min'                => ['type' => 'integer'],
                    'banquet_price_person'             => ['type' => 'integer'],
                    'bright_room'                      => ['type' => 'integer'],
                    'city_id'                          => ['type' => 'integer'],
                    'separate_entrance'                => ['type' => 'integer'],
                    'outside_registration'             => ['type' => 'integer'],
                    'payment_model'                    => ['type' => 'integer'],
                    'type_name'                        => ['type' => 'text'],
                    'alias'                            => ['type' => 'text'],
                    'alias_rus'                            => ['type' => 'text'],
                    'name'                             => ['type' => 'text'],
                    'features'                         => ['type' => 'text'],
                    'cover_url'                        => ['type' => 'text'],
                    'description'                      => ['type' => 'text'],
                    'restaurant_rev_ya'             => ['type' => 'nested', 'properties' => [
                        'id'                            => ['type' => 'long'],
                        'rate'                          => ['type' => 'text'],
                        'count'                         => ['type' => 'text'],
                    ]],
                    'images'                           => ['type' => 'nested', 'properties' =>[
                        'id'                               => ['type' => 'integer'],
                        'sort'                             => ['type' => 'integer'],
                        'realpath'                         => ['type' => 'text'],
                        'subpath'                          => ['type' => 'text'],
                        'waterpath'                        => ['type' => 'text'],
                        'timestamp'                        => ['type' => 'text'],
                    ]]

                ]
            ],
        ];
    }

    /**
     * Set (update) mappings for this model
     */
    public static function updateMapping()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->setMapping(static::index(), static::type(), static::mapping());
    }

    /**
     * Create this model's index
     */
    public static function createIndex()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->createIndex(static::index(), [
            'settings' => [
                'number_of_replicas' => 0,
                'number_of_shards' => 1,
            ],
            'mappings' => static::mapping(),
        ]);
    }

    /**
     * Delete this model's index
     */
    public static function deleteIndex()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->deleteIndex(static::index(), static::type());
    }

    public static function refreshIndex($params) {
        $res = self::deleteIndex();
        $res = self::updateMapping();
        $res = self::createIndex();
        $res = self::updateIndex($params);
    }

    public static function updateIndex($params) {
        $connection = new \yii\db\Connection($params['main_connection_config']);
        $connection->open();
        Yii::$app->set('db', $connection);

        $restaurants_types = RestaurantsTypes::find()
            ->limit(100000)
            ->asArray()
            ->all();
        $restaurants_types = ArrayHelper::index($restaurants_types, 'value');

        $restaurants_specials = RestaurantsSpecial::find()
            ->limit(100000)
            ->asArray()
            ->all();
        $restaurants_specials = ArrayHelper::index($restaurants_specials, 'value');

        $restaurants_extra = RestaurantsExtra::find()
            ->limit(100000)
            ->asArray()
            ->all();
        $restaurants_extra = ArrayHelper::index($restaurants_extra, 'value');

        $restaurants_spec = RestaurantsSpec::find()
            ->limit(100000)
            ->asArray()
            ->all();
        $restaurants_spec = ArrayHelper::index($restaurants_spec, 'id');

        $restaurants_location = RestaurantsLocation::find()
            ->limit(100000)
            ->asArray()
            ->all();
        
        $restaurants_location = ArrayHelper::index($restaurants_location, 'value');

//        echo "<pre>";
//        print_r($restaurants_location);
//        die();

        $restaurants_region = DistrictGlobal::find()
            ->limit(100000)
            ->asArray()
            ->all();
        $restaurants_region = ArrayHelper::index($restaurants_region, 'id');


        $restaurants = Restaurants::find()
            ->with('rooms')
            ->with('imagesext')
            ->with('subdomen')
            ->with('yandexReview')
            ->where(['active' => 1])
            ->limit(100000)
            ->all();

        $connection = new \yii\db\Connection($params['site_connection_config']);
        $connection->open();
        Yii::$app->set('db', $connection);

        $images_module = ImagesModule::find()
            ->limit(500000)
            ->asArray()
            ->all();
        $images_module = ArrayHelper::index($images_module, 'gorko_id');

        $rooms_unique_id = RoomsUniqueId::find()
            ->limit(100000)
            ->asArray()
            ->all();
        $rooms_unique_id = ArrayHelper::index($rooms_unique_id, 'id');

        foreach ($restaurants as $restaurant) {
            foreach ($restaurant->rooms as $room) {
                $res = self::addRecord($room, $restaurant, $restaurants_types, $restaurants_spec, $restaurants_specials ,$restaurants_extra, $restaurants_location, $restaurants_region, $images_module, $rooms_unique_id, $params);
            }            
        }
        echo 'Обновление индекса '. self::index().' '. self::type() .' завершено<br>';
    }

    public static function softRefreshIndex() {
        $restaurants = Restaurants::find()
            ->with('rooms')
            ->limit(100000)
            ->where(['in_elastic' => 0, 'active' => 1])
            ->all();
        foreach ($restaurants as $restaurant) {
            foreach ($restaurant->rooms as $room) {
                $res = self::addRecord($room, $restaurant);
            }  

            $restaurant->in_elastic = 1;
            $restaurant->save();
        }
        echo 'Обновление индекса '. self::index().' '. self::type() .' завершено<br>';
    }

    public static function addRecord($room, $restaurant, $restaurants_types, $restaurants_spec, $restaurants_specials ,$restaurants_extra, $restaurants_location, $restaurants_region, $images_module, $rooms_unique_id, $params){
        if(!$restaurant->top){
            return 'Не абонент';
        }

        $restaurant_spec_white_list = [1];
        $restaurant_spec_rest = explode(',', $restaurant->restaurants_spec);
        if (count(array_intersect($restaurant_spec_white_list, $restaurant_spec_rest)) === 0) {
            return 'Неподходящий тип мероприятия';
        }

        $restaurant_type_white_list = [30, 11, 17, 14];
        $restaurant_type = explode(',', $restaurant->type);
        if (count(array_intersect($restaurant_type_white_list, $restaurant_type)) === 0) {
            return 'Неподходящий тип площадки';
        }

        $isExist = false;
        try{
            $record = self::get($room->gorko_id);
            if(!$record){
                $record = new self();
                $record->setPrimaryKey($room->gorko_id);
            }
            else{
                $isExist = true;
            }
        }
        catch(\Exception $e){
            $record = new self();
            $record->setPrimaryKey($room->gorko_id);
        }        

        $record->id  = $room->id;

        $record->restaurant_id = $restaurant->id;
//        $record->restaurant_gorko_id = $restaurant->gorko_id;
        $record->restaurant_gorko_id = $room->restaurant_id;
        $record->restaurant_price = $restaurant->price;
        $record->restaurant_district = $restaurant->district;
        $record->restaurant_region = !empty($restaurants_region[$restaurant->district]['name_short']) ? $restaurants_region[$restaurant->district]['name_short'] : '';
        $record->restaurant_parent_district = $restaurant->parent_district;
        $record->restaurant_alcohol = $restaurant->alcohol;
        $record->restaurant_firework = $restaurant->firework;
        $record->restaurant_name = $restaurant->name;
        $record->restaurant_address = $restaurant->address;
        $record->restaurant_cover_url = $restaurant->cover_url;
        $record->restaurant_latitude = $restaurant->latitude;
        $record->restaurant_longitude = $restaurant->longitude;
        $record->restaurant_own_alcohol = $restaurant->own_alcohol;
        $record->restaurant_cuisine = $restaurant->cuisine;
        $record->restaurant_parking = $restaurant->parking;
        $record->restaurant_extra_services = $restaurant->extra_services;
        $record->restaurant_payment = $restaurant->payment;
        $record->restaurant_special = $restaurant->special;
        $record->restaurant_phone = $restaurant->phone;
        $record->restaurant_city_id = $restaurant->city_id;
        $record->city_id = $restaurant->city_id;
//        $record->alias = $restaurant->alias;
        $record->restaurant_commission = $restaurant->commission;
        $restaurant->rating ? $record->restaurant_rating = $restaurant->rating : $record->restaurant_rating = 90;

        $record->outside_registration = 0;
        $restaurant_special_rest = explode(',', $restaurant->special_ids);
        foreach ($restaurant_special_rest as $key => $value) {
            if ($value == '41')
                $record->outside_registration = 1;
        }

        $subdomens = Subdomen::find()->all();
        foreach ($subdomens as $key => $subdomen) {
            if ($subdomen['city_id'] == $restaurant->city_id) {
                $record->alias = $subdomen->alias;
                $record->alias_rus = $subdomen->name;
            }
        }

        //Отзывы с Яндекса из общей базы
        $reviews = [];
        if (isset($restaurant->yandexReview)) {
            $reviews['id'] = $restaurant->yandexReview['rev_ya_id'];
            $reviews['rate'] = $restaurant->yandexReview['rev_ya_rate'];
            $reviews['count'] = $restaurant->yandexReview['rev_ya_count'];
        }
        $record->restaurant_rev_ya = $reviews;



//        $rests_properties = [];
//
//        foreach ($items->items as $key => $item) {
//            $rests_properties[$item['id']][] =  explode(', ', $items->items[$key]['restaurant_cuisine']);
//            $rests_properties[$item['id']][] =  explode(', ', $items->items[$key]['restaurant_extra_services']);
//            $rests_properties[$item['id']][] =  explode(', ', $items->items[$key]['restaurant_special']);
//        }

        $extra = explode(', ', $restaurant->extra_services);
        $special = explode(', ', $restaurant->special);

//        $temp_arr = [];
//        foreach ($extra as $key => $item) {
//            if ($item == 'Фейерверк') {
//                $temp_arr[] = $item;
//                break;
//          }
//        }
//        $extra = implode(', ', $temp_arr);
//        $record->restaurant_actual_extra_services = $extra;

        $own_fruits = '';
        $own_ba_drinks = '';
        $temp_arr = [];
        foreach ($special as $key => $item) {
            if ($item == 'Выездная регистрация' or $item == 'Велком зона' or $item == 'Сцена' or $item == 'Музыкальное оборудование') {
                $temp_arr[] = $item;
            }

            if ($item == 'Можно свои фрукты') {
                $own_fruits = $item;
            }

            if ($item == 'Можно свои б/а напитки') {
                $own_ba_drinks = $item;
            }
        }
        $special = implode(', ', $temp_arr);
        $record->restaurant_actual_special = $special;
        $record->own_fruits = $own_fruits;
        $record->own_ba_drinks = $own_ba_drinks;

        //Тип локации
        $restaurant_location = [];
        $restaurant_location_rest = explode(',', $restaurant->location);
        
        foreach ($restaurant_location_rest as $key => $value) {
            if(!empty($restaurants_location[$value])){
                $restaurant_location_arr = [];
                $restaurant_location_arr['id'] = $value;
                $restaurant_location_arr['text'] = $restaurants_location[$value]['text'];
                array_push($restaurant_location, $restaurant_location_arr);
            }
        }
        $record->restaurant_location = $restaurant_location;

        $flag_voda = true;
        $flag_city = true;
        foreach ($restaurant_location as $key => $item) {
            //1 - Около моря
            //2 - Около реки
            //7 - Около озера
            if ($flag_voda && ($item['id'] == 1 || $item['id'] == 2 || $item['id'] == 7)) {
                $record->restaurant_location_voda = $item['text'];
                $flag_voda = false;
            }

            //4 - В городе
            //5 - В центре города
            //6 - За городом
            if ($flag_city && ($item['id'] == 4 || $item['id'] == 5 || $item['id'] == 6)) {
                $record->restaurant_location_city = $item['text'];
                $flag_city = false;
            }
        }


        //Тип рест
        $restaurant_type = [];
        $baza = 0;
        $kotteg = 0;
        $restaurant_type_rest = explode(',', $restaurant->type);
        foreach ($restaurant_type_rest as $key => $value){
            $baza = $value == 13 ? 1 : $baza;
            $kotteg = $value == 15 ? 1 : $kotteg;
            $restaurant_type[]['id'] = $value;
            $record->restaurant_type = $restaurant_type;
        }
        $record->restaurant_type = $restaurant_type;
        $record->baza = $baza;
        $record->kotteg = $kotteg;

        $record->id = $room->id;
        $record->gorko_id = $room->gorko_id;
        $record->restaurant_id = $room->restaurant_id;
        $record->price = $room->price;
        $record->capacity_reception = $room->capacity_reception;
        $record->restaurant_min_capacity = $room->capacity_min;
        $record->restaurant_max_capacity = $room->capacity;
        $record->capacity = $room->capacity;
        $record->type = $room->type;
        $record->rent_only = $room->rent_only;
        $record->rent_room_only = $room->rent_room_only;
        $record->banquet_price = $room->banquet_price;
        $record->banquet_price_min = $room->banquet_price_min;
        $record->banquet_price_person = $room->banquet_price_person;
        $record->bright_room = $room->bright_room;
        $record->separate_entrance = $room->separate_entrance;
        $record->type_name = $room->type_name;
        $record->name = self::normalizeRegName($room->name);
        $record->features = $room->features;
        $record->cover_url = $room->cover_url;

        $record->payment_model = $room->payment_model;

        //Уникальные св-ва для залов в модуле
        if(isset($rooms_unique_id[$room->gorko_id]) && $rooms_unique_id[$room->gorko_id]['unique_id']){
            $record->unique_id = $rooms_unique_id[$room->gorko_id]['unique_id'];
        }
        else{
            $rooms_unique_id_upd = new RoomsUniqueId();
            $new_id = RoomsUniqueId::find()->max('unique_id') + 1;
            $rooms_unique_id_upd->unique_id = $new_id;
            $rooms_unique_id_upd->id = $room->gorko_id;
            $rooms_unique_id_upd->save();
            $record->unique_id = $new_id;
        }
        //Картинки залов
        $images = [];
        $group = array();
        foreach ($restaurant->imagesext as $value) {
            $group[$value['room_id']][] = $value;
        }
        $images_sorted = array();
        $room_ids = array();
        foreach ($group as $room_id => $images_ext) {
            $room_ids[] = $room_id;
            foreach($images_ext as $image){
                $images_sorted[$room_id][$image['event_id']][] = $image;    
            }       
        }
        $specs = [1, 0];
        $image_flag = false;
        foreach ($specs as $spec) {
            for ($i=0; $i < 20; $i++) {
                if(isset($images_sorted[$room->gorko_id]) && isset($images_sorted[$room->gorko_id][$spec]) && isset($images_sorted[$room->gorko_id][$spec][$i])){
                    $image = $images_sorted[$room->gorko_id][$spec][$i];
                    $image_arr = [];
                    $image_arr['id'] = $image['gorko_id'];
                    $image_arr['sort'] = $image['sort'];
                    $image_arr['realpath'] = str_replace('lh3.googleusercontent.com', 'img.svadbanaprirode.com', $image['path']);;
                        if(isset($images_module[$image['gorko_id']])){
                            $image_arr['subpath']   = str_replace('lh3.googleusercontent.com', 'img.svadbanaprirode.com', $images_module[$image['gorko_id']]['subpath']);
                            $image_arr['waterpath'] = str_replace('lh3.googleusercontent.com', 'img.svadbanaprirode.com', $images_module[$image['gorko_id']]['waterpath']);
                            $image_arr['timestamp'] = str_replace('lh3.googleusercontent.com', 'img.svadbanaprirode.com', $images_module[$image['gorko_id']]['timestamp']);
                        }
                    else{
                        $queue_id = Yii::$app->queue->push(new AsyncRenewImages([
                            'gorko_id'      => $image['gorko_id'],
                            'params'        => $params,
                            'rest_flag'     => false,
                            'rest_gorko_id' => $restaurant->gorko_id,
                            'room_gorko_id' => $room->gorko_id,
                            'elastic_index' => static::index(),
                            'elastic_type'  => 'room',
                        ]));
                    }                
                    array_push($images, $image_arr);
                }
                if(count($images) > 19){
                    $image_flag = true;
                    break;
                }
            }
            if($image_flag) break;
        }
        if(count($images) == 0)
            return "Нет картинок";

        $record->images = $images;

        
        try{
            if(!$isExist){
                $result = $record->insert();
            }
            else{
                $result = $record->update();
            }
        }
        catch(\Exception $e){
            $result = false;
        }
        
        return $result;
    }

    /**
     * Приводит строку к виду Xxx xxx «Xxx xxx» xxx
     * т.е. первая буква в выражении и первая в кавычках - заглавные, а остальные прописные
     * @param  $string String Входная строка
     * @return String
     */
    public static function normalizeRegName($string) {
        $string = mb_strtolower($string);
        $arr = preg_split('/(«|"|»)/', $string);

        foreach( $arr as $key=>$val )
            $arr[$key] = trim(mb_strtoupper(mb_substr($val, 0, 1), 'UTF-8').mb_substr($val, 1, strlen($val)));

        if (!empty($arr[1]))
            $arr[1] = '«'.$arr[1].'»';

        return implode(' ', $arr);
    }

    public static function updateDocument($data, $id, $options = [])
    {
        $db = static::getDb();
        $command = $db->createCommand();
        if ($command->exists(static::index(), static::type(), $id)) {
            $options['retry_on_conflict'] = 3;
            $command->update(static::index(), static::type(), $id, $data, $options);
        }

        gc_collect_cycles();
    }
}