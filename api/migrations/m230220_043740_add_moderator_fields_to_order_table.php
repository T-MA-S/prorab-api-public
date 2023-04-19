<?php

use yii\db\Migration;

/**
 * Class m230220_043740_add_moderator_fields_to_order_table
 */
class m230220_043740_add_moderator_fields_to_order_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('order', 'verified', $this->integer());
        $this->addColumn('order', 'moderator_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('order', 'verified');
        $this->dropColumn('order', 'moderator_id');
    }
}
