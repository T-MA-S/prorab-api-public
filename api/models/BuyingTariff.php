<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "buying_tariff".
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $tariff_id
 * @property string|null $datetime
 * @property int $is_payed
 *
 * @property Account $account
 * @property TariffPlan $tariff
 */
class BuyingTariff extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'buying_tariff';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['account_id', 'tariff_id', 'is_payed'], 'integer'],
            [['datetime'], 'safe'],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['account_id' => 'id']],
            [['tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => TariffPlan::className(), 'targetAttribute' => ['tariff_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_id' => 'ID аккаунта',
            'tariff_id' => 'ID тарифа',
            'datetime' => 'Дата',
            'is_payed' => 'Статус оплаты',
        ];
    }

    /**
     * Gets query for [[Account]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'account_id']);
    }

    /**
     * Gets query for [[Tariff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTariff()
    {
        return $this->hasOne(TariffPlan::className(), ['id' => 'tariff_id']);
    }
}
