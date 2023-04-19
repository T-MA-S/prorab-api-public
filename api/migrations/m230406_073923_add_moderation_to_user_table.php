<?php

use yii\db\Migration;

/**
 * Class m230406_073923_add_moderation_to_user_table
 */
class m230406_073923_add_moderation_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'status', 'integer');
        $this->addColumn('user', 'on_moderation', 'integer');
        $this->addColumn('user', 'moderator_id', 'integer');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'status');
        $this->dropColumn('user', 'on_moderation');
        $this->dropColumn('user', 'moderator_id');
    }
}
