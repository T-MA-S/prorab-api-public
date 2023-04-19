<?php

namespace app\controllers;

use app\components\QueryActiveController;

class AdditionalCategoryController extends QueryActiveController
{
    public $modelClass = 'app\models\AdditionalCategory';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'] = array_merge($behaviors['access']['rules'], [
            [
                'allow' => true,
                'actions' => ['index', 'view'],
                'roles' => ['user']
            ]
        ]);
        return $behaviors;
    }
}
