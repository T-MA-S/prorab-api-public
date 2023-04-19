<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%admin_action_access}}`.
 */
class m220620_092950_create_admin_action_access_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%admin_action_access}}', [
            'id' => $this->primaryKey(),
            'account_id' => $this->integer(),
            'admin_action_id' => $this->integer(),
        ]);

        $this->createIndex(
            'inx-admin_action_access-account_id',
            'admin_action_access',
            'account_id'
        );

        $this->addForeignKey(
            'fk-admin_action_access-account_id',
            'admin_action_access',
            'account_id',
            'account',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'inx-admin_action_access-admin_action_id',
            'admin_action_access',
            'admin_action_id'
        );

        $this->addForeignKey(
            'fk-admin_action_access-admin_action_id',
            'admin_action_access',
            'admin_action_id',
            'admin_action',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%admin_action_access}}');
    }
}
