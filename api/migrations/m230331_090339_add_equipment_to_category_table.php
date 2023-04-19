<?php

use yii\db\Migration;

/**
 * Class m230331_090339_add_equipment_to_category_table
 */
class m230331_090339_add_equipment_to_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('category', 'equipment', $this->integer()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('category', 'equipment');
    }
}
