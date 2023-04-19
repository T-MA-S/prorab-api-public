<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%contact_payment}}`.
 */
class m221017_052449_create_contact_payment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%contact_payment}}', [
            'id' => $this->primaryKey(),
            'user_request_id' => $this->integer(),
            'user_response_id' => $this->integer(),
            'paid' => $this->integer(),
        ]);

        $this->createIndex(
            'inx-contact_payment-user_request_id',
            'contact_payment',
            'user_request_id'
        );

        $this->createIndex(
            'inx-contact_payment-user_response_id',
            'contact_payment',
            'user_response_id'
        );

        $this->addForeignKey(
            'fk-contact_payment-user_request_id',
            'contact_payment',
            'user_request_id',
            'user',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-contact_payment-user_response_id',
            'contact_payment',
            'user_response_id',
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
        $this->dropTable('{{%contact_payment}}');
    }
}
