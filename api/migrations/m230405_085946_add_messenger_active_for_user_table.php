<?php

use yii\db\Migration;

/**
 * Class m230405_085946_add_messenger_active_for_user_table
 */
class m230405_085946_add_messenger_active_for_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'messengers_active', 'tinyint');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'messengers_active');
    }
}
