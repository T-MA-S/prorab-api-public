<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\components\QueryActiveController;
use app\models\ContactPayment;
use yii\web\NotFoundHttpException;

class ContactPaymentController extends QueryActiveController
{
    public $modelClass = ContactPayment::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ['pay', 'options'];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['pay']
                ],
                [
                    'allow' => true,
                    'actions' => ['get-payment'],
                    'roles' => ['user']
                ],
            ],
        ];
        return $behaviors;
    }

    protected function verbs()
    {
        return array_merge(parent::verbs(), [
            'pay' => ['PATCH'],
            'get-payment' => ['GET']
        ]);
    }

    public function actionPay()
    {
        $model = new ContactPayment();

        return $model->payContact(Yii::$app->getRequest()->getBodyParams());
    }

    public function actionGetPayment()
    {
        $data = Yii::$app->getRequest()->getBodyParams();
        $model = $this->modelClass::find()
            ->where(['user_id' => Yii::$app->user->id, 'entity_id' => $data['entity_id'], 'entity' => $data['entity']])
            ->one();

        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException('Оплата контактов не обнаружена');
        }
    }
}
