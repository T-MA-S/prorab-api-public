<?php

namespace app\components\payment;

use app\components\interfaces\PaymentInterface;
use app\models\PaymentHistory;
use yii\base\Model;

/**
 * This is the class for working with wallet and payment history.
 *
 * @property int $type money or point
 * @property int $user_id the user who created the payment 
 * @property string $appointment
 * @property int $direction income or outcome to wallet
 * @property int $wallet_id the wallet what a user want to replenish
 * @property int $amount 
 * @property string $invoice_id the id of payment in acquiring system
 * @property int $status
 */
abstract class Payment extends Model implements PaymentInterface
{
    public $appointment;

    public $invoice_id;

    public $wallet_id;

    public $direction;

    public $user_id;

    public $date;

    public $amount;

    public $status;

    public $type;

    public function __construct()
    {
        $this->date = date('Y-m-d H:i:s', time());
        $this->user_id = defined('YII_DEBUG') ? \Yii::$app->params['debugUserId'] : \Yii::$app->user->id;
    }

    public function rules()
    {
        return [
            [['amount', 'invoice_id', 'type', 'appointment'], 'safe'],
            [['type', 'amount'], 'number']
        ];
    }

    public function saveHistory()
    {
        $history = new PaymentHistory();
        
        $history->setAttributes($this->attributes);

        if(!$history->save()){
            return false;
        };

        return true;
    }

    public function points()
    {
        $this->type = $this::TYPE_POINTS;
        return $this;
    }

    public function money()
    {
        $this->type = $this::TYPE_MONEY;
        return $this;
    }
}