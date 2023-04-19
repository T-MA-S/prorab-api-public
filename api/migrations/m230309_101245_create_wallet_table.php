<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%wallet}}`.
 */
class m230309_101245_create_wallet_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%wallet}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'money' => $this->integer(),
            'points' => $this->integer()
        ]);

        $this->createIndex(
            'inx-wallet-user_id',
            'wallet',
            'user_id'
        );

        $this->addForeignKey(
            'fk-wallet-user_id',
            'wallet',
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
        $this->dropTable('{{%wallet}}');
    }
}
