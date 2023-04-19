<?php

namespace app\models;

use app\components\behaviors\UpdateTimeBehavior;
use Yii;

/**
 * This is the model class for table "booking".
 *
 * @property int $id
 * @property int|null $order_id
 * @property string|null $date_from
 * @property string|null $created
 * @property int|null $duration
 * @property string|null $updated
 *
 * @property Order $order
 */
class Booking extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['updateTime'] = [
            'class' => UpdateTimeBehavior::class
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'booking';
    }

    public function extraFields()
    {
        return ['order'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'duration'], 'integer'],
            [['date_from', 'created', 'updated'], 'safe'],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'ID заказа',
            'date_from' => 'Дата начала',
            'created' => 'Дата создания',
            'duration' => 'Продолжительность',
            'updated' => 'Дата обновления',
        ];
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }
}
