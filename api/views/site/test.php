<?php

use app\components\SxGeo;
use app\models\City;

$geo = new SxGeo($_SERVER['DOCUMENT_ROOT'] . '/SxGeoCity.dat');

echo Yii::$app->request->userIP;

$geoCity = $geo->getCity(Yii::$app->request->userIP);

$city = City::find()->where(['geo_id'=>$geoCity['city']['id']])->one();

?>

<div>
    <pre>
        fwf
        <?php print_r($city); ?>
    </pre>
</div>

