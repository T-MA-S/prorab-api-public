<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order}}`.
 */
class m220621_085038_create_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%order}}', [
            'id' => $this->primaryKey(),
            'object_id' => $this->integer(),
            'user_id' => $this->integer(),
            'about' => $this->text(),
            'confirmed' => $this->integer(),
            'created' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex(
            'inx-order-object_id',
            'order',
            'object_id'
        );

        $this->createIndex(
            'inx-order-user_id',
            'order',
            'user_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%order}}');
    }
}
