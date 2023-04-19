<?php

namespace app\controllers;

use yii\filters\AccessControl;
use app\components\QueryActiveController;

class DictionaryController extends QueryActiveController
{
    public $modelClass = 'app\models\Dictionary';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['except'] = ['options', 'index', 'view'];

        $behaviors['access']['rules'] = array_merge($behaviors['access']['rules'], [
            [
                'allow' => true,
                'actions' => ['create', 'index'],
                'roles' => ['moderator']
            ]
        ]);

        return $behaviors;
    }
}
