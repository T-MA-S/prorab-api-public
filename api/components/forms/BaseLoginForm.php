<?php

namespace app\components\forms;

use app\components\interfaces\AuthInterface;
use app\models\Account;
use yii\base\Model;
use Yii;

/**
 * BaseLoginForm is the base model behind the login form.
 *
 * @property-read Account $user
 *
 */
class BaseLoginForm extends Model implements AuthInterface
{
    protected $user;
    public $phone;

    protected $devPhones = YII_ENV === 'dev' ?
        ['+7 (999) 468 51 26', '+7 (911) 111 11 11', '+7 (999) 999 99 99', '+7 (913) 484 35 97'] :
        ['+7 (999) 468 51 26'];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [];
    }

    /**
     * Finds user by [[username]]
     *
     * @return Account|false
     */
    public function getUser()
    {
        // $purePhone = str_replace(['+', '(', ')', ' ', '-'], '', $this->phone);

        $this->user = Account::findByUsername($this->phone ?: $this->login);

        if(!$this->user) {
            return false;
        }

        return $this->user;
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if (static::validate()) {
            if ($this->getUser()) {
                $access_token = $this->user->generateAccessToken();
                $this->user->expire_at = time() + self::EXPIRE_TIME;
                $this->user->code = '';
                $this->user->save();
                Yii::$app->user->login($this->user, self::EXPIRE_TIME);
                return $access_token;
            }
        }
        return false;
    }
}
