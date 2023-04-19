<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%blog_article}}`.
 */
class m230201_093957_create_blog_article_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%blog_article}}', [
            'id' => $this->primaryKey(),
            'active' => $this->integer(),
            'section_id' => $this->integer(),
            'title' => $this->string(),
            'preview' => $this->text(),
            'text' => $this->text(),
            'image' => $this->string(),
            'view_count' => $this->integer()->defaultValue(0),
            'created' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated' => $this->dateTime(),
        ]);

        $this->createIndex(
            'inx-blog_article-section_id',
            'blog_article',
            'section_id'
        );

        $this->addForeignKey(
            'fk-blog_article-section_id',
            'blog_article',
            'section_id',
            'blog_section',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%blog_article}}');
    }
}
