<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%charity_fund}}`.
 */
class m230203_083742_create_charity_fund_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%charity_fund}}', [
            'id' => $this->primaryKey(),
            'active' => $this->integer(),
            'title' => $this->string(),
            'description' => $this->text(),
            'link' => $this->string(),
            'logo' => $this->string(),
            'image' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%charity_fund}}');
    }
}
