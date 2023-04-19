<?php

namespace app\models;

use creocoder\nestedsets\NestedSetsBehavior;
use Yii;

/**
 * This is the model class for table "blog_comment".
 *
 * @property int $id
 * @property int|null $article_id
 * @property int|null $user_id
 * @property string|null $text
 * @property string|null $created
 * @property int|null $parent_id
 * @property int $tree
 * @property int $lft
 * @property int $rgt
 * @property int $depth
 * @property int $position
 *
 * @property BlogArticle $article
 * @property User $user
 */
class BlogComment extends \yii\db\ActiveRecord
{
    // библеотека для работы с деревом в реакте https://www.npmjs.com/package/nested-sets-tree
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['tree'] = [
            'class' => NestedSetsBehavior::className(),
            'treeAttribute' => 'tree'
        ];
        return $behaviors;
    }


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'blog_comment';
    }

    public function extraFields()
    {
        return ['article', 'user'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['text', 'article_id'], 'required'],
            [['article_id', 'user_id', 'parent_id', 'tree', 'lft', 'rgt', 'depth', 'position'], 'integer'],
            [['text'], 'string'],
            [['created'], 'safe'],
            [['article_id'], 'exist', 'skipOnError' => true, 'targetClass' => BlogArticle::class, 'targetAttribute' => ['article_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'article_id' => 'Article ID',
            'user_id' => 'User ID',
            'text' => 'Text',
            'created' => 'Created',
            'parent_id' => 'Parent ID',
            'tree' => 'Tree',
            'lft' => 'Lft',
            'rgt' => 'Rgt',
            'depth' => 'Depth',
            'position' => 'Position',
        ];
    }

    /**
     * Gets query for [[Article]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArticle()
    {
        return $this->hasOne(BlogArticle::class, ['id' => 'article_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            if (empty($this->user_id)) {
                $this->user_id = \Yii::$app->user->id;
            }
        }

        return parent::beforeSave($insert);
    }
}
