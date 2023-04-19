<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%faq_type}}`.
 */
class m230203_092352_create_faq_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%faq_type}}', [
            'id' => $this->primaryKey(),
            'active' => $this->integer(),
            'title' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%faq_type}}');
    }
}
