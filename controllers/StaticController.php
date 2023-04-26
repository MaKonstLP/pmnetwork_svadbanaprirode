<?php
namespace app\modules\svadbanaprirode\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use common\models\Pages;
use common\models\Seo;

class StaticController extends Controller
{

	public function actionPrivacy()
	{
		$page = Pages::find()
			->where([
				'type' => 'privacy',
			])
			->one();

		$seo = new Seo('privacy', 1);
        $this->setSeo($seo->seo);

		return $this->render('privacy.twig', [
			'page' => $page,
		]);
	}

    public function actionAdvertising() {
	    $seo['title'] = "Реклама на сайте";
	    $seo['description'] = "Реклама на сайте";
	    $seo['keywords'] = "Реклама на сайте";
        $this->setSeo($seo);
        return $this->render('advertising.twig');
    }

	public function actionRobots()
	{
        header('Content-type: text/plain');
		return "
User-agent: *
Disallow: *district_code=*
Disallow: *gostey=*
Disallow: *area_type=*
Disallow: *y-vody=*
Disallow: *chek=*
Disallow: *na-kryshe=*
Disallow: *?keyword=*
Disallow: *?yhid=*
Disallow: *?from=*
Disallow: */favorites/

Sitemap:  https://svadbanaprirode.com/sitemap/
Sitemap:  https://svadbanaprirode.com/samara/sitemap/
Sitemap:  https://svadbanaprirode.com/kazan/sitemap/
Sitemap:  https://svadbanaprirode.com/spb/sitemap/
Sitemap:  https://svadbanaprirode.com/ekaterinburg/sitemap/
Sitemap:  https://svadbanaprirode.com/krasnodar/sitemap/
Sitemap:  https://svadbanaprirode.com/ufa/sitemap/
Sitemap:  https://svadbanaprirode.com/rostov/sitemap/
Sitemap:  https://svadbanaprirode.com/nn/sitemap/
Sitemap:  https://svadbanaprirode.com/chelyabinsk/sitemap/
		";
		exit;
	}

	private function setSeo($seo){
        $this->view->title = $seo['title'];
        $this->view->params['desc'] = $seo['description'];
        $this->view->params['kw'] = $seo['keywords'];
    }
}