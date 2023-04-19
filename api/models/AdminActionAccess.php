<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "admin_action_access".
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $admin_action_id
 *
 * @property Account $account
 * @property AdminAction $adminAction
 */
class AdminActionAccess extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'admin_action_access';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['account_id', 'admin_action_id'], 'integer'],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['account_id' => 'id']],
            [['admin_action_id'], 'exist', 'skipOnError' => true, 'targetClass' => AdminAction::className(), 'targetAttribute' => ['admin_action_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_id' => 'ID аккаунта',
            'admin_action_id' => 'ID экшена',
        ];
    }

    /**
     * Gets query for [[Account]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'account_id']);
    }

    /**
     * Gets query for [[AdminAction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdminAction()
    {
        return $this->hasOne(AdminAction::className(), ['id' => 'admin_action_id']);
    }
}
