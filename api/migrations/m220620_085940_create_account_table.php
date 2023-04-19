<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%account}}`.
 */
class m220620_085940_create_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%account}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'role' => $this->string()->defaultValue('user'),
            'active' => $this->boolean()->notNull()->defaultValue(1),
            'password_hash' => $this->string(),
            'password_reset_code' => $this->string(),
            'password_reset_code_expires' => $this->integer(),
            'auth_key' => $this->string(),
            'access_token' => $this->string(),
            'expire_at' => $this->integer(),
            'created' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex(
            'inx-account-user_id',
            'account',
            'user_id',
            true
        );

        $this->addForeignKey(
            'fk-account-user_id',
            'account',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }



    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%account}}');
    }
}
