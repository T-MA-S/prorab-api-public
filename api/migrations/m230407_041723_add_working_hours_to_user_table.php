<?php

use yii\db\Migration;

/**
 * Class m230407_041723_add_working_hours_to_user_table
 */
class m230407_041723_add_working_hours_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'working_hours_start', $this->time()->notNull()->defaultValue('00:00'));
        $this->addColumn('user', 'working_hours_end', $this->time()->notNull()->defaultValue('24:00'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'working_hours_start');
        $this->dropColumn('user', 'working_hours_end');
    }
}
