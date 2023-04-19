<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mark}}`.
 */
class m220621_090038_create_mark_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mark}}', [
            'id' => $this->primaryKey(),
            'user_from_id' => $this->integer(),
            'user_to_id' => $this->integer(),
            'mark' => $this->float(),
            'date' => $this->dateTime(),
        ]);

        $this->createIndex(
            'inx-mark-user_from_id',
            'mark',
            'user_from_id'
        );

        $this->addForeignKey(
            'fk-mark-user_from_id',
            'mark',
            'user_from_id',
            'user',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'inx-mark-user_to_id',
            'mark',
            'user_to_id'
        );

        $this->addForeignKey(
            'fk-mark-user_to_id',
            'mark',
            'user_to_id',
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
        $this->dropTable('{{%mark}}');
    }
}
