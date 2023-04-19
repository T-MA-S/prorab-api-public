<?php

use yii\db\Migration;

/**
 * Class m220722_041140_add_name_to_tariff_plan_table
 */
class m220722_041140_add_name_to_tariff_plan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('tariff_plan', 'name', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('tariff_plan', 'name');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220722_041140_add_name_to_tariff_plan_table cannot be reverted.\n";

        return false;
    }
    */
}
