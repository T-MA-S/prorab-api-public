<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tariff_plan".
 *
 * @property int $id
 * @property string|null $name
 * @property int $active
 * @property int|null $type
 * @property int|null $price
 * @property int|null $duration
 * @property int|null $orders_quantity
 *
 * @property BuyingTariff[] $buyingTariffs
 */
class TariffPlan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tariff_plan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
            [['active', 'type', 'price', 'duration', 'orders_quantity'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'active' => 'Статус активности',
            'type' => 'Тип',
            'price' => 'Цена',
            'duration' => 'Продолжительность',
            'orders_quantity' => 'Количество заявок',
        ];
    }

    /**
     * Gets query for [[BuyingTariffs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBuyingTariffs()
    {
        return $this->hasMany(BuyingTariff::className(), ['tariff_id' => 'id']);
    }
}
