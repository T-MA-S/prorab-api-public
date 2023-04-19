<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%booking}}`.
 */
class m220621_085504_create_booking_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%booking}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer(),
            'date_from' => $this->dateTime(),
            'created' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'duration' => $this->integer(),
            'updated' => $this->dateTime(),
        ]);

        $this->createIndex(
            'inx-booking-order_id',
            'booking',
            'order_id'
        );

        $this->addForeignKey(
            'fk-booking-user_id',
            'booking',
            'order_id',
            'order',
            'id',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%booking}}');
    }
}
