<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%image}}`.
 */
class m221222_085105_create_image_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%image}}', [
            'id' => $this->primaryKey(),
            'model' => $this->string(),
            'model_id' => $this->integer(),
            'filename' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%image}}');
    }
}
