<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%buying_tariff}}`.
 */
class m220620_110749_create_buying_tariff_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%buying_tariff}}', [
            'id' => $this->primaryKey(),
            'account_id' => $this->integer(),
            'tariff_id' => $this->integer(),
            'datetime' => $this->dateTime(),
            'is_payed' => $this->boolean()->notNull()->defaultValue(0),
        ]);

        $this->createIndex(
            'inx-buying_tariff-account_id',
            'buying_tariff',
            'account_id'
        );

        $this->addForeignKey(
            'fk-buying_tariff-account_id',
            'buying_tariff',
            'account_id',
            'account',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'inx-buying_tariff-tariff_id',
            'buying_tariff',
            'tariff_id'
        );

        $this->addForeignKey(
            'fk-buying_tariff-tariff_id',
            'buying_tariff',
            'tariff_id',
            'tariff_plan',
            'id',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%buying_tariff}}');
    }
}
