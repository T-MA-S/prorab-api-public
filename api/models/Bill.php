<?php

namespace app\models;

use app\components\interfaces\PaymentInterface;
use app\components\traits\ActiveRecordExtend;
use Yii;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "bill".
 *
 * @property int $id
 * @property int $user_id
 * @property string $wallet_id
 * @property string $date
 * @property string $expire_at
 * @property string $appointment
 * @property int $status
 * @property float $amount
 */
class Bill extends \yii\db\ActiveRecord implements PaymentInterface
{
    use ActiveRecordExtend;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bill';
    }

    public function fields()
    {
        return [
            'id',
            'amount',
            'user_id',
            'appointment',
            'date' => fn() => $this->formatDate($this->date),
            'expire_at' => fn() => date('d.m.Y', strtotime($this->expire_at))
        ];
    }

    protected function formatDate($date)
    {
        $date = explode('.', date('d.m.Y', strtotime($date)));
        $months = [
            '01' => 'Января', '02' => 'Февраля',
            '03' => 'Марта', '04' => 'Апреля', '05' => 'Мая', 
            '06' => 'Июня', '07' => 'Июля', '08' => 'Августа',
            '09' => 'Сентября', '10' => 'Октября', '11' => 'Ноября', 
            '12' => 'Декабря'
        ];

        $date[1] = $months[$date[1]];

        return implode(' ', $date);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'amount', 'appointment'], 'safe'],
            [['amount', 'appointment'], 'required'],
            [['amount', 'status'], 'number'],
            [['appointment'], 'string', 'max' => 255],
            ['user_id', 'billExist', 'when' => fn($m) => $this->isNewRecord]
        ];
    }

    public function billExist($attribute, $param)
    {
        $model = $this::find()
        ->where([
            'user_id' => $this->$attribute,
            'status' =>  $this::BILL_AWAIT
        ])
        ->andWhere('expire_at >= :ex', [':ex' => date('Y-m-d H:i:s', time())]);

        if($model->one()){
            $this->addError($attribute, 'У вас уже есть счёт ожидающий оплаты');
        }
    }

    public function beforeSave($insert)
    {
        if($this->isNewRecord) {
            $this->user_id = defined('YII_DEBUG') ? \Yii::$app->params['debugUserId'] : \Yii::$app->user->id;
            $this->date = date('Y-m-d H:i:s', time());
            $this->expire_at = date('Y-m-d H:i:s', time() + 259200);
            $this->status = $this::BILL_AWAIT;
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        /* ждём шаблоны 
        $user = $this->user;

        if(!empty($user) && $user->send_messages){
            Yii::$app->mailer->compose('notification/bill/created', ['model' => $this])
                ->setFrom(Yii::$app->params['senderEmail'])
                ->setTo($this->user->email)
                ->setSubject('Новый счёт с сайта ' . Yii::$app->params['siteUrl'])
                ->send()
            ;
        }
        */
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function executeBill()
    {
        $transaction = $this::getDb()->beginTransaction();
        try {
            $user = User::findOrFail($this->user_id);

            if(!$user->replenishByBill($this->attributes)){
                throw new HttpException(402, $user->getErrors(), 402);
            }

            $this->status = $this::BILL_SUCCESS;
            if(!$this->save()){
                throw new HttpException(402, json_encode($this->getErrors()), 402);
            };

            $transaction->commit();

            return $this->id;

        } catch (\Throwable $e) {
            $transaction->rollback();
            $c = $e->getCode();
            throw new HttpException($c, $e->getMessage(), $c);
        }

        return false;
    }

    public function rejectBill()
    {
        $this->status = $this::BILL_REJECT;
        return $this->update(false);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'ID пользователя',
            'wallet_id' => 'ID кошелька пользователя',
            'amount' => 'Сумма счёта',
            'appointment' => 'Назначение счета',
            'exprite_at' => 'Истечение срока действия счёта',
        ];
    }
}
