<?php

namespace app\components\helpers;

use linslin\yii2\curl;


class PaymentHelper
{
    public static function checkPayment($invoiceId)
    {
        $curl = new curl\Curl();
        $data = [
            "InvoiceId"=>$invoiceId
        ];

        $resultJson = $curl->setHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . \Yii::$app->params['paymentAuthKey']
        ])->setPostParams($data)->post('https://api.cloudpayments.ru/payments/find');
        $result = json_decode($resultJson);
        if ($result->Success && in_array($result->Model->Status, ['Completed', 'Authorized'])) {
            return true;
        }
        return false;
    }

    public static function confirmPayment($invoiceId)
    {
        $curl = new curl\Curl();
        $data = [
            "InvoiceId"=>$invoiceId
        ];

        $resultJson = $curl->setHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . \Yii::$app->params['paymentAuthKey']
        ])->setPostParams($data)->post('https://api.cloudpayments.ru/payments/get');
        $result = json_decode($resultJson);
        $amount = $result->Model->Amount;
        if ($result->Success && $result->Model->Status == 'Authorized') {
            $curl = new curl\Curl();
            $data = [
                "TransactionId"=>$result->Model->TransactionId,
                "Amount" => $amount
            ];
            $resultJson = $curl->setHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . \Yii::$app->params['paymentAuthKey']
            ])->setPostParams($data)->post('https://api.cloudpayments.ru/payments/confirm');
            $result = json_decode($resultJson);

            if ($result->Success) return $amount;
        } elseif ($result->Success && $result->Model->Status ==  'Completed') {
            return $amount;
        }
        return false;
    }
}