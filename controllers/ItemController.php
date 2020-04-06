<?php
namespace app\modules\svadbanaprirode\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Rooms;
use common\models\elastic\ItemsElastic;
use common\components\Breadcrumbs;
use common\models\elastic\ItemsWidgetElastic;

class ItemController extends Controller
{

	public function actionIndex($id)
	{

		//ItemsElastic::refreshIndex();
		//exit;

		$item = ItemsElastic::get($id);

		//$item = ApiItem::getData($item->restaurants->gorko_id);

		$seo['h1'] = $item->name;
		$seo['breadcrumbs'] = Breadcrumbs::get_breadcrumbs(2);
		$seo['desc'] = $item->restaurant_name;
		$seo['address'] = $item->restaurant_address;

		$itemsWidget = new ItemsWidgetElastic;
		$other_rooms = $itemsWidget->getOther($item->restaurant_id, $id);

		return $this->render('index.twig', array(
			'item' => $item,
			'queue_id' => $id,
			'seo' => $seo,
			'other_rooms' => $other_rooms
		));
	}

}