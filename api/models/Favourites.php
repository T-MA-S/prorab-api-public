<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "favourites".
 *
 * @property int $id
 * @property int|null $object_id
 * @property int|null $user_id
 *
 * @property Objects $object
 * @property User $user
 */
class Favourites extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'favourites';
    }

    public function extraFields()
    {
        return ['object'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['object_id', 'user_id'], 'required'],
            [['object_id', 'user_id'], 'integer'],
            [['object_id'], 'exist', 'skipOnError' => true, 'targetClass' => Objects::className(), 'targetAttribute' => ['object_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
        ];
    }

    /**
     * Gets query for [[Object]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObject()
    {
        return $this->hasOne(Objects::className(), ['id' => 'object_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function beforeValidate()
    {
        $favourite = self::find()->where([
            'user_id' => $this->user_id,
            'object_id' => $this->object_id
        ])->one();
        if (!empty($favourite)) {
            $this->addError('object_id', 'Вы уже добавили объявление в избранное');
        }
        return parent::beforeValidate();
    }

}
