<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%blog_section}}`.
 */
class m230201_090534_create_blog_section_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%blog_section}}', [
            'id' => $this->primaryKey(),
            'active' => $this->integer(),
            'title' => $this->string(),
            'description' => $this->text(),
            'image' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%blog_section}}');
    }
}
