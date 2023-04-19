<?php

namespace app\models;

use app\components\behaviors\UpdateTimeBehavior;
use Yii;

/**
 * This is the model class for table "page".
 *
 * @property int $id
 * @property string|null $alias
 * @property string|null $title
 * @property string|null $text
 * @property string|null $created
 * @property string|null $updated
 *
 * @property PageContent[] $pageContents
 */
class Page extends \yii\db\ActiveRecord
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
        return 'page';
    }

    public function extraFields()
    {
        return ['pageContents'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['text'], 'string'],
            [['created', 'updated'], 'safe'],
            [['alias', 'title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'alias' => 'Alias',
            'title' => 'Title',
            'text' => 'Text',
            'created' => 'Created',
            'updated' => 'Updated',
        ];
    }

    /**
     * Gets query for [[PageContents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPageContents()
    {
        return $this->hasMany(PageContent::class, ['page_id' => 'id']);
    }
}
