<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%object}}`.
 */
class m220621_035356_create_object_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%object}}', [
            'id' => $this->primaryKey(),
            'image' => $this->string(),
            'name' => $this->string(),
            'model' => $this->string(),
            'about' => $this->text(),
            'price_1' => $this->integer(),
            'price_2' => $this->integer(),
            'quantity' => $this->integer(),
            'status_busy' => $this->boolean()->notNull()->defaultValue(0),
            'status' => $this->boolean()->notNull()->defaultValue(0),
            'active' => $this->boolean()->notNull()->defaultValue(0),
            'user_id' => $this->integer(),
            'city_id' => $this->integer(),
            'type' => $this->integer()->notNull()->defaultValue(0),
            'category_id' => $this->integer(),
            'created' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated' => $this->dateTime(),
        ]);

        $this->createIndex(
            'inx-object-city_id',
            'object',
            'city_id'
        );

        $this->addForeignKey(
            'fk-object-city_id',
            'object',
            'city_id',
            'city',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'inx-object-user_id',
            'object',
            'user_id'
        );

        $this->addForeignKey(
            'fk-object-user_id',
            'object',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'inx-object-category_id',
            'object',
            'category_id'
        );

        $this->addForeignKey(
            'fk-object-category_id',
            'object',
            'category_id',
            'category',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%object}}');
    }
}
