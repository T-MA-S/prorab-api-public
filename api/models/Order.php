<?php

namespace app\models;

use Yii;
use app\models\Notification;
use app\components\behaviors\NotificationBehavior;
use app\components\helpers\PaymentHelper;
use app\components\interfaces\StatusInterface;
use app\components\traits\Moderadable;
use yii\web\UnauthorizedHttpException;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int|null $object_id
 * @property int|null $user_id
 * @property string|null $about
 * @property int|null $confirmed
 * @property string|null $created
 * @property string|null $time_from
 * @property string|null $time_to
 * @property string|null $address
 * @property string|null $payment_from
 * @property string|null $payment_to
 * @property string|null $paid
 * @property string|null $invoice_id
 * @property int|null $rejected
 * @property int|null $verified
 * @property int|null $moderator_id
 * @property array|string $booking
 *
 * @property Booking[] $bookings
 */
class Order extends \yii\db\ActiveRecord
{
    use Moderadable;

    public $booking;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['notification'] = [
            'class' => NotificationBehavior::class,
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order';
    }

    public function extraFields()
    {
        return ['user', 'object', 'bookings'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['object_id', 'user_id'], 'required'],
            [['object_id', 'user_id', 'verified', 'moderator_id'], 'integer'],
            [['about'], 'string'],
            [['invoice_id'], 'string', 'max' => 255],
            [['created', 'time_from', 'time_to', 'address', 'payment_from', 'payment_to', 'booking'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'object_id' => 'ID объявления',
            'user_id' => 'ID пользователя',
            'about' => 'Описание',
            'confirmed' => 'Статус подтверждения',
            'created' => 'Дата создания',
            'time_from' => 'Время от',
            'time_to' => 'Время до',
            'address' => 'Адрес',
            'payment_from' => 'Оплата от',
            'payment_to' => 'Оплата до',
        ];
    }

    /**
     * Gets query for [[Bookings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBookings()
    {
        return $this->hasMany(Booking::class, ['order_id' => 'id']);
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

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObject()
    {
        return $this->hasOne(Objects::class, ['id' => 'object_id']);
    }

    public function beforeSave($insert)
    {
        if ($this->getOldAttribute('paid') != $this->paid) {
            if(PaymentHelper::checkPayment($this->invoice_id)) {
                $this->paid = 1;
            } else {
                $this->paid = 0;
            }
        }

        if ($this->getOldAttribute('confirmed') != $this->confirmed) {
            if (!$this->paid) {
                if(!PaymentHelper::checkPayment($this->invoice_id)) {
                    throw new UnauthorizedHttpException('Это платная услуга, для того чтобы откликнуться на заявку необходимо произвести оплату ', -1);
                } else {
                    $this->paid = 1;
                }
            }
        }

        $this->moderate();

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if($this->status == StatusInterface::APPROVED) {

            $this->booking = json_decode($this->booking);

            if (is_array($this->booking)) {

                foreach ($this->booking as $booking) {
                    $bookingModel = Booking::find()
                        ->where(['order_id' => $this->id])
                        ->andFilterWhere(['like', 'date_from', $booking->date])->one();
                    if(empty($bookingModel)) {
                        $bookingModel = new Booking();
                        $bookingModel->order_id = $this->id;
                        $bookingModel->date_from = $booking->date;
                    }
                    $bookingModel->duration = $booking->duration;
                    $bookingModel->save();
                }
            }
            if ($insert) {
                $user = User::findOne($this->user_id);
                $notificationMessage = "На ваше объявление " . $this->object->name . " оставил заявку пользователь " . $user->name;
                $this->sendNotification(Notification::TYPE_ORDER_CREATED, $notificationMessage, $this->object->user_id);
            }

            if (array_key_exists('confirmed', $changedAttributes) && $this->confirmed) {
                $user = User::findOne($this->object->user_id);
                $notificationMessage = "Ваша заявка на объявление " . $this->object->name . " получила отклик от пользователя " . $user->name;
                $this->sendNotification(Notification::TYPE_ORDER_CONFIRMED, $notificationMessage, $this->user_id);

                $orders = self::find()
                    ->joinWith(['object'])
                    ->where([
                        'confirmed' => null,
                        'rejected' => null,
                        'archive' => 0,
                        'deleted' => 0,
                        'order.user_id' => $this->user_id,
                        'object.city_id' => $this->object->city_id,
                        'object.category_id' => $this->object->category_id
                    ])
                    ->asArray()
                    ->all();

                $ids = [];
                foreach ($orders as $order) {
                    $ids[] = $order['id'];
                }
                if (!empty($ids)) {
                    $sql = "UPDATE `order` SET rejected = 1 WHERE id IN (" . implode(', ', $ids) . ")";
                    \Yii::$app->db->createCommand($sql)->execute();
                }
            }
        }

        if (array_key_exists('rejected', $changedAttributes) && $this->rejected) {
            $notificationMessage = "Ваша заявка на объявление " . $this->object->name . " была отклонена.";
            $this->sendNotification(Notification::TYPE_ORDER_REJECTED, $notificationMessage, $this->user_id);
        }
    }
}
