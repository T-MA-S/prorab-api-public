<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%promocode}}`.
 */
class m220621_025203_create_promocode_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%promocode}}', [
            'id' => $this->primaryKey(),
            'created' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'duration' => $this->integer(),
            'active_till' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%promocode}}');
    }
}
