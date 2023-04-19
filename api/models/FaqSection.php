<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "faq_section".
 *
 * @property int $id
 * @property int|null $type_id
 * @property int|null $active
 * @property string|null $title
 *
 * @property FaqElement[] $faqElements
 * @property FaqType $type
 */
class FaqSection extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'faq_section';
    }

    public function extraFields()
    {
        return ['type', 'elements'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'type_id'], 'required'],
            [['type_id', 'active'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => FaqType::class, 'targetAttribute' => ['type_id' => 'id']],
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
            'active' => 'Active',
            'title' => 'Title',
        ];
    }

    /**
     * Gets query for [[FaqElements]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getElements()
    {
        return $this->hasMany(FaqElement::class, ['section_id' => 'id']);
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(FaqType::class, ['id' => 'type_id']);
    }
}
