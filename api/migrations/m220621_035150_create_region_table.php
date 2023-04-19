<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%region}}`.
 */
class m220621_035150_create_region_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%region}}', [
            'id' => $this->primaryKey(),
            'country_id' => $this->integer(),
            'name' => $this->string(),
            'active' => $this->boolean()->notNull()->defaultValue(1),
        ]);

        $this->createIndex(
            'inx-region-country_id',
            'region',
            'country_id'
        );

        $this->addForeignKey(
            'fk-region-country_id',
            'region',
            'country_id',
            'country',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%region}}');
    }
}
