<?php
namespace app\modules\svadbanaprirode\controllers;

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

	public function actionSlice($slice)
	{
		$slice_obj = new QueryFromSlice($slice);
		if($slice_obj->flag){
			$this->view->params['menu'] = $slice;
			$params = $this->parseGetQuery($slice_obj->params, $this->filter_model, $this->slices_model);
			isset($_GET['page']) ? $params['page'] = $_GET['page'] : $params['page'];

			$canonical = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];
			if($params['page'] > 1){
				$canonical .= $params['canonical'];
			}			

			return $this->actionListing(
				$page 			=	$params['page'],
				$per_page		=	$this->per_page,
				$params_filter	= 	$params['params_filter'],
				$breadcrumbs 	=	Breadcrumbs::get_breadcrumbs(2),
				$canonical 		= 	$canonical,
				$type 			=	$slice
			);
		}
		else{
			return $this->goHome();
		}				
	}

	public function actionIndex()
	{
		$getQuery = $_GET;
		unset($getQuery['q']);
		if(count($getQuery) > 0){
			$params = $this->parseGetQuery($getQuery, $this->filter_model, $this->slices_model);
			$canonical = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];
			if($params['page'] > 1){
				$canonical .= $params['canonical'];
			}

			return $this->actionListing(
				$page 			=	$params['page'],
				$per_page		=	$this->per_page,
				$params_filter	= 	$params['params_filter'],
				$breadcrumbs 	=	Breadcrumbs::get_breadcrumbs(1),
				$canonical 		= 	$canonical
			);	
		}
		else{
			$canonical = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];

			return $this->actionListing(
				$page 			=	1,
				$per_page		=	$this->per_page,
				$params_filter	= 	[],
				$breadcrumbs 	= 	Breadcrumbs::get_breadcrumbs(1),
				$canonical 		= 	$canonical
			);
		}
	}

	public function actionListing($page, $per_page, $params_filter, $breadcrumbs, $canonical, $type = false)
	{
		$elastic_model = new ElasticItems;
		$items = new ItemsFilterElastic($params_filter, $per_page, $page, false, 'rooms', $elastic_model);

		if($page > 1){
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
		$seo = $this->getSeo($seo_type, $page, $items->total);
		$seo['breadcrumbs'] = $breadcrumbs;
		$this->setSeo($seo, $page, $canonical);

		//print_r($seo);
		//exit;

		return $this->render('index.twig', array(
			'items' => $items->items,
			'filter' => $filter,
			'pagination' => $pagination,
			'seo' => $seo,
			'count' => $items->total
		));	
	}

	public function actionAjaxFilter(){
		$params = $this->parseGetQuery(json_decode($_GET['filter'], true), $this->filter_model, $this->slices_model);

		$elastic_model = new ElasticItems;
		$items = new ItemsFilterElastic($params['params_filter'], $this->per_page, $params['page'], false, 'rooms', $elastic_model);

		$pagination = PaginationWidget::widget([
			'total' => $items->pages,
			'current' => $params['page'],
		]);

		substr($params['listing_url'], 0, 1) == '?' ? $breadcrumbs = Breadcrumbs::get_breadcrumbs(1) : $breadcrumbs = Breadcrumbs::get_breadcrumbs(2);
		$seo_type = $params['slice_alias'] ? $params['slice_alias'] : 'listing';
		$seo = $this->getSeo($seo_type, $params['page'], $items->total);
		$seo['breadcrumbs'] = $breadcrumbs;

		$title = $this->renderPartial('//components/generic/title.twig', array(
			'seo' => $seo,
			'count' => $items->total
		));

		if($params['page'] == 1){
			$text_top = $this->renderPartial('//components/generic/text.twig', array('text' => $seo['text_top']));
			$text_bottom = $this->renderPartial('//components/generic/text.twig', array('text' => $seo['text_bottom']));
		}
		else{
			$text_top = '';
			$text_bottom = '';
		}

		return  json_encode([
			'listing' => $this->renderPartial('//components/generic/listing.twig', array(
				'items' => $items->items,
				'img_alt' => $seo['img_alt'],
			)),
			'pagination' => $pagination,
			'url' => $params['listing_url'],
			'title' => $title,
			'text_top' => $text_top,
			'text_bottom' => $text_bottom,
		]);
	}

	public function actionAjaxFilterSlice(){
		$slice_url = ParamsFromQuery::isSlice(json_decode($_GET['filter'], true));

		return $slice_url;
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

		$temp_params = new ParamsFromQuery($getQuery, $filter_model, $this->slices_model);

		$return['params_api'] = $temp_params->params_api;
		$return['params_filter'] = $temp_params->params_filter;
		$return['listing_url'] = $temp_params->listing_url;
		$return['canonical'] = $temp_params->canonical;
		return $return;
	}

	private function getSeo($type, $page, $count = 0){
		$seo = new Seo($type, $page, $count);

		return $seo->seo;
	}

	private function setSeo($seo, $page, $canonical){
		$this->view->title = $seo['title'];
		$this->view->params['desc'] = $seo['description'];
		if($page != 1){
			$this->view->params['canonical'] = $canonical;
		}
        $this->view->params['kw'] = $seo['keywords'];
	}

}