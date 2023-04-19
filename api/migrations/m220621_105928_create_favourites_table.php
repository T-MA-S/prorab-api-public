<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%favourites}}`.
 */
class m220621_105928_create_favourites_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%favourites}}', [
            'id' => $this->primaryKey(),
            'object_id' => $this->integer(),
            'user_id' => $this->integer(),
        ]);

        $this->createIndex(
            'inx-favourites-object_id',
            'favourites',
            'object_id'
        );

        $this->addForeignKey(
            'fk-favourites-objects_id',
            'favourites',
            'object_id',
            'object',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'inx-favourites-user_id',
            'favourites',
            'user_id'
        );

        $this->addForeignKey(
            'fk-favourites-user_id',
            'favourites',
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
        $this->dropTable('{{%favourites}}');
    }
}
