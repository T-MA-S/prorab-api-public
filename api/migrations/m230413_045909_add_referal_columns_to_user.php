<?php

use yii\db\Migration;

/**
 * Class m230413_045909_add_referal_columns_to_user
 */
class m230413_045909_add_referal_columns_to_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'referal_code', 'string');
        $this->addColumn('user', 'referal_status', 'integer');
        $this->addColumn('user', 'referal_user_id', 'integer');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'referal_code');
        $this->dropColumn('user', 'referal_status');
        $this->dropColumn('user', 'referal_user_id');
    }
}
