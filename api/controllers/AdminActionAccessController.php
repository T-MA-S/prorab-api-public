<?php

namespace app\controllers;

use app\components\QueryActiveController;

class AdminActionAccessController extends QueryActiveController
{
    public $modelClass = 'app\models\AdminActionAccess';

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
