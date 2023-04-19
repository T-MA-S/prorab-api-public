<?php

namespace app\controllers;

use yii\filters\AccessControl;
use app\components\QueryActiveController;

class ScheduleIsBusyController extends QueryActiveController
{
    public $modelClass = 'app\models\ScheduleIsBusy';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['admin', 'user']
                ],
            ],
        ];
        return $behaviors;
    }
}
