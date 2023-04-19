<?php

namespace app\controllers;

use Yii;
use app\components\QueryActiveController;
use yii\web\ServerErrorHttpException;

class WalletController extends QueryActiveController
{
    public $modelClass = 'app\models\Wallet';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'] = array_merge($behaviors['access']['rules'], [
            [
                'allow' => true,
                'actions' => ['view', 'create', 'update'],
                'roles' => ['moderator']
            ]
        ]);
        return $behaviors;
    }

    // public function actionPutMoney($userId)
    // {
    //     $params = Yii::$app->getRequest()->getBodyParams();
    //     $model = $this->modelClass::find()->where(['user_id' => $userId])->one();
    //     if (empty($model)) {
    //         $model = new $this->modelClass();
    //         $model->user_id = $userId;
    //     }
    //     $model->money += $params['money'];
    //     if ($model->save()) {
    //         return $model;
    //     } else {
    //         throw new ServerErrorHttpException('Возникла ошибка при зачислении денег');
    //     }
    // }
}
