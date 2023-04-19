<?php

namespace app\components\validators;

use yii\validators\Validator;

class SelfMarkValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if ($model->user_from_id == $model->user_to_id) {
            $this->addError($model, $attribute, 'Нельзя ставить себе оценку');
        }
    }
}