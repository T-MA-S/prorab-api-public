<?php


namespace app\models;


use yii\base\Model;

class UserSearch extends User
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
        $query = User::find();

        if (!($this->load($params) && $this->validate())) {
            return $query;
        }

        $query->andFilterWhere([
            'or',
            ['like', 'email', $this->query],
            ['like', 'name', $this->query],
            ['like', 'phone', $this->query],
        ]);

        return $query;
    }
}
