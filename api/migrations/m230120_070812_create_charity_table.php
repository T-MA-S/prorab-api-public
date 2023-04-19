<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%charity}}`.
 */
class m230120_070812_create_charity_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%charity}}', [
            'id' => $this->primaryKey(),
            'fio' => $this->string(),
            'email' => $this->string(),
            'phone' => $this->string(),
            'fund' => $this->string(),
            'comment' => $this->text(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%charity}}');
    }
}
