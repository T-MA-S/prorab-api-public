<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tariff_plan}}`.
 */
class m220620_102714_create_tariff_plan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tariff_plan}}', [
            'id' => $this->primaryKey(),
            'active' => $this->boolean()->notNull()->defaultValue(1),
            'type' => $this->integer(),
            'price' => $this->integer(),
            'duration' => $this->integer(),
            'orders_quantity' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%tariff_plan}}');
    }
}
