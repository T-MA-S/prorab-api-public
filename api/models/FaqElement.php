<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "faq_element".
 *
 * @property int $id
 * @property int|null $section_id
 * @property int|null $active
 * @property string|null $question
 * @property string|null $answer
 *
 * @property FaqSection $section
 */
class FaqElement extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'faq_element';
    }

    public function extraFields()
    {
        return ['section'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['section_id', 'question', 'answer'], 'required'],
            [['section_id', 'active'], 'integer'],
            [['question', 'answer'], 'string'],
            [['section_id'], 'exist', 'skipOnError' => true, 'targetClass' => FaqSection::class, 'targetAttribute' => ['section_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'section_id' => 'Section ID',
            'active' => 'Active',
            'question' => 'Question',
            'answer' => 'Answer',
        ];
    }

    /**
     * Gets query for [[Section]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSection()
    {
        return $this->hasOne(FaqSection::class, ['id' => 'section_id']);
    }
}
