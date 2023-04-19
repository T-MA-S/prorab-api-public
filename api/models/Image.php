<?php

namespace app\models;

use app\components\behaviors\FileBehavior;

/**
 * This is the model class for table "image".
 *
 * @property int $id
 * @property string|null $model
 * @property int|null $model_id
 * @property string|null $filename
 */
class Image extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['file'] = [
            'class' => FileBehavior::class,
            'attribute' => 'filename'
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['model', 'model_id', 'filename'], 'required'],
            [['model_id'], 'integer'],
            [['model'], 'string', 'max' => 255],
            [['filename'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model' => 'Model',
            'model_id' => 'Model ID',
            'filename' => 'Filename',
        ];
    }

    public function getModelName()
    {
        $tableSplit = explode("_", $this->model);
        $model = 'app\models\\';
        foreach ($tableSplit as $item) {
            if ($item == 'object') $item = 'objects';
            $model .= ucfirst($item);
        }
        return $model;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
//        $model = $this->getModelName()::findOne($this->model_id);
//        $attribute = $model->getBehavior('files')->attribute;
//        $image = Image::findOne(['filename' => $model->$attribute]);
//        if (empty($image)) {
//            $path = \Yii::getAlias('@webroot') . '/uploads/' . $model->$attribute;
//            if (file_exists($path) && isset($model->$attribute)) {
//                unlink($path);
//            }
//            $model->$attribute = $this->filename;
//            $model->save();
//        }
    }
}
