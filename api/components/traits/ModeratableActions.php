<?php

namespace app\components\traits;

use yii\web\NotFoundHttpException;

trait ModeratableActions
{
    public function actionApprove($id)
    {
        if(!$this->modelClass::approve($id)){
            throw new NotFoundHttpException();
        }
        
        return ['object' => $id];
    }

    public function actionReject($id)
    {
        if(!$this->modelClass::reject($id)){
            throw new NotFoundHttpException();
        }
        
        return ['object' => $id];
    }

    public function actionForModeration()
    {
        return $this->modelClass::forModeration($this->elementName);
    }
}