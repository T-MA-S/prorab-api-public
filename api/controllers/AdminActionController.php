<?php

namespace app\controllers;

use app\components\QueryActiveController;

class AdminActionController extends QueryActiveController
{
    public $modelClass = 'app\models\AdminAction';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'] = array_merge($behaviors['access']['rules'], [
            [
                'allow' => true,
                'actions' => ['index'],
                'roles' => ['tech']
            ]
        ]);
        return $behaviors;
    }
}
