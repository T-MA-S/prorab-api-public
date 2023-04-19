<?php

use yii\db\Migration;

/**
 * Class m221125_023645_add_mark_to_object_table
 */
class m221125_023645_add_mark_to_object_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('object', 'mark', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('object', 'mark');
    }
}
