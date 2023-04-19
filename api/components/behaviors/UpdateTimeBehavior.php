<?php

namespace app\components\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

class UpdateTimeBehavior extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
        ];
    }

    public function beforeSave($event)
    {
        if ($this->owner->scenario != 'not_update') {
            $date = new \DateTime();
            $this->owner->updated = $date->format('Y-m-d H:i:s');
        }
    }
}
