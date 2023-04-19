<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%promocode_activation}}`.
 */
class m220621_025603_create_promocode_activation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%promocode_activation}}', [
            'id' => $this->primaryKey(),
            'datetime' => $this->dateTime(),
            'account_id' => $this->integer(),
            'promocode_id' => $this->integer()
        ]);

        $this->createIndex(
            'inx-promocode_activation-account_id',
            'promocode_activation',
            'account_id'
        );

        $this->addForeignKey(
            'fk-promocode_activation-account_id',
            'promocode_activation',
            'account_id',
            'account',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'inx-promocode_activation-promocode_id',
            'promocode_activation',
            'promocode_id'
        );

        $this->addForeignKey(
            'fk-promocode_activation-promocode_id',
            'promocode_activation',
            'promocode_id',
            'promocode',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%promocode_activation}}');
    }
}
