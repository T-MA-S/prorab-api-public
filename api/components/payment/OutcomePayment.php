<?php

namespace app\components\payment;

use app\components\payment\Payment;
use yii\db\Query;

/**
 * This is the class for working with outcome payments.
 *
 * @property int $type money or point
 * @property int $user_id the user who created the payment 
 * @property string $appointment
 * @property int $direction default is 2 (outcome payment)
 * @property int $wallet_id the wallet what a user want use
 * @property int $amount
 */
class OutcomePayment extends Payment
{
    public $direction = self::OUTCOME;
    
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['amount', 'outcomeValidation']
        ]);
    }

    public function outcomeValidation($attribute, $param)
    {
        $balance = $this->getUserBalance();

        if($balance < $this->$attribute) {
            $this->addError($attribute, 'На вашем счете недостаточно средств');
        }
    }

    public function getUserBalance()
    {
        $query = new Query();

        if($this->type == $this::TYPE_POINTS) {
            $query->select('points');
        } elseif ($this->type == $this::TYPE_MONEY) {
            $query->select('money');
        }

        $query->from('wallet')->where('user_id = :uid', [':uid' => $this->user_id]);

        return $query->scalar();
    }
}