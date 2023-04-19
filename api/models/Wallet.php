<?php

namespace app\models;

use app\components\interfaces\PaymentInterface;
use app\components\payment\Bill;
use app\components\payment\IncomePayment;
use app\components\payment\OutcomePayment;
use app\components\payment\Payment;
use app\models\User;
use Yii;
use yii\base\Exception;
use yii\web\HttpException;

/**
 * This is the model class for table "wallet".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $money
 * @property int|null $points
 *
 * @property Payment $payment
 * @property User $user
 */
class Wallet extends \yii\db\ActiveRecord implements PaymentInterface
{
    public $payment;

    public $appointment;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wallet';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['user_id', 'required'],
            [['user_id', 'money', 'points'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'money' => 'Money',
            'points' => 'Points',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function executePayment()
    {
        $this->payment->wallet_id = $this->id;
        $this->payment->user_id = defined('YII_DEBUG') ? Yii::$app->params['debugUserId'] : Yii::$app->user->id;

        if($this->payment->type == $this::TYPE_MONEY) {
            $this->byMoney();
        } elseif($this->payment->type == $this::TYPE_POINTS) {
            $this->byPoints();
        }

        if(!$this->save(false)) {
            throw new HttpException(402, $this->getErrors(), 402);
        };

        return false;
    }

    protected function byMoney(): void
    {
        if($this->payment->direction == $this::INCOME) {
            $this->appointment = 'Пополнение кошелька';
            $this->increase('money', $this->payment->amount);
        } elseif($this->payment->direction == $this::OUTCOME) {
            $this->appointment = 'Списание с кошелька';
            $this->decrease('money', $this->payment->amount);
        }
    }

    protected function byPoints(): void
    {
        if($this->payment->direction == $this::INCOME) {
            $this->appointment = 'Реферальные баллы';
            $this->increase('points', $this->payment->amount);
        } elseif($this->payment->direction == $this::OUTCOME) {
            $this->appointment = 'Оплата баллами';
            $this->decrease('points', $this->payment->amount);
        }
    }

    protected function increase(string $attribute, int $amount): void
    {
        $this->$attribute += $amount;
    }

    protected function decrease(string $attribute, int $amount): void
    {
        $this->$attribute -= $amount;
    }

    public function beforeSave($insert)
    {
        if(!empty($this->payment)){
            if(empty($this->payment->appointment)){
                $this->payment->appointment = $this->appointment;
            }
            $this->payment->status = $this::PAYMENT_SUCCESS;
            $this->payment->saveHistory();
        }

        return parent::beforeSave($insert);
    }
}
