<?php

namespace app\controllers;

use app\components\QueryActiveController;


class FaqElementController extends QueryActiveController
{
    public $modelClass = 'app\models\FaqElement';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ['options', 'index', 'view'];
        $behaviors['access']['rules'] = array_merge($behaviors['access']['rules'], [
            [
                'allow' => true,
                'actions' => ['index', 'view'],
            ],
        ]);
        return $behaviors;
    }
}
