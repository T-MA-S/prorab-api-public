<?php

use app\models\Region;
use app\models\City;

$row = 1;
if (($handle = fopen($_SERVER['DOCUMENT_ROOT'] . '/city.tsv', "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
        $region = Region::find()->where(['geo_id' => $data[1]])->one();
        if (!empty($region)) {
            $city = new City();
            $city->region_id = $region->id;
            $city->name = $data[2];
            $city->active = 1;
            $city->geo_id = $data[0];
            if($city->save()) {
                $num = count($data);
                echo "<p> $num полей в строке $row: <br /></p>\n";
                $row++;
                for ($c=0; $c < $num; $c++) {
                    echo $data[$c] . "<br />\n";
                }
            } else {
                echo 'Упс, каката ху..та)))';
            }
        }
    }
    fclose($handle);
}

