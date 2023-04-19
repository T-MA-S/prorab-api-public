<?php

use yii\db\Migration;

/**
 * Class m230403_054445_add_fields_to_object_table
 */
class m230403_054445_add_fields_to_object_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('object', 'schedule_type', $this->tinyInteger()->notNull()->defaultValue(0));
        $this->addColumn('object', 'work_on_weekend', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('object', 'schedule_type');
        $this->dropColumn('object', 'work_on_weekend');
    }
}
