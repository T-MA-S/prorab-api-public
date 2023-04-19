<?php

use yii\db\Migration;

/**
 * Class m230313_093002_add_on_moderation_to_object_table
 */
class m230313_093002_add_on_moderation_to_object_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('object', 'on_moderation', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('object', 'on_moderation');
    }
}
