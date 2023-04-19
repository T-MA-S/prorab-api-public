<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%city}}`.
 */
class m220621_035200_create_city_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%city}}', [
            'id' => $this->primaryKey(),
            'region_id' => $this->integer(),
            'name' => $this->string(),
            'active' => $this->boolean()->notNull()->defaultValue(1),
        ]);

        $this->createIndex(
            'inx-city-region_id',
            'city',
            'region_id'
        );

        $this->addForeignKey(
            'fk-city-region_id',
            'city',
            'region_id',
            'region',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%city}}');
    }
}
