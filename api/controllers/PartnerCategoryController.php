<?php

namespace app\controllers;

use yii\filters\AccessControl;
use app\components\QueryActiveController;

class PartnerCategoryController extends QueryActiveController
{
    public $modelClass = 'app\models\PartnerCategory';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ['index', 'view'];
        $behaviors['access']['rules'] = array_merge($behaviors['access']['rules'], [
            [
                'allow' => true,
                'actions' => ['index', 'view'],
            ],
        ]);
        return $behaviors;
    }

}