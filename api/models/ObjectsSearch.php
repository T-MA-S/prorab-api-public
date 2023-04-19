<?php


namespace app\models;


use yii\base\Model;

class ObjectsSearch extends User
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
        $query = Objects::find();

        if (!($this->load($params) && $this->validate())) {
            return $query;
        }

        $query->andFilterWhere([
            'like', 'object.name', $this->query
        ]);

        return $query;
    }
}
