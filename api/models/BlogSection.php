<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "blog_section".
 *
 * @property int $id
 * @property int|null $active
 * @property string|null $title
 * @property string|null $description
 * @property string|null $image
 *
 * @property BlogArticle[] $blogArticles
 */
class BlogSection extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'blog_section';
    }

    public function extraFields()
    {
        return ['articles'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['active'], 'integer'],
            [['description'], 'string'],
            [['title', 'image'], 'string', 'max' => 255],
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
            'description' => 'Description',
            'image' => 'Image',
        ];
    }

    /**
     * Gets query for [[BlogArticles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArticles()
    {
        return $this->hasMany(BlogArticle::class, ['section_id' => 'id']);
    }
}
