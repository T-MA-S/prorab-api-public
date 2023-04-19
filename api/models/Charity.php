<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "charity".
 *
 * @property int $id
 * @property string|null $fio
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $fund
 * @property string|null $comment
 */
class Charity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'charity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fio', 'email', 'phone', 'fund'], 'required'],
            [['email'], 'email'],
            [['comment'], 'string'],
            [['fio', 'email', 'phone', 'fund'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fio' => 'ФИО',
            'email' => 'Email',
            'phone' => 'Телефон',
            'fund' => 'Фонд',
            'comment' => 'Комментарий',
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Yii::$app->mailer->compose('charity/html', ['model' => $this])
            ->setFrom(Yii::$app->params['senderEmail'])
            ->setTo(Yii::$app->params['adminEmail'])
            ->setSubject($this->fio . ' заполнил форму на сайте ' . Yii::$app->params['siteUrl'])
            ->send();
    }
}
