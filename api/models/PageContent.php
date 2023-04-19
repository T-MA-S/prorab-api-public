<?php

namespace app\models;

use app\components\behaviors\UpdateTimeBehavior;
use Yii;

/**
 * This is the model class for table "page_content".
 *
 * @property int $id
 * @property int|null $page_id
 * @property string|null $title
 * @property string|null $text
 * @property string|null $created
 * @property string|null $updated
 *
 * @property Page $page
 */
class PageContent extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['updateTime'] = [
            'class' => UpdateTimeBehavior::class
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'page_content';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['page_id'], 'integer'],
            [['text'], 'string'],
            [['created', 'updated'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['page_id'], 'exist', 'skipOnError' => true, 'targetClass' => Page::class, 'targetAttribute' => ['page_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'page_id' => 'Page ID',
            'title' => 'Title',
            'text' => 'Text',
            'created' => 'Created',
            'updated' => 'Updated',
        ];
    }

    /**
     * Gets query for [[Page]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPage()
    {
        return $this->hasOne(Page::class, ['id' => 'page_id']);
    }
}
