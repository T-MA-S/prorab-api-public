<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "admin_action".
 *
 * @property int $id
 * @property string|null $url
 * @property string|null $title
 *
 * @property AdminActionAccess[] $adminActionAccesses
 */
class AdminAction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'admin_action';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url', 'title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url' => 'URL',
            'title' => 'Заголовок',
        ];
    }

    /**
     * Gets query for [[AdminActionAccesses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdminActionAccesses()
    {
        return $this->hasMany(AdminActionAccess::className(), ['admin_action_id' => 'id']);
    }
}
