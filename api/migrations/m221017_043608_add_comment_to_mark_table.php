<?php

use yii\db\Migration;

/**
 * Class m221017_043608_add_comment_to_mark_table
 */
class m221017_043608_add_comment_to_mark_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('mark', 'comment', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('mark', 'comment');
    }
}
