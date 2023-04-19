<?php

use yii\db\Migration;

/**
 * Class m230413_054905_alter_contact_payment_table
 */
class m230413_054905_alter_contact_payment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = $this->db->tablePrefix . 'contact_payment';
        if ($this->db->getTableSchema($tableName, true) !== null) {
            $this->dropTable('{{%contact_payment}}');
        }
        
        $this->createTable('{{%contact_payment}}', [
            'id' => $this->primaryKey(),
            'entity' => $this->string(),
            'user_id' => $this->integer(),
            'entity_id' => $this->integer()
        ]);

        $this->createIndex(
            'inx-contact_payment-user_id',
            'contact_payment',
            'user_id'
        );

        $this->addForeignKey(
            'fk-contact_payment-user_id',
            'contact_payment',
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
        $this->dropTable('{{%contact_payment}}');
    }
}
