<?php

namespace app\components\behaviors;

use app\models\Image;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use app\components\UploadedFile;

class FilesBehavior extends Behavior
{
    const IMAGE_TYPE = 0;
    const FILE_TYPE = 1;

    public $attribute;
    public $files;
    public $maxNumber = 5;
    public $type = self::IMAGE_TYPE;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            ActiveRecord::EVENT_AFTER_INSERT  => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE  => 'afterSave',
            ActiveRecord::EVENT_AFTER_DELETE  => 'afterDelete',
        ];
    }

    public function beforeSave($event)
    {
        $attributeName = $this->attribute;
        $this->files = UploadedFile::getInstancesByName($attributeName);

    }

    public function afterSave($event)
    {
        $className = get_class($this->owner);
        $imagesNumber = Image::find()->where(['model' => 'object', 'model_id' => $this->owner->id])->count();
        $counter =  $this->maxNumber - $imagesNumber;
        foreach ($this->files as $file) {
            if ($counter < 1) break;
            if ($file instanceof UploadedFile) {
                if ($this->type == self::IMAGE_TYPE) {
                    $image = new Image();
                    $image->model = get_class($this->owner)::tableName();
                    $image->model_id = $this->owner->id;
                    $image->filename = $file;
                    $image->save();
                }
            }
            $counter--;
        }
    }

    public function afterDelete($event)
    {
        if ($this->type == self::IMAGE_TYPE) {
            $images = Image::find()->where(['model' => $this->owner::tableName(), 'model_id' => $this->owner->id])->all();
            foreach ($images as $image) {
                $image->delete();
            }
        }
    }
}
