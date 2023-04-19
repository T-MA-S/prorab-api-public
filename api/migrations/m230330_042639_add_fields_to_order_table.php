<?php

use yii\db\Migration;

/**
 * Class m230330_042639_add_fields_to_order_table
 */
class m230330_042639_add_fields_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('order', 'archive', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('order', 'deleted', $this->integer()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('order', 'archive');
        $this->dropColumn('order', 'deleted');
    }
}
