<?php

namespace app\models;


/**
 * This is the model class for table "partner_category".
 *
 * @property int $id
 * @property string $category_name
 *
 * @property PartnerElement[] $partnerElements
 */
class PartnerCategory extends yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'partner_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_name'], 'required'],
            [['category_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerElements()
    {
        return $this->hasMany(PartnerElement::class, ['category_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_name' => 'Category Name',
        ];
    }
}
