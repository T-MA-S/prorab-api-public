<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%faq_element}}`.
 */
class m230203_092645_create_faq_element_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%faq_element}}', [
            'id' => $this->primaryKey(),
            'section_id' => $this->integer(),
            'active' => $this->integer(),
            'question' => $this->text(),
            'answer' => $this->text(),
        ]);

        $this->createIndex(
            'inx-faq_element-section_id',
            'faq_element',
            'section_id'
        );

        $this->addForeignKey(
            'fk-faq_element-section_id',
            'faq_element',
            'section_id',
            'faq_section',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%faq_element}}');
    }
}
