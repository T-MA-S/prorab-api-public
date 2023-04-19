<?php

use yii\db\Migration;

/**
 * Class m230220_040136_add_rejected_to_order_table
 */
class m230220_040136_add_rejected_to_order_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('order', 'rejected', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('order', 'rejected');
    }
}
