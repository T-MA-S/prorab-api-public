<?php

namespace app\models;

/**
 * This is the model class for table "partner_element".
 *
 * @property int $id
 * @property string $name
 * @property string $image
 * @property string $url
 * @property int $category_id
 *
 * @property PartnerCategory $category
 */
class PartnerElement extends yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'partner_element';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'image', 'url', 'category_id'], 'required'],
            [['name', 'image', 'url'], 'string', 'max' => 255],
            [['category_id'], 'integer'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => PartnerCategory::class, 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(PartnerCategory::class, ['id' => 'category_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'image' => 'Image',
            'url' => 'Url',
            'category_id' => 'Category ID',
        ];
    }
}