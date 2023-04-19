<?php

namespace app\commands;

use yii\console\Controller;
use app\models\Order;

class OrderController extends Controller
{

    public function actionReject()
    {
        $today = new \DateTime('now');
        $yesterday = $today->modify('-1 day');

        $sql = "UPDATE `order` SET rejected = 1 WHERE confirmed IS NULL "
            . "AND rejected IS NULL AND archive = 0 AND deleted = 0 "
            . "AND created < '" . $yesterday->format('Y-m-d H:i:s') . "'";
        \Yii::$app->db->createCommand($sql)->execute();
    }

    public function actionArchive()
    {
        $today = new \DateTime('now');
        $yesterday = $today->modify('-1 day');
        $orders =  Order::find()
            ->where(['confirmed' => 1, 'rejected' => null, 'archive' => 0, 'deleted' => 0])
            ->andWhere(['<', 'created', $yesterday->format('Y-m-d H:i:s')])
            ->all();
        foreach ($orders as $order) {
            $order->archive = 1;
            $order->save();
        }
    }
}
