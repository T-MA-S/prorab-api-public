<?php

use app\models\Region;

$row = 1;
if (($handle = fopen($_SERVER['DOCUMENT_ROOT'] . '/region.tsv', "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
        if ($data[2] == 'RU') {
            $region = new Region();
            $region->country_id = 1;
            $region->name = $data[3];
            $region->active = 1;
            $region->geo_id = $data[0];
            if ($region->save()) {
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

