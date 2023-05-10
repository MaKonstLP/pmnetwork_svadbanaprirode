<?php
use frontend\modules\svadbanaprirode\models\ElasticItems;


$elastic_model = new ElasticItems;
$item = $elastic_model::find()
	->query(['bool' => ['must' => [
	    ['match'=>['unique_id' => $text_id]],
        ['match' => ['city_id' => \Yii::$app->params['subdomen_id']]]
    ]]])
	->limit(1)
	->search();

if(isset($item['hits']['hits'][0])){
	echo $this->render('//components/generic/restaurant_adv_test.twig', ['item' => $item['hits']['hits'][0]]);
}
	




?>