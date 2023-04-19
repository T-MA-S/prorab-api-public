<?php

use yii\db\Migration;

/**
 * Class m220719_032347_change_tree_field_category_table
 */
class m220719_032347_change_tree_field_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('category', 'tree');
        $this->addColumn('category', 'tree', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('category', 'tree');
        $this->addColumn('category', 'tree', $this->integer()->notNull());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220719_032347_change_tree_field_category_table cannot be reverted.\n";

        return false;
    }
    */
}
