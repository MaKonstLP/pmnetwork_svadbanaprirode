<?php
namespace app\modules\svadbanaprirode\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\helpers\Html;
use common\components\GorkoLeadApi;
use frontend\modules\svadbanaprirode\models\ElasticItems;

class FormController extends Controller
{
    public function beforeAction($action){
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionSend()
    {
        $payload = [];

        if(isset($_POST['name']))
            $payload['name'] = $_POST['name'];
        if(isset($_POST['phone']))
            $payload['phone'] = $_POST['phone'];
        if(isset($_POST['connection']))
            $payload['connection'] = $_POST['connection'];
        if(isset($_POST['count']))
            $payload['guests'] = intval($_POST['count']);
        if(isset($_POST['date']))
            $payload['date'] = $_POST['date'];
        if(isset($_POST['venue_id']))
            $payload['venue_id'] = $_POST['venue_id'];
        $payload['details'] = '';
        if(isset($_POST['water']) || isset($_POST['tent']) || isset($_POST['country']) || isset($_POST['incity']) || isset($_POST['connection']) ){
            $payload['details'] .= 'Клиент просит связаться с ним через: ';
            if(isset($_POST['connection']))
                $payload['details'] .= $_POST['connection']."%0A";
            $payload['details'] .= 'Клиент просит подобрать зал по условиям: ';
            if(isset($_POST['water']))
                $payload['details'] .= ' у воды; ';
            if(isset($_POST['tent']))
                $payload['details'] .= ' с шатром; ';
            if(isset($_POST['country']))
                $payload['details'] .= ' за городом; ';
            if(isset($_POST['incity']))
                $payload['details'] .= ' в черте города; ';
        }
        if(isset($_POST['type']) && $_POST['type'] == 'item')
            $payload['details'] .= ' Клиент сделал заявку на конкретный зал - '.$_POST['url'];
        if(isset($_POST['url']))
            $payload['details'] .= ' Заявка отправлена с '.$_POST['url'];

        $payload['event_type'] = "Wedding";
        $payload['city_id'] = Yii::$app->params['subdomen_id'];

        $this->SendTg(false, $payload);

        $resp = GorkoLeadApi::send_lead('v.gorko.ru', 'svadbanaprirode', $payload);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $resp;
    }

    public function actionSendFavorites() {
        $payload = [];
        $favoriteCookie = Yii::$app->request->cookies->get(Yii::$app->params['subdomen_favorite_cookie_name']);
        $itemsInCookie = $favoriteCookie->value['items'];

        $namesItems = [];
        foreach ($itemsInCookie as $item) {
            $elastic_model = new ElasticItems;
            $item = $elastic_model::find()
                ->query(['bool' => ['must' => ['match' => ['unique_id' => $item]]]])
                ->limit(1)
                ->search();
            $item = $item['hits']['hits'][0];
            $namesItems[] = $item['restaurant_gorko_id'].' - '.$item['gorko_id'].' - '.$item['alias_rus'];
        }

        $listCookie = '';
        foreach ($namesItems as $number => $name) {
            $listCookie .= "%0A".++$number.'. '.$name;
        }

        if(isset($_POST['name']))
            $payload['name'] = $_POST['name'];
        if(isset($_POST['phone']))
            $payload['phone'] = $_POST['phone'];
        if(isset($_POST['connection']))
            $payload['connection'] = $_POST['connection'];
        if(isset($_POST['count']))
            $payload['guests'] = intval($_POST['count']);
        if(isset($_POST['date']))
            $payload['date'] = $_POST['date'];
        if(isset($_POST['venue_id']))
            $payload['venue_id'] = $_POST['venue_id'];
        $payload['details'] = '';
        if(isset($_POST['water']) || isset($_POST['tent']) || isset($_POST['country']) || isset($_POST['incity']) || isset($_POST['connection']) ){
            $payload['details'] .= 'Клиент просит связаться с ним через: ';
            if(isset($_POST['connection']))
                $payload['details'] .= $_POST['connection']."%0A";
            $payload['details'] .= 'Клиент просит подобрать зал по условиям: ';
            if(isset($_POST['water']))
                $payload['details'] .= ' у воды; ';
            if(isset($_POST['tent']))
                $payload['details'] .= ' с шатром; ';
            if(isset($_POST['country']))
                $payload['details'] .= ' за городом; ';
            if(isset($_POST['incity']))
                $payload['details'] .= ' в черте города; ';

            $payload['details'] .= "%0A".'Клиент добавил в избранное (id ресторана - id зала - город):'.$listCookie."%0A";
        }
        if(isset($_POST['type']) && $_POST['type'] == 'item')
            $payload['details'] .= ' Клиент сделал заявку на конкретный зал - '.$_POST['url'];
        if(isset($_POST['url']))
            $payload['details'] .= ' Заявка отправлена с '.$_POST['url'];

        $payload['event_type'] = "Wedding";
        $payload['city_id'] = Yii::$app->params['subdomen_id'];
        
        $this->SendTg(true, $payload);

        $resp = GorkoLeadApi::send_lead('v.gorko.ru', 'svadbanaprirode', $payload);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $resp;
    }

    public function actionRoom() {
        $elastic_model = new ElasticItems;
        $item = $elastic_model::find()
            ->query(['bool' => ['must' => ['match'=>['unique_id' => $_POST['room_id']]]]])
            ->limit(1)
            ->search();

        $item = $item['hits']['hits'][0];

        $to   = $_POST['book_email'];
        $from = Yii::$app->params['senderEmail'];
        $sub = 'Информация о месте для свадьбы - '.$item['restaurant_name'];

        $msg  = $this->renderPartial('//emails/roominfo.twig', array(
//            'url' => Yii::$app->params['subdomen_alias'] ? 'https://'.Yii::$app->params['subdomen_alias'].'.svadbanaprirode_dev.com/catalog/'.$_POST['room_id'].'/'  : 'http://svadbanaprirode_dev.com/catalog/'.$_POST['room_id'].'/',
            'url' => 'https://svadbanaprirode.com/'.$_POST['city_link'].'catalog/'.$item['unique_id'].'/',
            'item' => $item,
//            'link' => Yii::$app->params['subdomen_alias'] ? 'https://'.Yii::$app->params['subdomen_alias'].'.svadbanaprirode_dev.com' : 'http://svadbanaprirode_dev.com'
        ));

        if (isset($_POST['book_email'])) {
            $message  = Yii::$app->mailer->compose()
                ->setFrom($from)
                ->setTo($to)
                ->setSubject($sub)
                ->setCharset('utf-8')
                ->setHtmlBody($msg.'.');

            if (count($_FILES) > 0) {
                foreach ($_FILES['files']['tmp_name'] as $k => $v) {
                    $message->attach($v, ['fileName' => $_FILES['files']['name'][$k]]);
                }
            }

            $message->send();
        }

        $payload = [];
        $payload['details'] = '';

        if(isset($_POST['type']))
            $payload['details'] .= ' Клиент сделал заявку на конкретный зал - '.$_POST['url'];
        if(isset($_POST['url']))
            $payload['details'] .= 'Заявка отправлена с '.$_POST['url'];
        if (isset($_POST['room_id']))
            $payload['room_id'] = $_POST['room_id'];
        if (isset($_POST['book_email']))
            $payload['email'] = $_POST['book_email'];

        $resp = GorkoLeadApi::send_lead('v.gorko.ru', 'svadbanaprirode', $payload);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $resp;
    }

    public function actionAdvertisingSend() {

        $from = Yii::$app->params['senderEmail'];
//        $to = 'anonim050900@gmail.com';
        $to = ['svadba.naprirode@yandex.ru', 'ab@liderpoiska.ru'];
        $sub = 'Заявка со страницы рекламы на svadbanaprirode.com';

        $msg = '';
        $resp = [];
        $resp['name'] = $_POST['name'];
        $resp['phone'] = $_POST['phone'];

        if(isset($_POST['name']))
            $msg.='Имя: '.$_POST['name'].'<br>';
        if(isset($_POST['position']))
            $msg.='Должность: '.$_POST['position'].'<br>';
        if(isset($_POST['phone']))
            $msg.='Номер: '.$_POST['phone'].'<br>';
        if(isset($_POST['name_room']))
            $msg.='Название площадки: '.$_POST['name_room'].'<br>';
        if(isset($_POST['city']))
            $msg.='Город: '.$_POST['city'].'<br>';

        $message  = Yii::$app->mailer->compose()
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($sub)
            ->setCharset('utf-8')
            ->setHtmlBody($msg);

        $message->send();

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $resp;
    }

    public function SendTg($isFavorite, $payload) {

        $chat_id = '-975156926';
        $token = '5991590602:AAF_0pOHAL735TtwT0m0vzcmfxLv73pUCUE';

        if ($isFavorite) {
            $favoriteCookie = Yii::$app->request->cookies->get(Yii::$app->params['subdomen_favorite_cookie_name']);
            $itemsInCookie = $favoriteCookie->value['items'];

            $namesItems = [];
            foreach ($itemsInCookie as $item) {
                $elastic_model = new ElasticItems;
                $item = $elastic_model::find()
                    ->query(['bool' => ['must' => ['match' => ['unique_id' => $item]]]])
                    ->limit(1)
                    ->search();
                $item = $item['hits']['hits'][0];
                $namesItems[] = $item['restaurant_gorko_id'].' - '.$item['gorko_id'].' - '.$item['alias_rus'];
            }

            $listCookie = '';
            foreach ($namesItems as $number => $name) {
                $listCookie .= "%0A".++$number.'. '.$name;
            }
        }

        $content = '';
        foreach ($payload as $key => $item) {
            $content.= $key . ": " . $item . "%0A";
        }

        $sendToTelegram = fopen("https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chat_id}&parse_mode=html&text={$content}","r");
    }
}
