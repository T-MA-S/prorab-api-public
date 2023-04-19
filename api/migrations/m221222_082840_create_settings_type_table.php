<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%settings_type}}`.
 */
class m221222_082840_create_settings_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%settings_type}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%settings_type}}');
    }
}
