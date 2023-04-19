<?php

namespace app\controllers;

use app\components\QueryActiveController;

class ComplaintController extends QueryActiveController
{
    public $modelClass = 'app\models\Complaint';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'] = array_merge($behaviors['access']['rules'], [
            [
                'allow' => true,
                'actions' => ['create', 'index'],
                'roles' => ['user']
            ]
        ]);
        return $behaviors;
    }
}
