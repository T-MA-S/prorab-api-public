<?php

use yii\db\Migration;

/**
 * Class m220819_052654_remove_datetime_field_from_schedule_is_busy_table
 */
class m220819_052654_remove_datetime_field_from_schedule_is_busy_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('schedule_is_busy', 'datetime');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('schedule_is_busy', 'datetime', $this->dateTime());
    }
}
