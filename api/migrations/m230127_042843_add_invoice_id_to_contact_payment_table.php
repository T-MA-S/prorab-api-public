<?php

use yii\db\Migration;

/**
 * Class m230127_042843_add_invoice_id_to_contact_payment_table
 */
class m230127_042843_add_invoice_id_to_contact_payment_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('contact_payment', 'invoice_id', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('contact_payment', 'invoice_id');
    }
}
