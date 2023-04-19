<?php

use yii\db\Migration;

/**
 * Class m230123_092318_add_fields_to_order_table
 */
class m230123_092318_add_fields_to_order_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('order', 'paid',       $this->integer());
        $this->addColumn('order', 'invoice_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('order', 'paid');
        $this->dropColumn('order', 'invoice_id');
    }
}
