<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%additional_category}}`.
 */
class m220622_050827_create_additional_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%additional_category}}', [
            'id' => $this->primaryKey(),
            'object_id' => $this->integer(),
            'category_id' => $this->integer(),
        ]);

        $this->createIndex(
            'inx-additional_category-object_id',
            'additional_category',
            'object_id'
        );

        $this->addForeignKey(
            'fk-additional_category-object_id',
            'additional_category',
            'object_id',
            'object',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'inx-additional_category-category_id',
            'additional_category',
            'category_id'
        );

        $this->addForeignKey(
            'fk-additional_category-category_id',
            'additional_category',
            'category_id',
            'category',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%additional_category}}');
    }
}
