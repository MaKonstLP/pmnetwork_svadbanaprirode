<?php

namespace frontend\modules\svadbanaprirode\models;

use Yii;
use yii\base\Model;
use common\components\QueryFromSlice;
use common\models\WidgetMain;
use common\components\ParamsFromQuery;
use common\models\FilterItems;
use common\models\elastic\ItemsFilterElastic;

class ItemsWidgetElastic extends Model
{

	public function getMain($filter_model, $slices_model, $main_table, $elastic_model = false){

		$widgets = WidgetMain::find()->with('slice')->all();

	    foreach ($widgets as $key => $widget) {

	    	$slice_obj = new QueryFromSlice($widget->slice->alias);
	    	$params = $this->parseGetQuery($slice_obj->params, $filter_model, $slices_model);

	    	$object = new ItemsFilterElastic($params['params_filter'], 8, 1, true, $main_table, $elastic_model);

	    	if(count($object->items) > 0){
	    		$widget->items = $object->items;
	    	}
	    	else{
	    		unset($widgets[$key]);
	    	}
	    }
	    
	    if($elastic_model){
			$total = $elastic_model::find(['id'])->limit(1)->search();
	    }
	    else{
	    	switch ($main_table) {
				case 'restaurants':
					$total = Restaurants::find()->count();
					break;

				case 'rooms':
					$total = ItemsElastic::find(['id'])->limit(1)->search();
					break;
				
				default:
					$total = Restaurants::find()->count();
					break;
			}
	    }		    

	    //exit;

		return [
			'total' => $total['hits']['total'],
			'filter' => false,
			'widgets' => $widgets,
		];
	}

	public function getOther($restaurant_id, $room_id, $elastic_model = false){
		if($elastic_model){
			$items = $elastic_model::find()->query([
			    'bool' => [
			        'must' => [
			            ['match' => ['restaurant_id' => $restaurant_id]]
			        ],
			        'must_not' => [
			            ['match' => ['_id' => $room_id]]
			        ],
			    ],
			])->all();

			return $items;
		}
		else{
			$items = ItemsElastic::find()->query([
			    'bool' => [
			        'must' => [
			            ['match' => ['restaurant_id' => $restaurant_id]]
			        ],
			        'must_not' => [
			            ['match' => ['_id' => $room_id]]
			        ],
			    ],
			])->all();

			return $items;
		}			
	}

	public function getSimilar($item, $main_table, $elastic_model = false){
		$params = $this->getParamsByItem($item);

		$items = new ItemsFilterElastic($params, 5, 1, true, $main_table, $elastic_model, true, $item->id);

		return $items->items;
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

		//print_r($getQuery);
		//exit;

		$temp_params = new ParamsFromQuery($getQuery, $filter_model, $slices_model);

		$return['params_api'] = $temp_params->params_api;
		$return['params_filter'] = $temp_params->params_filter;
		$return['listing_url'] = $temp_params->listing_url;
		$return['seo'] = $temp_params->seo;
		return $return;
	}

	private function getParamsByItem($item){
		$params = [];

		if($item->restaurant_district == 547 || $item->restaurant_parent_district == 547){
			$params['district_code'] = [1];
		}
		else{
			$params['district_code'] = [2];
		}

		$filter_gostey = FilterItems::find()
			->where(['filter_id' => 4])
			->all();

		foreach ($filter_gostey as $key => $value) {
			$filter = json_decode($value->api_arr, true);
			$filter_str = $filter[0]['value'];

			switch (substr($filter_str, 0, 1)) {
				case '<':
					if($item->capacity < str_replace('<', '', $filter_str))
						$params['gostey'] = [$value->value];
					break;
				case '&':
					$value_arr = explode(',', substr($filter_str, 1));
					if(in_array($item->capacity, range($value_arr[0], $value_arr[1])))
						$params['gostey'] = [$value->value];
					break;
				case '>':
					if($item->capacity > str_replace('>', '', $filter_str))
						$params['gostey'] = [$value->value];
					break;
				default:
					$params['gostey'] = [1];
					break;
			}
		}		

		return $params;
	}
}