<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%faq_section}}`.
 */
class m230203_092439_create_faq_section_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%faq_section}}', [
            'id' => $this->primaryKey(),
            'type_id' => $this->integer(),
            'active' => $this->integer(),
            'title' => $this->string(),
        ]);

        $this->createIndex(
            'inx-faq_section-type_id',
            'faq_section',
            'type_id'
        );

        $this->addForeignKey(
            'fk-faq_section-type_id',
            'faq_section',
            'type_id',
            'faq_type',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%faq_section}}');
    }
}
