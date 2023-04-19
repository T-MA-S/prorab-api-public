<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%chat_message_file}}`.
 */
class m221019_035212_create_chat_message_file_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%chat_message_file}}', [
            'id' => $this->primaryKey(),
            'chat_message_id' => $this->integer(),
            'file' => $this->string(),
            'filename' => $this->string(),
            'type' => $this->integer(),
        ]);

        $this->createIndex(
            'inx-chat_message_file-chat_message_id',
            'chat_message_file',
            'chat_message_id'
        );

        $this->addForeignKey(
            'fk-chat_message_file-chat_message_id',
            'chat_message_file',
            'chat_message_id',
            'chat_message',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%chat_message_file}}');
    }
}
