<?php

namespace app\controllers;

use app\components\QueryActiveController;

class ChatMessageFileController extends QueryActiveController
{
    public $modelClass = 'app\models\ChatMessageFile';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'] = array_merge($behaviors['access']['rules'], [
            [
                'allow' => true,
                'actions' => ['create', 'index', 'view'],
                'roles' => ['user']
            ]
        ]);
        return $behaviors;
    }
}
