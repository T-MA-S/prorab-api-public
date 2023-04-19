<?php

namespace app\controllers;

use app\components\QueryActiveController;

class BuyingTariffController extends QueryActiveController
{
    public $modelClass = 'app\models\BuyingTariff';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'] = array_merge($behaviors['access']['rules'], [
            [
                'allow' => true,
                'actions' => ['index'],
                'roles' => ['user']
            ]
        ]);
        return $behaviors;
    }
}
