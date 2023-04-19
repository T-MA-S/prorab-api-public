<?php

use yii\db\Migration;

/**
 * Class m220822_045230_add_type_to_category_table
 */
class m220822_045230_add_type_to_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('category', 'type', $this->integer()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('category', 'type');
    }
}
