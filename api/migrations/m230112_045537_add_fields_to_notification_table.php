<?php

use yii\db\Migration;

/**
 * Class m230112_045537_add_fields_to_notification_table
 */
class m230112_045537_add_fields_to_notification_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('notification', 'user_from_id',    $this->integer());
        $this->addColumn('notification', 'type',            $this->integer());

        $this->createIndex(
            'inx-notification-user_from_id',
            'notification',
            'user_from_id'
        );

        $this->addForeignKey(
            'fk-notification-user_from_id',
            'notification',
            'user_from_id',
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
        $this->dropColumn('notification', 'user_from_id');
        $this->dropColumn('notification', 'type');
    }
}
