<?php

namespace app\components\payment;

use app\components\helpers\PaymentHelper;
use app\components\payment\Payment;
use app\models\PaymentHistory;

/**
 * This is the class for working with income payments.
 *
 * @property int $type money or point
 * @property int $user_id the user who created the payment 
 * @property string $appointment
 * @property int $direction default is 1 (income payment)
 * @property int $wallet_id the wallet what a user want to replenish
 * @property int $amount
 * @property string $invoice_id the id of payment in acquiring system
 */
class IncomePayment extends Payment
{
    public $direction = self::INCOME;

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['invoice_id', 'incomeValidation', 'when' => fn ($model) => $model->type === $model::TYPE_MONEY],
            ['invoice_id', 'required'],
        ]);
    }

    public function incomeValidation($attribute, $params)
    {
        if(PaymentHistory::find()->where('invoice_id = :iid', [':iid' => $this->invoice_id])->one()) {
            $this->addError($attribute, 'Данный платеж завершен');
        };

        $amount = PaymentHelper::confirmPayment($this->invoice_id);

        if(!$amount){
            $this->addError($attribute, 'Ошибка при подтверждении платежа');
        } else {
            $this->amount = $amount;
        }
    }
}