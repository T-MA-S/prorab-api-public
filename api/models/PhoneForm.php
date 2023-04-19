<?php

namespace app\models;

use app\components\forms\BaseLoginForm;
use linslin\yii2\curl;

/**
 * PhoneForm is the model behind the login form.
 *
 * @property string $phone
 *
 */
class PhoneForm extends BaseLoginForm
{
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['phone'], 'required'],
        ];
    }

    public function sendCode()
    {
        $this->phone = '+' . substr($this->phone, 1);
        if ($this->getUser()) {
            if (in_array($this->phone, $this->devPhones)) {
                $this->user->setCode(1111);
                $this->user->code_expire = time() + static::CODE_EXPIRE;
                if ($this->user->save()) {
                    return true;
                }
            } else {
                $code = random_int(1000, 9999);
                $this->user->setCode($code);
                $this->user->code_expire = time() + static::CODE_EXPIRE;
                if ($this->user->save()) {
                    $curl = new curl\Curl();
                    $data = [
                        'login' => 'migo',
                        'psw' => 'E8K{eblLstitPO8~P4MYfCdE*QLylCa$5bo}',
                        'sender' => 'foreman-go',
                        'phones' => $this->phone,
                        'mes' => 'Код для входа на сайт foreman-go.ru: ' . $code
                    ];
                    $result = $curl->setHeaders([])->setGetParams($data)->get('https://smsc.ru/sys/send.php');
                    if (strripos($result, 'OK') !== false) {
                        return true;
                    }
                }
            }
            $this->addError('phone', 'Произошла ошибка при отправке кода');
        } else {
            $this->addError('phone', 'Пользователь с таким телефоном не найден');
        }
        return false;
    }
}
