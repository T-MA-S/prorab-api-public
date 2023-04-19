<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%blog_comment}}`.
 */
class m230202_084355_create_blog_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%blog_comment}}', [
            'id' => $this->primaryKey(),
            'article_id' => $this->integer(),
            'user_id' => $this->integer(),
            'text' => $this->text(),
            'created' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'parent_id' => $this->integer(),
            'tree' => $this->integer(),
            'lft' => $this->integer()->notNull(),
            'rgt' => $this->integer()->notNull(),
            'depth' => $this->integer()->notNull(),
            'position' => $this->integer()->notNull()->defaultValue(0),
        ]);

        $this->createIndex(
            'inx-blog_comment-article_id',
            'blog_comment',
            'article_id'
        );

        $this->addForeignKey(
            'fk-blog_comment-article_id',
            'blog_comment',
            'article_id',
            'blog_article',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'inx-blog_comment-user_id',
            'blog_comment',
            'user_id'
        );

        $this->addForeignKey(
            'fk-blog_comment-user_id',
            'blog_comment',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%blog_comment}}');
    }
}
