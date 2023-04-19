<?php

use yii\db\Migration;

/**
 * Class m230131_090739_add_fields_to_account_table
 */
class m230131_090739_add_fields_to_account_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('account', 'code',        $this->string());
        $this->addColumn('account', 'code_expire', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('account', 'code');
        $this->dropColumn('account', 'code_expire');
    }
}
