<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dictionary".
 *
 * @property int $id
 * @property string $word
 * 
 */
class Dictionary extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'dictionary';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['word', 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'word' => 'Недопустимые слова'
        ];
    }
}
