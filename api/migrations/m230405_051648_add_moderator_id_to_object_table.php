<?php

use yii\db\Migration;

/**
 * Class m230405_051648_add_moderator_id_to_object_table
 */
class m230405_051648_add_moderator_id_to_object_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('object', 'moderator_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('object', 'moderator_id');
    }
}
