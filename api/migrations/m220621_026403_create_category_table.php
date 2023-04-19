<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%category}}`.
 */
class m220621_026403_create_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%category}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(),
            'parent_id' => $this->integer(),
            'tree' => $this->integer()->notNull(),
            'lft' => $this->integer()->notNull(),
            'rgt' => $this->integer()->notNull(),
            'depth' => $this->integer()->notNull(),
            'position' => $this->integer()->notNull()->defaultValue(0),
        ]);

        $this->createIndex(
            'inx-category-parent_id',
            'category',
            'parent_id'
        );

        $this->createIndex('inx-category-lft', 'category', ['tree', 'lft', 'rgt']);
        $this->createIndex('inx-category-rgt', 'category', ['tree', 'rgt']);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%category}}');
    }
}
