<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%complaint}}`.
 */
class m220622_044142_create_complaint_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%complaint}}', [
            'id' => $this->primaryKey(),
            'object_id' => $this->integer(),
            'count' => $this->integer(),
        ]);

        $this->createIndex(
            'inx-complaint-object_id',
            'complaint',
            'object_id'
        );

        $this->addForeignKey(
            'fk-complaint-object_id',
            'complaint',
            'object_id',
            'object',
            'id',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%complaint}}');
    }
}
