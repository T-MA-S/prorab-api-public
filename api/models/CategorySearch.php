<?php


namespace app\models;

use yii\base\Model;
use app\models\Category;

class CategorySearch extends Category
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
        $query = Category::find();

        if (!($this->load($params) && $this->validate())) {
            return $query;
        }

        $query->andFilterWhere([
            'like', 'title', $this->query
        ]);

        return $query;
    }
}
