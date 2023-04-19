<?php

namespace app\controllers;

use app\components\QueryActiveController;
use yii\filters\AccessControl;
use app\models\Bill;

class BillController extends QueryActiveController
{
    public $modelClass = Bill::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ['options'];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['execute-bill', 'reject-bill', 'update'],
                    'roles' => ['moderator']
                ],
                [
                    'allow' => true,
                    'actions' => ['create'],
                    'roles' => ['user']
                ]
            ],
        ];
        return $behaviors;
    }

    protected function verbs()
    {
        return array_merge(parent::verbs(), [
            'execute-bill' => ['PATCH'],
            'reject-bill' => ['PATCH'],
            'create' => ['POST']
        ]);
    }

    public function actionExecuteBill($id)
    {
        return Bill::findByConditionOrFail(['id' => $id, 'status' => Bill::BILL_AWAIT])->executeBill();
    }

    public function actionRejectBill($id)
    {
        return Bill::findByConditionOrFail(['id' => $id, 'status' => Bill::BILL_AWAIT])->rejectBill();
    }
}
