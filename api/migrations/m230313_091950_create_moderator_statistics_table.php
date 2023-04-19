<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%moderator_statistics}}`.
 */
class m230313_091950_create_moderator_statistics_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%moderator_statistics}}', [
            'id' => $this->primaryKey(),
            'moderator_id' => $this->integer(),
            'model' => $this->string(),
            'model_id' => $this->integer(),
            'date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')
        ]);

        $this->createIndex(
            'inx-moderator_statistics-moderator_id',
            'moderator_statistics',
            'moderator_id'
        );

        $this->addForeignKey(
            'fk-moderator_statistics-moderator_id',
            'moderator_statistics',
            'moderator_id',
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
        $this->dropTable('{{%moderator_statistics}}');
    }
}
