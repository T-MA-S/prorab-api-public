<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "faq_type".
 *
 * @property int $id
 * @property int|null $active
 * @property string|null $title
 *
 * @property FaqSection[] $faqSections
 */
class FaqType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'faq_type';
    }

    public function extraFields()
    {
        return ['sections'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['active'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'active' => 'Active',
            'title' => 'Title',
        ];
    }

    /**
     * Gets query for [[FaqSections]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSections()
    {
        return $this->hasMany(FaqSection::class, ['type_id' => 'id']);
    }
}
