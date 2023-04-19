<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%schedule_is_busy}}`.
 */
class m220621_055009_create_schedule_is_busy_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%schedule_is_busy}}', [
            'id' => $this->primaryKey(),
            'object_id' => $this->integer(),
            'datetime' => $this->dateTime(),
            'date_from' => $this->dateTime(),
            'duration' => $this->integer(),
            'status' => $this->integer(),
        ]);

        $this->createIndex(
            'inx-schedule_is_busy-object_id',
            'schedule_is_busy',
            'object_id'
        );

        $this->addForeignKey(
            'fk-schedule_is_busy-object_id',
            'schedule_is_busy',
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
        $this->dropTable('{{%schedule_is_busy}}');
    }
}
