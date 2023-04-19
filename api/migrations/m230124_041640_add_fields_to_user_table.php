<?php

use yii\db\Migration;

/**
 * Class m230124_041640_add_fields_to_user_table
 */
class m230124_041640_add_fields_to_user_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('user', 'mail_confirmed',            $this->integer());
        $this->addColumn('user', 'mail_confirm_code_expires', $this->integer());
        $this->addColumn('user', 'mail_confirm_code',         $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'mail_confirmed');
        $this->dropColumn('user', 'mail_confirm_code_expires');
        $this->dropColumn('user', 'mail_confirm_code');
    }
}
