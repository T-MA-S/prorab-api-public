<?php

namespace app\controllers;

use app\components\QueryActiveController;

class PageContentController extends QueryActiveController
{
    public $modelClass = 'app\models\PageContent';

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
