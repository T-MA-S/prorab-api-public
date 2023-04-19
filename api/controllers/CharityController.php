<?php

namespace app\controllers;

use app\components\QueryActiveController;

class CharityController extends QueryActiveController
{
    public $modelClass = 'app\models\Charity';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ['options', 'create'];
        $behaviors['access']['rules'] = array_merge($behaviors['access']['rules'], [
            [
                'allow' => true,
                'actions' => ['create'],
            ]
        ]);
        return $behaviors;
    }
}
