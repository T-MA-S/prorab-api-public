<?php

namespace app\models;

use app\components\bots\TelegramBot;
use Yii;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

/**
 * This is the model class for table "notification".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $model
 * @property int|null $model_id
 * @property string|null $text
 * @property string|null $created
 * @property int $new
 * @property int|null $user_from_id
 * @property int|null $type
 *
 * @property User $user
 */
class Notification extends \yii\db\ActiveRecord
{
    const TYPE_OBJECT_REJECTED = 0;
    const TYPE_ORDER_CREATED = 1;
    const TYPE_ORDER_CONFIRMED = 2;
    const TYPE_CHAT_MESSAGE = 3;
    const TYPE_OBJECT_APPROVED = 4;
    const TYPE_ORDER_REJECTED = 5;
    const TYPE_OBJECT_ACTIVATED = 6;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notification';
    }

    public function extraFields()
    {
        return ['user', 'userFrom'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'model_id', 'new', 'user_from_id', 'type'], 'integer'],
            [['text'], 'string'],
            [['created'], 'safe'],
            [['model'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'ID пользователя',
            'model' => 'Название модели',
            'model_id' => 'ID записи',
            'text' => 'Текст уведомления',
            'created' => 'Дата создания',
            'new' => 'Новое',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserFrom()
    {
        return $this->hasOne(User::class, ['id' => 'user_from_id']);
    }

    public static function emailViews()
    {
        return [
            'object/rejected',
            'order/created',
            'order/confirmed',
            'chat/new-message',
            'object/approved',
            'order/rejected',
            'object/activated',
        ];
    }

    protected function sendToTelegram($chat_id)
    {
        $botClass = new TelegramBot($this->text, $chat_id);

        return $botClass->sendMessage();
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

//        $subscription = Subscription::create([
//            'contentEncoding' => 'aes128gcm',
//            'endpoint' => 'http://0.0.0.0:2345', //'https://fcm.googleapis.com/fcm/send/fzEsDAKKfrY:APA91bFhgU6EXXlCUW9yI0X5-gw6E0gKfylqc3hxiv9y9AqCcX126ZweRTGBf6ySKLOYJDCIBolu4qLBHQhaqxHhFGd_zGlFNiooJoBitwyw_9cVNY8baTDyk5uHEHNm5vJj4K4h2pSS',
//            'expirationTime' => null,
//            'keys' => [
//                'auth' => 'vrL07mwN_v3_lUD2eToIlw',
//                'p256dh' => 'BGUl17AxwpneKS9EX_kOsW_KElaAIVXFhI_9lzZYiKfhC400-A32eVbY5K8n4IEpkulPTGBsZ-pfsAty0FxFqLM'
//            ]
//        ]);
//
//        $auth = array(
//            'VAPID' => array(
//                'subject' => 'https://github.com/Minishlink/web-push-php-example/',
//                'publicKey' => 'BMBlr6YznhYMX3NgcWIDRxZXs0sh7tCv7_YCsWcww0ZCv9WGg-tRCXfMEHTiBPCksSqeve1twlbmVAZFv7GSuj0', // don't forget that your public key also lives in app.js
//                'privateKey' => 'vplfkITvu0cwHqzK9Kj-DYStbCH_9AhGx9LqMyaeI6w', // in the real world, this would be in a secret file
//            ),
//        );
//
//        $webPush = new WebPush();
//
//        $report = $webPush->sendOneNotification(
//            $subscription,
//            $this->text
//        );

        $user = $this->user;

        if($user->messengers_active && $user->chat_id){
            $this->sendToTelegram($user->chat_id);
        }
        
        Yii::$app->mailer->compose('notification/' . static::emailViews()[$this->type], ['model' => $this])
            ->setFrom(Yii::$app->params['senderEmail'])
            ->setTo($user->email)
            ->setSubject('Уведомление с сайта ' . Yii::$app->params['siteUrl'])
            ->send();
        
    }
}
