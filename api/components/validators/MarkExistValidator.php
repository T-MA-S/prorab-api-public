<?php

namespace app\components\validators;

use yii\validators\Validator;

class MarkExistValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $mark = $model::find()->where([
            'user_from_id' => $model->user_from_id,
            'user_to_id' => $model->user_to_id
        ]);
        if ($mark->one()) {
            $this->addError($model, $attribute, 'Вы уже ставили оценку этому пользователю');
        }
    }
}