<?php

use yii\db\Migration;

/**
 * Class m230407_040113_add_busy_to_user_table
 */
class m230407_040113_add_busy_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'busy', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'busy');
    }
}
