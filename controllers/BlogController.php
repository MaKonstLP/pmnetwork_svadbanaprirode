<?php
namespace app\modules\svadbanaprirode\controllers;

use common\models\blog\BlogPost;
use common\models\blog\BlogTag;
use common\models\Seo;
use common\models\Filter;
use common\models\Slices;
use frontend\modules\svadbanaprirode\models\ElasticItems;
use frontend\components\Breadcrumbs;
use frontend\modules\svadbanaprirode\models\ItemsWidgetElastic;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\widgets\LinkPager;
use yii\web\Controller;
use Yii;

class BlogController extends Controller
{

  	public function actionIndex(){
	    $query = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true]);
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 100,
				'forcePageParam' => false,
				'totalCount' => $query->count()
			],
		]);

		$seo = (new Seo('blog', $dataProvider->getPagination()->page + 1))->seo;
		$seo['breadcrumbs'] = Breadcrumbs::get_breadcrumbs(1);
		$this->setSeo($seo);



		$topPosts = (clone $query)->where(['featured' => true])->limit(5)->all();

		$listConfig = [
			'dataProvider' => $dataProvider,
			'itemView' => '_list-item.twig',
			'layout' => "{items}\n<div class='pagination_wrapper items_pagination' data-pagination-wrapper>{pager}</div>",
			'pager' => [
				'class' => LinkPager::class,
				'disableCurrentPageButton' => true,
				'nextPageLabel' => 'Следующая →',
				'prevPageLabel' => '← Предыдущая',
				'maxButtonCount' => 4,
				'activePageCssClass' => '_active',
				'pageCssClass' => 'items_pagination_item',
			],

		];
		return $this->render('index.twig', compact('listConfig', 'topPosts', 'seo'));
  	}

  	public function actionPost($alias)
	{
        $elastic_model = new ElasticItems;
        $filter_model = Filter::find()->with('items')->all();
        $slices_model = Slices::find()->all();
        $itemsWidget = new ItemsWidgetElastic;
        $apiMain = $itemsWidget->getMain($filter_model, $slices_model, 'rooms', $elastic_model);

		$post = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true, 'alias' => $alias])->one();
		if(empty($post)) {
			return new NotFoundHttpException();
		}
		$seo = ArrayHelper::toArray($post->seoObject);
		$this->setSeo($seo);
		return $this->render('post.twig', [
            'post' => $post,
            'widgets' => $apiMain['widgets']
        ]);
	}

	public function actionPreview($id)
	{
		$post = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true, 'id' => $id])->one();
		if(empty($post)) {
			return new NotFoundHttpException();
		}
		$seo = ArrayHelper::toArray($post->seoObject);
		$this->setSeo($seo);
		return $this->render('post.twig', compact('post'));
	}

  	private function setSeo($seo)
	{
		$this->view->title = $seo['title'];
		$this->view->params['desc'] = $seo['description'];
		$this->view->params['kw'] = $seo['keywords'];
	}
}