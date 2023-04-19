<?php

use yii\db\Migration;

/**
 * Class m230328_072341_add_status_to_mark_table
 */
class m230328_072341_add_status_to_mark_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('mark', 'status', 'tinyint');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('mark', 'status');
    }
}
