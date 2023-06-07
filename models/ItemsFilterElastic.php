<?php

namespace frontend\modules\svadbanaprirode\models;

use yii\base\BaseObject;
use common\models\FilterItems;
use Yii;
use common\models\Filter;
use yii\helpers\ArrayHelper;

use Elasticsearch\ClientBuilder;

class ItemsFilterElastic extends BaseObject{

    public $items,
        $total,
        $pages,
        $query;

    public function __construct($filter_arr = [], $limit = 24, $offset = 0, $widget_flag = false, $main_table, $elastic_model = false, $random = false, $must_not = false, $api_subdomen = false, $console = false, $price_sort = false, $rating_sort = true, $premium = false, $check_sort='') {

        //echo '<pre style="display:none;">';
        //echo '</pre>';

        $filter_main_model = ArrayHelper::map(Filter::find()->all(), 'alias', 'type');

        if(!$console){
            $session = Yii::$app->session;
            if($session->get('seed')){
                $seed = $session->get('seed');
            }
            else{
                $rand_seed = random_int(1, 999999);
                $session->set('seed', $rand_seed);
                $seed = $rand_seed;
            }

            if($widget_flag){
                if($session->get('widget_seed')){
                    $seed = $session->get('widget_seed');
                }
                else{
                    $rand_seed = random_int(1, 999999);
                    $session->set('widget_seed', $rand_seed);
                    $seed = $rand_seed;
                }
            }
        }
        else{
            $seed = 1;
        }

        if($random){
            $seed = random_int(1, 999999);
        }

        if($elastic_model){
            $query = $elastic_model::find();
        }
        elseif($main_table == 'rooms'){
            $query = ItemsElastic::find();
        }
        else{
            $query = RestaurantElastic::find();
            $query->source([
                'id',
                'restaurant_cover_url',
                'restaurant_name',
                'restaurant_address',
                'restaurant_min_capacity',
                'restaurant_max_capacity',
            ]);
        }

        $simple_query = [

        ];

        $nested_query = [

        ];

        $type_query = [

        ];

        $spec_query = [

        ];

        $rooms_spec_query = [

        ];

        $specials_query = [

        ];

        $extra_query = [

        ];

        $location_query = [

        ];

        $metro_query = [

        ];

        foreach ($filter_arr as $key => $value_temp) {

            foreach ($value_temp as $value) {
                if($filter_main_model[$key] != 'input'){
                    $filter_item_obj = FilterItems::find()
                        ->joinWith(['filter'])
                        ->where(['filter.alias' => $key])
                        ->andWhere(['value' => $value])
                        ->one();
                    $filter_item_arr = json_decode($filter_item_obj->api_arr, true);

                    foreach ($filter_item_arr as $filter_data) {

                        $filter_query = new FilterQueryConstructorElastic($filter_data, $main_table);

                        if($filter_query->nested){
                            if(!isset($nested_query[$filter_query->query_type])){
                                $nested_query[$filter_query->query_type] = [];
                            }
                        }
                        elseif($filter_query->type){
                            if(!isset($type_query[$filter_query->query_type])){
                                $type_query[$filter_query->query_type] = [];
                            }
                        }

                        elseif($filter_query->spec){
                            if(!isset($spec_query[$filter_query->query_type])){
                                $spec_query[$filter_query->query_type] = [];
                            }
                        }
                        elseif($filter_query->rooms_spec){
                            if(!isset($rooms_spec_query[$filter_query->query_type])){
                                $rooms_spec_query[$filter_query->query_type] = [];
                            }
                        }
                        elseif($filter_query->specials){
                            if(!isset($specials_query[$filter_query->query_type])){
                                $specials_query[$filter_query->query_type] = [];
                            }
                        }
                        elseif($filter_query->extra){
                            if(!isset($extra_query[$filter_query->query_type])){
                                $extra_query[$filter_query->query_type] = [];
                            }
                        }
                        elseif($filter_query->location){
                            if(!isset($location_query[$filter_query->query_type])){
                                $location_query[$filter_query->query_type] = [];
                            }
                        }
                        elseif($filter_query->metro){
                            if(!isset($metro_query[$filter_query->query_type])){
                                $metro_query[$filter_query->query_type] = [];
                            }
                        }
                        else{
                            if(!isset($simple_query[$filter_query->query_type])){
                                $simple_query[$filter_query->query_type] = [];
                            }
                        }

                        foreach ($filter_query->query_arr as $filter_value) {
                            if($filter_query->nested){
                                array_push($nested_query[$filter_query->query_type], $filter_value);
                            }
                            elseif($filter_query->type){
                                array_push($type_query[$filter_query->query_type], $filter_value);
                            }
                            elseif($filter_query->spec){
                                array_push($spec_query[$filter_query->query_type], $filter_value);
                            }
                            elseif($filter_query->rooms_spec){
                                array_push($rooms_spec_query[$filter_query->query_type], $filter_value);
                            }
                            elseif($filter_query->specials){
                                array_push($specials_query[$filter_query->query_type], $filter_value);
                            }
                            elseif($filter_query->extra){
                                array_push($extra_query[$filter_query->query_type], $filter_value);
                            }
                            elseif($filter_query->location){
                                array_push($location_query[$filter_query->query_type], $filter_value);
                            }
                            elseif($filter_query->metro){
                                array_push($metro_query[$filter_query->query_type], $filter_value);
                            }
                            else{
                                array_push($simple_query[$filter_query->query_type], $filter_value);
                            }
                        }
                    }
                }
                else{
                    $filter_item_obj = FilterItems::find()
                        ->joinWith(['filter'])
                        ->where(['filter.alias' => $key])
                        ->one();
                    $filter_item_arr = json_decode($filter_item_obj->api_arr, true);

                    $filter_item_arr[0]['value'] = $filter_item_arr[0]['value'].$value;

                    foreach ($filter_item_arr as $filter_data) {

                        $filter_query = new FilterQueryConstructorElastic($filter_data, $main_table);

                        if($filter_query->nested){
                            if(!isset($nested_query[$filter_query->query_type])){
                                $nested_query[$filter_query->query_type] = [];
                            }
                        }
                        else{
                            if(!isset($simple_query[$filter_query->query_type])){
                                $simple_query[$filter_query->query_type] = [];
                            }
                        }

                        foreach ($filter_query->query_arr as $filter_value) {
                            $filter_query->nested ?
                                array_push($nested_query[$filter_query->query_type], $filter_value):
                                array_push($simple_query[$filter_query->query_type], $filter_value);
                        }
                    }
                }
            }
        }

        $final_query = [
            'bool' => [
                'must' => [],
            ]
        ];



        if($must_not){
            $final_query['bool']['must_not'] = ['match' => ['id' => $must_not]];
        }

        if(isset(Yii::$app->params['subdomen_id'])){
            $subdomen_id = Yii::$app->params['subdomen_id'];
        }
        elseif($api_subdomen){
            $subdomen_id = $api_subdomen;
        }
        else{
            $subdomen_id = 0;
        }

        if($subdomen_id){
            array_push($final_query['bool']['must'], ['match' => ['restaurant_city_id' => $subdomen_id]]);
        }

        if($premium){
            array_push($final_query['bool']['must'], ['match' => ['restaurant_premium' => 1]]);
        }
        else{
            if(!isset($final_query['bool']['must_not'])){
                $final_query['bool']['must_not'] = ['match' => ['restaurant_premium' => 1]];
            }
        }

        foreach ($simple_query as $type => $arr_filter) {
            $temp_type_arr = [];
            foreach ($arr_filter as $key => $value) {
                array_push($temp_type_arr, $value);
            }
            array_push($final_query['bool']['must'], ['bool' => ['should' => $temp_type_arr]]);
        }

        foreach ($nested_query as $type => $arr_filter) {
            $temp_type_arr = [];
            foreach ($arr_filter as $key => $value) {
                array_push($temp_type_arr, $value);
            }
            if($main_table == 'rooms'){
                array_push($final_query['bool']['must'], ['bool' => ['should' => $temp_type_arr]]);
            }
            else{
                array_push($final_query['bool']['must'], ['nested' => ["path" => "rooms","query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
            }
        }

        foreach ($type_query as $type => $arr_filter) {
            $temp_type_arr = [];
            foreach ($arr_filter as $key => $value) {
                array_push($temp_type_arr, $value);
            }
            if($main_table == 'rooms'){
                array_push($final_query['bool']['must'], ['nested' => ["path" => "restaurant_types","query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
            }
            else{
                array_push($final_query['bool']['must'], ['nested' => ["path" => "restaurant_types","query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
            }
        }

        foreach ($spec_query as $type => $arr_filter) {
            $temp_type_arr = [];
            foreach ($arr_filter as $key => $value) {
                array_push($temp_type_arr, $value);
            }
            if($main_table == 'rooms'){
                array_push($final_query['bool']['must'], ['nested' => ["path" => "restaurant_spec","query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
            }
            else{
                array_push($final_query['bool']['must'], ['nested' => ["path" => "restaurant_spec","query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
            }
        }

        foreach ($rooms_spec_query as $type => $arr_filter) {
            $temp_type_arr = [];
            foreach ($arr_filter as $key => $value) {
                array_push($temp_type_arr, $value);
            }
            if($main_table == 'rooms'){
                array_push($final_query['bool']['must'], ['nested' => ["path" => "room_spec","query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
            }
            else{
                array_push($final_query['bool']['must'], ['nested' => ["path" => "room_spec","query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
            }
        }

        foreach ($specials_query as $type => $arr_filter) {
            $temp_type_arr = [];
            foreach ($arr_filter as $key => $value) {
                array_push($temp_type_arr, $value);
            }
            if($main_table == 'rooms'){
                array_push($final_query['bool']['must'], ['nested' => ["path" => "restaurant_specials","query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
            }
            else{
                array_push($final_query['bool']['must'], ['nested' => ["path" => "restaurant_specials","query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
            }
        }

        foreach ($extra_query as $type => $arr_filter) {
            $temp_type_arr = [];
            foreach ($arr_filter as $key => $value) {
                array_push($temp_type_arr, $value);
            }
            if($main_table == 'rooms'){
                array_push($final_query['bool']['must'], ['nested' => ["path" => "restaurant_extra","query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
            }
            else{
                array_push($final_query['bool']['must'], ['nested' => ["path" => "restaurant_extra","query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
            }
        }

        foreach ($location_query as $type => $arr_filter) {
            $temp_type_arr = [];
            foreach ($arr_filter as $key => $value) {
                array_push($temp_type_arr, $value);
            }
            if($main_table == 'rooms'){
                array_push($final_query['bool']['must'], ['nested' => ["path" => "restaurant_location","query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
            }
            else{
                array_push($final_query['bool']['must'], ['nested' => ["path" => "restaurant_location","query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
            }
        }

        foreach ($metro_query as $type => $arr_filter) {
            $temp_type_arr = [];
            foreach ($arr_filter as $key => $value) {
                array_push($temp_type_arr, $value);
            }
            if($main_table == 'rooms'){
                array_push($final_query['bool']['must'], ['nested' => ["path" => "restaurant_metro_stations","query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
            }
            else{
                array_push($final_query['bool']['must'], ['nested' => ["path" => "restaurant_metro_stations","query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
            }
        }

        if($subdomen_id){
            //echo '<pre>';
            //print_r($type_query);
            //echo '</pre>';
            //exit;
        }



        if($price_sort){
            $final_query = [
                "function_score" => [
                    "query" => $final_query,
                    "functions" => [
                        [
                            "field_value_factor" => [
                                "field" => "restaurant_rating",
                                "factor" => 0.01
                            ],
                            "weight" => 2
                        ],
                        [
                            "filter" => [ "match" => [ "restaurant_commission" => "2" ] ],
                            "weight" => 100
                        ],
                        [
                            "random_score" => [ "seed" => $seed ],
                            "weight" => 1
                        ],
                        [
                            "filter" => [ "range" => [ "restaurant_min_price" => [ "gte" => 100 ] ] ],
                            "random_score" => [ "seed" => $seed ],
                            "weight" => 100
                        ],
                    ]
                ]
            ];
        }
        elseif($rating_sort){
            $final_query = [
                "function_score" => [
                    "query" => $final_query,
                    "score_mode" => "sum",
                    "functions" => [
                        [
                            "field_value_factor" => [
                                "field" => "restaurant_rating",
                                "factor" => 0.01
                            ],
                            "weight" => 2
                        ],
                        [
                            "filter" => [ "match" => [ "restaurant_commission" => "2" ] ],
                            "weight" => 100
                        ],
                        [
                            "random_score" => [ "seed" => $seed ],
                            "weight" => 1
                        ]
                    ]
                ]
            ];
        }
        else{
            $final_query = [
                "function_score" => [
                    "query" => $final_query,
                    "score_mode" => "sum",
                    "functions" => [
                        [
                            "random_score" => [ "seed" => $seed ],
                            "weight" => 1
                        ]
                    ]
                ]
            ];
        }

        $query->query($final_query);

        if (!empty($check_sort))
            $query->orderBy([ 'restaurant_min_check' => $check_sort ]);

        $query->limit($limit)
            ->offset(($offset-1)*$limit);

        $elastic_search = $query->search();

        if(!$widget_flag){
            $this->total = $elastic_search['hits']['total'];
        }

        $this->items = $elastic_search['hits']['hits'];

        $this->pages = ceil($this->total / $limit);

        $this->query = $query;

    }

}