<?php

use yii\db\Migration;

/**
 * Class m221019_025219_add_fields_to_order_table
 */
class m221019_025219_add_fields_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('order', 'time_from',    $this->string());
        $this->addColumn('order', 'time_to',      $this->string());
        $this->addColumn('order', 'address',      $this->string());
        $this->addColumn('order', 'payment_from', $this->string());
        $this->addColumn('order', 'payment_to',   $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('order', 'time_from');
        $this->dropColumn('order', 'time_to');
        $this->dropColumn('order', 'address');
        $this->dropColumn('order', 'payment_from');
        $this->dropColumn('order', 'payment_to');
    }
}
