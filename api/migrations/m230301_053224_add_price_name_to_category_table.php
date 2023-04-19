<?php

use yii\db\Migration;

/**
 * Class m230301_053224_add_price_name_to_category_table
 */
class m230301_053224_add_price_name_to_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('category', 'price_1_name', $this->string());
        $this->addColumn('category', 'price_2_name', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('category', 'price_1_name');
        $this->dropColumn('category', 'price_2_name');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230301_053224_add_price_name_to_category_table cannot be reverted.\n";

        return false;
    }
    */
}
