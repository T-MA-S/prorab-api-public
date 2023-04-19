<?php

use yii\db\Migration;

/**
 * Class m230411_040937_add_moderation_to_order_table
 */
class m230411_040937_add_moderation_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('order', 'status', 'integer');
        $this->addColumn('order', 'on_moderation', 'integer');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('order', 'status');
        $this->dropColumn('order', 'on_moderation');
    }
}
