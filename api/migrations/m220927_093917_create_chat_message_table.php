<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%chat_message}}`.
 */
class m220927_093917_create_chat_message_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%chat_message}}', [
            'id' => $this->primaryKey(),
            'from_user_id' => $this->integer(),
            'to_user_id' => $this->integer(),
            'text' => $this->text(),
            'viewed' => $this->boolean()->notNull()->defaultValue(0),
            'created' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'view_time' => $this->dateTime()
        ]);

        $this->createIndex(
            'inx-chat_message-from_user_id',
            'chat_message',
            'from_user_id'
        );

        $this->createIndex(
            'inx-chat_message-to_user_id',
            'chat_message',
            'to_user_id'
        );

        $this->addForeignKey(
            'fk-chat_message-from_user_id',
            'chat_message',
            'from_user_id',
            'user',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-chat_message-to_user_id',
            'chat_message',
            'to_user_id',
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
        $this->dropTable('{{%chat_message}}');
    }
}
