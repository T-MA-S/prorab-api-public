<?php

namespace app\controllers;

use app\components\QueryActiveController;

class ImageController extends QueryActiveController
{
    public $modelClass = 'app\models\Image';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'] = array_merge($behaviors['access']['rules'], [
            [
                'allow' => true,
                'actions' => ['index', 'view', 'delete'],
                'roles' => ['user']
            ]
        ]);
        return $behaviors;
    }

}
