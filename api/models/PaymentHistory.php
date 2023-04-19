<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "payment_history".
 *
 * @property int $type money or point
 * @property int $user_id the user who created the payment 
 * @property int $direction income or outcome to wallet
 * @property int $wallet_id the wallet what a user want to replenish
 * @property int $amount 
 * @property string $invoice_id the id of payment in acquiring system
 *
 */
class PaymentHistory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_history';
    }

    public function rules()
    {
        return [
            [['wallet_id', 'user_id', 'direction', 'appointment', 'type', 'amount', 'invoice_id', 'date', 'status'], 'safe']
        ];
    }
}