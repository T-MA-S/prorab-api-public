<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "promocode".
 *
 * @property int $id
 * @property string|null $created
 * @property int|null $duration
 * @property string|null $active_till
 *
 * @property PromocodeActivation[] $promocodeActivations
 */
class Promocode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promocode';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created', 'active_till'], 'safe'],
            [['duration'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created' => 'Дата создания',
            'duration' => 'Продолжительность',
            'active_till' => 'Активен до',
        ];
    }

    /**
     * Gets query for [[PromocodeActivations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPromocodeActivations()
    {
        return $this->hasMany(PromocodeActivation::className(), ['promocode_id' => 'id']);
    }
}
