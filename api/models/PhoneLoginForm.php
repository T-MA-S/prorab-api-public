<?php

namespace app\models;

use app\components\forms\BaseLoginForm;

/**
 * PhoneLogin is the model behind the login form.
 *
 * @property string $code
 * @property string $phone
 */
class PhoneLoginForm extends BaseLoginForm
{
    public $code;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['phone', 'code'], 'required'],
            ['code', 'validateCode'],
        ];
    }

    public function validateCode($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if($this->getUser()){
                if (!$this->user->validateCode($this->$attribute)) {
                    $this->addError($attribute, 'Неверный код');
                }
            } else {
                $this->addError($attribute, 'Пользователь с таким телефоном не найден');
            }
        }
    }
}
