<?php

namespace app\controllers;

use app\components\QueryActiveController;

class AccountController extends QueryActiveController
{
    public $modelClass = 'app\models\Account';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return $behaviors;
    }
}
