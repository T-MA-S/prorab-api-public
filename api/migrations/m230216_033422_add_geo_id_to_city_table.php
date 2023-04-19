<?php

use yii\db\Migration;

/**
 * Class m230216_033422_add_geo_id_to_city_table
 */
class m230216_033422_add_geo_id_to_city_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('city', 'geo_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('city', 'geo_id');
    }
}
