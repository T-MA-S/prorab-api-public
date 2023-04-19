<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%notification}}`.
 */
class m220621_103441_create_notification_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%notification}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'model' => $this->string(),
            'model_id' => $this->integer(),
            'text' => $this->text(),
            'created' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'new' => $this->integer()->notNull()->defaultValue(1),
        ]);

        $this->createIndex(
            'inx-notification-user_id',
            'notification',
            'user_id'
        );

        $this->addForeignKey(
            'fk-notification-user_id',
            'notification',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%notification}}');
    }
}
