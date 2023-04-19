<?php

use yii\db\Migration;

/**
 * Class m221003_080012_add_foreign_key_to_order_table
 */
class m221003_080012_add_foreign_key_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addForeignKey(
            'fk-order-object_id',
            'order',
            'object_id',
            'object',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-order-object_id', 'order');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221003_080012_add_foreign_key_to_order_table cannot be reverted.\n";

        return false;
    }
    */
}
