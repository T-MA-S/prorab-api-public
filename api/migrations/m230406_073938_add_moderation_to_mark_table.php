<?php

use yii\db\Migration;

/**
 * Class m230406_073938_add_moderation_to_mark_table
 */
class m230406_073938_add_moderation_to_mark_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('mark', 'on_moderation', 'integer');
        $this->addColumn('mark', 'moderator_id', 'integer');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('mark', 'on_moderation');
        $this->dropColumn('mark', 'moderator_id');
    }
}
