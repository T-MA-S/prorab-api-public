<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%page_content}}`.
 */
class m221222_084705_create_page_content_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%page_content}}', [
            'id' => $this->primaryKey(),
            'page_id' => $this->integer(),
            'title' => $this->string(),
            'text' => $this->text(),
            'created' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated' => $this->dateTime(),
        ]);

        $this->createIndex(
            'inx-page_content-page_id',
            'page_content',
            'page_id'
        );

        $this->addForeignKey(
            'fk-page_content-page_id',
            'page_content',
            'page_id',
            'page',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%page_content}}');
    }
}
