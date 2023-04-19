<?php

namespace app\models;

use app\components\behaviors\FileBehavior;
use app\components\behaviors\UpdateTimeBehavior;
use Yii;

/**
 * This is the model class for table "blog_article".
 *
 * @property int $id
 * @property int|null $active
 * @property int|null $section_id
 * @property string|null $title
 * @property string|null $text
 * @property string|null $image
 * @property int|null $view_count
 * @property string|null $created
 * @property string|null $updated
 *
 * @property BlogSection $section
 */
class BlogArticle extends \yii\db\ActiveRecord
{
    const SCENARIO_NOT_UPDATE = 'not_update';

    public $preview;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['file'] = [
            'class' => FileBehavior::class,
            'attribute' => 'image'
        ];
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
        return 'blog_article';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['not_update'] = array_keys($this->attributes);
        return $scenarios;
    }

    public function extraFields()
    {
        return ['comments', 'section'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'section_id'], 'required'],
            [['active', 'section_id', 'view_count'], 'integer'],
            [['text'], 'string'],
            [['created', 'updated'], 'safe'],
            [['title', 'image'], 'string', 'max' => 255],
            [['section_id'], 'exist', 'skipOnError' => true, 'targetClass' => BlogSection::class, 'targetAttribute' => ['section_id' => 'id']],
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
            'section_id' => 'Section ID',
            'title' => 'Title',
            'text' => 'Text',
            'image' => 'Image',
            'view_count' => 'View Count',
            'created' => 'Created',
            'updated' => 'Updated',
        ];
    }

    /**
     * Gets query for [[Section]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSection()
    {
        return $this->hasOne(BlogSection::class, ['id' => 'section_id']);
    }

    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(BlogComment::class, ['article_id' => 'id']);
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->updated = date('Y-m-d H:i:s', time());
        }

        return parent::beforeSave($insert);
    }
}
