<?php

namespace app\models;

use Yii;
use creocoder\nestedsets\NestedSetsBehavior;
use app\components\behaviors\FileBehavior;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string $title
 * @property int|null $parent_id
 * @property int $tree
 * @property int $lft
 * @property int $rgt
 * @property int $depth
 * @property int $position
 * @property int $type
 * @property string|null $image
 * @property string|null $price_1_name
 * @property string|null $price_2_name
 * @property int $equipment
 *
 * @property AdditionalCategory[] $additionalCategories
 * @property Objects[] $objects
 */
class Category extends \yii\db\ActiveRecord
{
    public $objectsCountByType;
    public $favouritesObjectsCountByType;
    public $amountOfChildren;

    public $objectCount;
    public $children;
    public $parent;

    public $parent_price_1_name;
    public $parent_price_2_name;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['file'] = [
            'class' => FileBehavior::class,
            'attribute' => 'image'
        ];
        $behaviors['nestedTree'] = [
            'class' => NestedSetsBehavior::class,
            'treeAttribute' => 'tree'
        ];
        return $behaviors;
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    public function fields()
    {
        $fields = parent::fields();

        $fields['amountOfChildren'] = function ($model) {
            return $model->children()->count();
        };

        $fields['parent_price_1_name'] = function ($model) {
            return Category::getPriceName($this->id, 1);
        };

        $fields['parent_price_2_name'] = function ($model) {
            return Category::getPriceName($this->id, 2);
        };

        if (in_array(Yii::$app->controller->action->id, ['admin-list'])) {
            $fields['objectCount'] = function ($model) {
                return $model->getObjectsCount();
            };
            $fields['children'] = function ($model) {
                return $model->children(1)->all();
            };
        }

        if (in_array(Yii::$app->controller->action->id, ['search'])) {
            $fields['parent'] = function ($model) {
                return static::findOne($this->parent_id);
            };
        }


        return $fields;
    }

    public function extraFields()
    {
        return ['objects', 'objectsByType', 'objectsCountByType'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['parent_id', 'type', 'tree', 'lft', 'rgt', 'depth', 'position', 'equipment'], 'integer'],
            [['title', 'image', 'price_1_name', 'price_2_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
            'parent_id' => 'ID родительская категория',
            'tree' => 'Tree',
            'lft' => 'Lft',
            'rgt' => 'Rgt',
            'depth' => 'Depth',
            'position' => 'Position',
            'type' => 'Тип',
            'image' => 'Изображение',
        ];
    }

    /**
     * Gets query for [[AdditionalCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdditionalCategories()
    {
        return $this->hasMany(AdditionalCategory::className(), ['category_id' => 'id']);
    }

    /**
     * Gets query for [[Objects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObjects()
    {
        return $this->hasMany(Objects::className(), ['category_id' => 'id']);
    }

//    public function getParent()
//    {
//        return $this->hasOne(Category::className(), ['id' => 'parent_id']);
//    }
//
//    public function getChildren()
//    {
//        return $this->hasMany(Category::className(), ['parent_id' => 'id']);
//    }

    /**
     * Gets query for [[Objects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObjectsByType()
    {
        return $this->hasMany(Objects::className(), ['type' => 'type']);
    }

    public static function getPriceName(int $id, int $number = 1)
    {
        $model = static::findOne($id);
        $attribute = 'price_' . $number . '_name';
        $result = $model->$attribute;
        if (empty($result)) {
            $result = static::getPriceName($model->parent_id, $number);
        }
        return $result;
    }

    public function getObjectsCount()
    {
        $children = $this->children()->all();
        $ids = [$this->id];
        foreach ($children as $child) {
            $ids[] = $child->id;
        }

        return Objects::find()->where(['in', 'category_id', $ids])->count();
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $parent = static::findOne($this->parent_id);
            if ($parent->equipment) {
                $this->equipment =1;
            }
        }
        return parent::beforeSave($insert);
    }

//    public function getObjectsCountByType()
//    {
//        return Objects::find()->where(['type' => $this->type, 'user_id' => Yii::$app->user->id])->count();
//    }
}
