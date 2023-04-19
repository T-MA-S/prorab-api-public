<?php

use yii\db\Migration;

/**
 * Class m230403_080009_add_work_on_weekend_to_user_table
 */
class m230403_080009_add_work_on_weekend_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'work_on_weekend', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'work_on_weekend');
    }
}
