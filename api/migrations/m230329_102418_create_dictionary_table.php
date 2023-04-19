<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dictionary}}`.
 */
class m230329_102418_create_dictionary_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%dictionary}}', [
            'id' => $this->primaryKey(),
            'word' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%dictionary}}');
    }
}
