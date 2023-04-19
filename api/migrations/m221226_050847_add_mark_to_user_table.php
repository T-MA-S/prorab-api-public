<?php

use yii\db\Migration;

/**
 * Class m221226_050847_add_mark_to_user_table
 */
class m221226_050847_add_mark_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'mark', $this->float());
        $this->dropColumn('object', 'mark');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('object', 'mark', $this->float());
        $this->dropColumn('user', 'mark');
    }
}
