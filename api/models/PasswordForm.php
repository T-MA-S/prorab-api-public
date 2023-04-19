<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 *
 */
class PasswordForm extends Model
{
    public $password;
    public $passwordNew;
    public $passwordConfirm;

    protected $_user;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['password', 'passwordNew', 'passwordConfirm'], 'required'],
            ['passwordConfirm', 'compare',
                'compareAttribute' => 'passwordNew',
                'skipOnEmpty' => false,
                'message' => 'Пароли должны совпадать'
            ],
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
            $this->_user = Account::findOne(Yii::$app->user->id);

            if (!$this->_user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неверный логин или пароль');
            }
        }
    }

    public function update()
    {
        if(empty($this->_user)) {
            $this->_user = Account::findOne(Yii::$app->user->id);
        }
        $this->_user->setPassword($this->passwordNew);
        return $this->_user->save();
    }
}
