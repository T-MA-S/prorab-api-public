<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "moderator_statistics".
 *
 * @property int $id
 * @property int|null $moderator_id
 * @property string|null $model
 * @property int|null $model_id
 * @property string|null $date
 *
 * @property User $moderator
 */
class ModeratorStatistics extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'moderator_statistics';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['moderator_id', 'model_id', 'model'], 'required'],
            [['moderator_id', 'model_id'], 'integer'],
            [['date'], 'safe'],
            [['model'], 'string', 'max' => 255],
            [['moderator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['moderator_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'moderator_id' => 'Moderator ID',
            'model' => 'Model',
            'model_id' => 'Model ID',
            'date' => 'Date',
        ];
    }

    /**
     * Gets query for [[Moderator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModerator()
    {
        return $this->hasOne(User::class, ['id' => 'moderator_id']);
    }
}
