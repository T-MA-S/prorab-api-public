<?php

use yii\db\Migration;

/**
 * Class m230216_032525_add_geo_id_to_region_table
 */
class m230216_032525_add_geo_id_to_region_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('region', 'geo_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('region', 'geo_id');
    }
}
