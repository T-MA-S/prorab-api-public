<?php

namespace app\components\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use app\components\UploadedFile;

class FileBehavior extends Behavior
{
    public $attribute;
    public $file;

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
        if ($this->owner->$attributeName instanceof UploadedFile) {
            $this->file = $this->owner->$attributeName;
        } else {
            $this->file = UploadedFile::getInstanceByName($attributeName);
        }
        if ($this->file instanceof UploadedFile) {
            $dir = \Yii::getAlias('@webroot') . '/uploads/';
            $oldFilename = $this->owner->getOldAttribute($this->attribute);
            $oldPath = $dir . $oldFilename;

            if ($oldFilename && file_exists($oldPath)) {
                unlink($oldPath);
            }

            $this->owner->$attributeName = uniqid() . '.' . $this->file->extension;
        } else {
            $this->owner->setAttribute($attributeName, $this->owner->getOldAttribute($attributeName));
        }
    }

    public function afterSave($event)
    {
        $attributeName = $this->attribute;

        if ($this->file instanceof UploadedFile) {
            $this->file->saveAs(\Yii::getAlias('@webroot') . '/uploads/' . $this->owner->$attributeName);
        }
    }

    public function afterDelete($event)
    {
        $attributeName = $this->attribute;
        $path = \Yii::getAlias('@webroot') . '/uploads/' . $this->owner->$attributeName;
        if (file_exists($path) && isset($this->owner->$attributeName)) {
            unlink($path);
        }
    }
}
