<?php

namespace app\models;

use app\components\forms\BaseLoginForm;
use Yii;

/**
 * LoginForm is the model behind the login form.
 *
 * @property string $login
 * @property string $password
 * @property bool $rememberMe
 *
 */
class LoginForm extends BaseLoginForm
{
    public $login;
    public $password;
    public $rememberMe = true;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['login', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (isset($user)) {
                if (!$user->validatePassword($this->$attribute)) {
                    $this->addError($attribute, 'Неверный логин или пароль');
                }
            } else {
                $this->addError($attribute, 'Неверный логин или пароль');
            }
        }
    }
}
