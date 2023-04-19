<?php

use yii\db\Migration;

/**
 * Class m230127_040836_change_invoice_id_to_order_table
 */
class m230127_040836_change_invoice_id_to_order_table extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('order', 'invoice_id');
        $this->addColumn('order', 'invoice_id', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('order', 'invoice_id');
        $this->addColumn('order', 'invoice_id', $this->integer());
    }
}
