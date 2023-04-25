<?php

namespace app\controllers;

use yii\filters\AccessControl;
use app\components\QueryActiveController;

class PartnerElementController extends QueryActiveController
{
    public $modelClass = 'app\models\PartnerElement';

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