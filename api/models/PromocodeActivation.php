<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "promocode_activation".
 *
 * @property int $id
 * @property string|null $datetime
 * @property int|null $account_id
 * @property int|null $promocode_id
 *
 * @property Account $account
 * @property Promocode $promocode
 */
class PromocodeActivation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promocode_activation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['datetime'], 'safe'],
            [['account_id', 'promocode_id'], 'integer'],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['account_id' => 'id']],
            [['promocode_id'], 'exist', 'skipOnError' => true, 'targetClass' => Promocode::className(), 'targetAttribute' => ['promocode_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'datetime' => 'Дата',
            'account_id' => 'ID аккаунта',
            'promocode_id' => 'ID промокода',
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
     * Gets query for [[Promocode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPromocode()
    {
        return $this->hasOne(Promocode::className(), ['id' => 'promocode_id']);
    }
}
