<?php

use app\models\Region;
use app\models\City;

$xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/db.xml');
print_r($_SERVER['DOCUMENT_ROOT']);
foreach ($xml->Country[190]->Region as $region) {
    $regionExist = Region::find()->where(['name' => (string)$region['name'][0]])->one();
    if (empty($regionExist)) {
        $regionModel = new Region();
        $regionModel->country_id = 7;
        $regionModel->name = (string)$region['name'][0];
        $regionModel->active = 1;
        if ($regionModel->save()) {
            echo '<pre>';
            echo $region['name'][0];
            echo '</pre>';
            foreach ($region->Locality as $city) {
                $cityExist = City::find()->where(['name' => (string)$city['type'][0] . ' ' . (string)$city['name'][0]])->one();
                if (empty($cityExist)) {
                    $cityModel = new City();
                    $cityModel->region_id = $regionModel->id;
                    $cityModel->name = (string)$city['type'][0] . ' ' . (string)$city['name'][0];
                    $cityModel->active = 1;
                    if ($cityModel->save()) {
                        echo '<pre>';
                        echo '- ' . $city['type'][0] . ' ' . $city['name'][0];
                        echo '</pre>';
                    }
                }
            }
        }
    }
}
