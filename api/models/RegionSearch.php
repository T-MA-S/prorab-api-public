<?php


namespace app\models;

use yii\base\Model;
use app\models\Region;

class RegionSearch extends Region
{
    public $query;

    public function rules()
    {
        return [
            ['query', 'string'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * @param $params
     * @return \yii\db\ActiveQuery
     */
    public function search($params)
    {
        $query = Region::find();

        if (!($this->load($params) && $this->validate())) {
            return $query;
        }

        $query->andFilterWhere([
            'like', 'name', $this->query
        ]);

        return $query;
    }
}
