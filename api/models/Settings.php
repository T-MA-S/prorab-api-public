<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "settings".
 *
 * @property int $id
 * @property int|null $type_id
 * @property string|null $name
 * @property string|null $title
 * @property string|null $text
 *
 * @property SettingsType $type
 */
class Settings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_id', 'name', 'title'], 'required'],
            ['name', 'unique', 'message' => 'Пользователь с таким номером телефона уже существует'],
            [['type_id'], 'integer'],
            [['text'], 'string'],
            [['name', 'title'], 'string', 'max' => 255],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => SettingsType::class, 'targetAttribute' => ['type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_id' => 'Type ID',
            'name' => 'Name',
            'title' => 'Title',
            'text' => 'Text',
        ];
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(SettingsType::class, ['id' => 'type_id']);
    }
}
