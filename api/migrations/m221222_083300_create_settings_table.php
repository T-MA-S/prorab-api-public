<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%settings}}`.
 */
class m221222_083300_create_settings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%settings}}', [
            'id' => $this->primaryKey(),
            'type_id' => $this->integer(),
            'name' => $this->string(),
            'title' => $this->string(),
            'text' => $this->text(),
        ]);

        $this->createIndex(
            'inx-settings-type_id',
            'settings',
            'type_id'
        );

        $this->addForeignKey(
            'fk-settings-type_id',
            'settings',
            'type_id',
            'settings_type',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%settings}}');
    }
}
