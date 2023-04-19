<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%payment_history}}`.
 */
class m230413_045935_create_payment_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%payment_history}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'wallet_id' => $this->integer(),
            'type' => $this->integer(),
            'direction' => $this->integer(),
            'appointment' => $this->string(),
            'amount' => $this->integer(),
            'date' => $this->dateTime(),
            'invoice_id' => $this->string(),
            'status' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-wallet-wallet_id',
            'payment_history',
            'wallet_id',
            'wallet',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%payment_history}}');
    }
}
