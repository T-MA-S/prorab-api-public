<?php

namespace app\models;

use Yii;
use app\components\helpers\ReferralHelper;
use app\models\Wallet;
use Throwable;
use yii\web\ServerErrorHttpException;

/**
 * Register form.
 *
 * @property-read User|null $user
 *
 */
class SignUpForm extends PhoneForm
{
    public $name;
    public $email;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['name', 'trim'],
            [['name', 'phone', 'email'], 'required'],
            [['name'], 'string', 'min' => 3, 'max' => 255],
            ['phone', 'unique', 'targetClass' => User::class, 'message' => 'Пользователь с таким номером телефона уже существует'],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'Пользователь с таким E-mail уже существует'],
            ['email', 'email'],
        ];
    }

    public function signUp()
    {
        $user = new User();

        $user->load($this->attributes, '');
        $user->password = Yii::$app->security->generateRandomString();
        $user->referal_code = Yii::$app->security->generateRandomString(12);

        if(ReferralHelper::getReferrerId() !== null){
            if($refUser = User::findOne(
                ['referal_code' => ReferralHelper::getReferrerId()]
            )) {
                $user->referal_user_id = $refUser->id;
                $user->referal_status = $user::REFERAL_ACTIVE;
            }
        }

        return $user->save();
    }
}
