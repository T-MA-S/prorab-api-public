<?php

namespace app\components\actions;

use Yii;
use yii\base\Model;
use yii\rest\Action;
use yii\web\ErrorAction;
use yii\web\ServerErrorHttpException;

class JsonErrorAction extends ErrorAction
{
    public function run()
    {
        Yii::$app->getResponse()->setStatusCodeByException($this->exception);

        return $this->renderAjaxResponse();
    }

}