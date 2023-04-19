<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%admin_action}}`.
 */
class m220620_092732_create_admin_action_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%admin_action}}', [
            'id' => $this->primaryKey(),
            'url' => $this->string(),
            'title' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%admin_action}}');
    }
}
