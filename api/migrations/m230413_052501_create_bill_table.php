<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%bill}}`.
 */
class m230413_052501_create_bill_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%bill}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'wallet_id' => $this->integer(),
            'appointment' => $this->string(),
            'amount' => $this->decimal(11, 2),
            'status' => $this->integer(),
            'date' => $this->dateTime(),
            'expire_at' => $this->dateTime(),
        ]);

        $this->addForeignKey(
            'fk-bill-user_id',
            'bill',
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
        $this->dropTable('{{%bill}}');
    }
}
