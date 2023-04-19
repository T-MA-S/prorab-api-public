<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user}}`.
 */
class m230331_050705_add_messengers_columns_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'send_messages', 'tinyint');
        $this->addColumn('user', 'messenger', 'string');
        $this->addColumn('user', 'chat_id', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'send_messages');
        $this->dropColumn('user', 'messenger');
        $this->dropColumn('user', 'chat_id');
    }
}
