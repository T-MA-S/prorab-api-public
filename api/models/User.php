<?php

namespace app\models;

use Yii;
use app\components\behaviors\FileBehavior;
use app\components\bots\base\BaseBot;
use app\components\interfaces\AuthInterface;
use app\components\interfaces\PaymentInterface;
use app\components\payment\BillPayment;
use app\components\payment\IncomePayment;
use app\components\payment\OutcomePayment;
use app\components\traits\ActiveRecordExtend;
use app\components\traits\Moderadable;
use app\models\Wallet;
use Throwable;
use yii\base\Exception;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $avatar
 * @property string|null $telegram
 * @property string|null $whatsapp
 * @property string|null $viber
 * @property float|null $mark
 * @property int|null $mail_confirmed
 * @property int|null $mail_confirm_code_expires
 * @property string|null $mail_confirm_code
 * @property int $work_on_weekend
 * @property int $busy
 * @property string $working_hours_start
 * @property string $working_hours_end
 *
 * @property Account $account
 * @property Favourites[] $favourites
 * @property Mark[] $marks
 * @property Mark[] $marks0
 * @property Notification[] $notifications
 * @property Objects[] $objects
 */
class User extends \yii\db\ActiveRecord implements AuthInterface, PaymentInterface
{
    use Moderadable, ActiveRecordExtend;
    
    const ROLE_ADMIN = 'admin';
    const ROLE_MODERATOR = 'moderator';
    const ROLE_TECH = 'tech';
    const ROLE_USER = 'user';

    const REFERAL_DISABLED = 0;
    const REFERAL_ACTIVE = 1;
    const REFERAL_USED = 2;

    const TELEGRAM = 1;
    const WHATSAPP = 2;

    public $password;

    protected Wallet $wallet;

    public $amountOfObjects;
    public $amountOfConfirmedOrders;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['file'] = [
            'class' => FileBehavior::class,
            'attribute' => 'avatar'
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    public function fields()
    {
        $fields = parent::fields();
        $role = '';
        if (isset(Yii::$app->user->identity)) {
            $role = Yii::$app->user->identity->role;
        }

        $unset = empty(Yii::$app->user->id);
        $unset = $unset || ($role == static::ROLE_USER && (Yii::$app->user->id != $this->id));
        $unset = $unset && (!in_array(Yii::$app->controller->action->id, ['contact', 'order-contacts', 'confirm']));
        if (in_array(Yii::$app->controller->id, ['order', 'object']) && in_array(Yii::$app->controller->action->id, ['all', 'index'])) {
            $order = Order::find()->joinWith('object')->where([
                'order.confirmed' => 1
            ])
            ->andWhere(['or',['object.user_id' => $this->id], ['order.user_id' => $this->id]])
            ->andWhere(['or',['object.user_id' => Yii::$app->user->id], ['order.user_id' => Yii::$app->user->id]])
            ->one();

            if ($order) {
                $unset = false;
            }
        }
        if ($unset) {
            unset($fields['email'], $fields['phone'], $fields['telegram'], $fields['whatsapp'], $fields['viber']);
        }
        unset(
            $fields['mail_confirm_code_expires'],
            $fields['mail_confirm_code'],
        );

        if (in_array($role, [static::ROLE_MODERATOR, static::ROLE_ADMIN]) || Yii::$app->user->id == $this->id) {
            $fields['amountOfObjects'] = function ($model) {
                return Objects::find()->where(['user_id' =>$this->id])
                    ->andWhere('status != :status', ['status'=> Objects::DELETED])
                    ->count();
            };

            $fields['amountOfConfirmedOrders'] = function ($model) {
                return Order::find()->joinWith('object')->where([
                    'object.user_id' =>$this->id,
                    'order.confirmed' => 1
                ])
                    ->andWhere(['or',['<>', 'rejected', 1], ['rejected' => null]])
                    ->andWhere(['archive' => 0, 'deleted' => 0])
                    ->count();
            };
        }

        if (in_array($role, [static::ROLE_MODERATOR, static::ROLE_ADMIN])) {
            $fields['statistics'] = function ($model) {
                $result = [
                    'objectsToday' => ModeratorStatistics::find()
                        ->where([
                            'model' => 'object',
                            'moderator_id' => $this->id
                        ])
                        ->andWhere(['>', 'date', date("Y-m-d")])
                        ->count(),
                    'objectsPerWeek' => ModeratorStatistics::find()
                        ->where([
                            'model' => 'object',
                            'moderator_id' => $this->id
                        ])
                        ->andWhere(['>', 'date', date('Y-m-d', strtotime('-1 week'))])
                        ->count(),
                ];
                return $result;
            };
        }

        $fields['working_hours_start'] = function ($model) {
            return date('H:i', strtotime($this->working_hours_start));
        };

        $fields['working_hours_end'] = function ($model) {
            return date('H:i', strtotime($this->working_hours_end));
        };

        return $fields;
    }

    public function extraFields()
    {
        return ['account', 'wallet_id', 'marksTo', 'messagesTo', 'messagesFrom', 'objects'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', 'trim'],
            [['name', 'phone', 'email'], 'required'],
            [['name', 'send_messages'], 'string', 'min' => 3, 'max' => 255],
            [['mark'], 'number'],
            ['phone', 'unique', 'message' => 'Пользователь с таким номером телефона уже существует'],
            ['email', 'unique', 'message' => 'Пользователь с таким E-mail уже существует'],
            ['email', 'email'],
            [['mail_confirmed', 'mail_confirm_code_expires', 'messengers_active', 'work_on_weekend', 'busy'], 'integer'],
            [['working_hours_start', 'working_hours_end'], 'safe'],
            [['email', 'messenger', 'chat_id', 'phone', 'avatar', 'telegram', 'whatsapp', 'viber', 'password', 'mail_confirm_code'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'email' => 'E-mail',
            'chat_id' => 'Чат телеграм бота',
            'phone' => 'Телефон',
            'avatar' => 'Аватарка',
            'telegram' => 'Telegram',
            'whatsapp' => 'Whatsapp',
            'viber' => 'Viber',
        ];
    }

    /**
     * Gets query for [[Account]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Favourites]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFavourites()
    {
        return $this->hasMany(Favourites::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Marks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMarksFrom()
    {
        return $this->hasMany(Mark::class, ['user_from_id' => 'id']);
    }

    /**
     * Gets query for [[Marks0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMarksTo()
    {
        return $this->hasMany(Mark::class, ['user_to_id' => 'id']);
    }

    /**
     * Gets query for [[Notifications]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNotifications()
    {
        return $this->hasMany(Notification::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Objects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObjects()
    {
        return $this->hasMany(Objects::class, ['user_id' => 'id'])
            ->where(['or',['object.status' => Objects::APPROVED], ['object.id' => null]]);
    }

    public static function getMessengerBot($botId)
    {
        return BaseBot::getBotClass($botId);
    }

    public function getMessagesTo()
    {
        return $this->hasMany(ChatMessage::class, ['to_user_id' => 'id']);
    }

    public function getMessagesFrom()
    {
        return $this->hasMany(ChatMessage::class, ['from_user_id' => 'id']);
    }

    public function setMessengerStatus($status)
    {
        $this->messengers_active = $status;
        
        return $this->save();
    }

    public function getAvgMark()
    {
        $mark = Mark::find()->select('AVG(mark)')
            ->where(['user_to_id' => $this->id, 'status' => Mark::APPROVED])
            ->scalar();

        return $mark;
    }

    public function setMark()
    {
        $this->mark = round($this->getAvgMark(), 1);
    }

    public function getWallet()
    {
        if(!isset($this->wallet)){
            $this->wallet = Wallet::find()->where('user_id = ' . $this->id)->one();
        }
        return $this->wallet;
    }

    public function setWallet($value)
    {
        $this->wallet = $value;
    }

    public function replenishByBill($paymentData)
    {
        $payment = new BillPayment();
        $payment->setAttributes($paymentData, false);

        return $this->replenish($payment);
    }

    public function replenishByAdmin($paymentData)
    {
        $payment = new BillPayment();
        $payment->money()
                ->setAttributes($paymentData);

        if(!array_key_exists('appointment', $paymentData)) {
            $payment->appointment = 'Пополнение кошелька администрацией';
        }

        return $this->replenish($payment);
    }

    public function replenishByPayment($paymentData)
    {
        $payment = new IncomePayment();
        $payment->money()
                ->setAttributes($paymentData);

        return $this->replenish($payment);
    }

    protected function replenish($payment) 
    {
        if(!$payment->validate()){
            throw new HttpException(402, json_encode($payment->getErrors()), 402);
        }

        $userWallet = $this->getWallet();
        $userWallet->payment = $payment;

        try {
            $userWallet->executePayment();

            if ($this->referal_status === $this::REFERAL_ACTIVE) {
                if(!empty($this->referal_user_id) && $payment->amount >= Yii::$app->params['minReferralPayment']) {
                    $this->sendReferrals($payment);
                }
            }

            return true;

        } catch (Throwable $e) {
            $payment->status = $this::PAYMENT_FAILURE;
            $payment->appointment = $e->getMessage();
            $payment->saveHistory();
        }

        return false;
    }

    public function pay($paymentData)
    {
        $payment = new OutcomePayment();

        $payment->setAttributes($paymentData);

        throw new HttpException(402, json_encode($payment->attributes), 402);
        
        if(!$payment->validate()){
            throw new HttpException(402, json_encode($payment->getErrors()), 402);
        }

        $wallet = $this->getWallet();
        $wallet->payment = $payment;

        return $wallet->executePayment();
    }

    protected function sendReferrals($payment)
    {
        $referedUserWallet = Wallet::find()->where('user_id = :r_id', [':r_id' => $this->referal_user_id])->one();
        if($referedUserWallet) {
            $transaction = $this::getDb()->beginTransaction();
            try {
                $payment->appointment = "Реферальные баллы";
                $payment->points()
                        ->amount = Yii::$app->params['referalPoints'];

                $referedUserWallet->payment = $payment;

                if(!$referedUserWallet->executePayment()) {
                    throw new Exception('Ошибка при начислении реферальных баллов');
                };

                $wallet = clone $this->getWallet();
                $wallet->payment = $payment;

                if(!$wallet->executePayment()) {
                    throw new Exception('Ошибка при начислении реферальных баллов');
                };

                $this->referal_status = $this::REFERAL_USED;
                if(!$this->save(false)){
                    throw new Exception('Ошибка при изменении статуса реферала');
                }

                $transaction->commit();

            } catch(Exception $e) {
                $payment->status = $this::PAYMENT_FAILURE;
                $payment->appointment = $e->getMessage();
                $payment->saveHistory();
                $transaction->rollBack();
                return $e->getMessage();
            }

            return true;
        }

        return false;
    }

    /**
     * @return bool 
     */
    public function deleteAvatar()
    {
        $this->detachBehavior('file');
        
        $path = \Yii::getAlias('@webroot') . '/uploads/';
        
        if(file_exists($path . $this->avatar) && isset($this->avatar)){
            unlink($path . $this->avatar);
        }

        $this->avatar = null;

        return $this->save(false);
    }

    /**
     * Generates new mail confirm code
     */
    public function generateMailConfirmCode()
    {
        $this->mail_confirmed = 0;
        $this->mail_confirm_code = Yii::$app->security->generateRandomString() . '_' . time();
        $this->mail_confirm_code_expires = time() + self::EXPIRE_TIME;
    }

    public function checkMailConfirmCode($code)
    {
        if ($code) {
            if ($this->mail_confirm_code === $code) {
                if ($this->mail_confirm_code_expires < time()) {
                    throw new UnauthorizedHttpException('Код подтверждения устарел.', -1);
                } else {
                    return true;
                }
            } else {
                throw new UnauthorizedHttpException('Неверный код подтверждения.', -1);
            }
        }
        return false;
    }

    public function getMailConfirmUrl()
    {
        return 'https://' . Yii::$app->params['siteUrl'] . '/mail-confirm?code=' . $this->mail_confirm_code;
    }

    public static function findByMailConfirmCode($code)
    {
        if ($code) {
            $user = static::find()->where(['mail_confirm_code' => $code])->one();
            if ($user) {
                if ($user->mail_confirm_code_expires < time()) {
                    throw new UnauthorizedHttpException('Код подтверждения устарел.', -1);
                } else {
                    return $user;
                }
            }
        }
        return false;
    }

    public static function findByPhone($phone)
    {
        if ($phone) {
            $user = static::find()->where(['phone' => $phone])->one();
            if (!empty($user)) {
                return $user;
            }
        }
        return false;
    }

    public function beforeSave($insert)
    {
        if ($this->getOldAttribute('email') != $this->email) {
            $this->generateMailConfirmCode();
        }
        if ($this->getOldAttribute('mail_confirmed') != $this->mail_confirmed && $this->mail_confirmed) {
            if (Yii::$app->controller->action->id != 'mail-confirm') {
                throw new UnauthorizedHttpException('Чтобы подтвердить почту перейдите по ссылке в письме, которое мы вам прислали на указанный вами email', -1);
            }
        }

        $this->moderate();

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if($insert){
            $wallet = new Wallet();
            $wallet->user_id = $this->id;
            $wallet->money = 0;
            $wallet->points = 0;
            $wallet->save();
        }

        if ((array_key_exists('email', $changedAttributes) || $insert) && isset($this->email)) {
            Yii::$app->mailer->compose('user/mail-confirm', ['url' => $this->getMailConfirmUrl()])
                ->setFrom(Yii::$app->params['senderEmail'])
                ->setTo($this->email)
                ->setSubject('Подтверждение почты для сайта ' . Yii::$app->params['siteUrl'])
                ->send();
        }

        if (array_key_exists('work_on_weekend', $changedAttributes) && $changedAttributes['work_on_weekend'] != $this->work_on_weekend) {
            $sql = "UPDATE object SET work_on_weekend = " . $this->work_on_weekend . " WHERE user_id = " . $this->id;
            \Yii::$app->db->createCommand($sql)->execute();
        }

        $account = Account::find()->where(['user_id' => $this->id])->one();

        if (empty($account)) {
            $account = new Account();
            $account->user_id = $this->id;
            $account->role = 'user';
            $account->active = 1;
            $account->save(false);
        }
        if ($this->password) {
            $account->setPassword($this->password);
        }

        $account->save(false);
    }

    public static function getDocTag()
    {
        $tag = [
            'name' => 'User',
            'x-displayName' => 'Пользователи',
            'description' => 'Методы для работы с пользователями',
        ];
    }

    public static function getDocPaths()
    {
        $paths = [
            'user' => [
                'post' => [
                    'tags' => ['User'],
                    'summary' => 'Создание пользователя',
                    'description' => 'Создание пользователя (доступно только Админу)',
                    'parameters' => [
                        [
                            'name' => "Accept",
                            'description' => 'Для получения ответа в формате JSON',
                            'in' => 'header',
                            'required' => true,
                            'schema' => [
                                'type' => "string",
                                'default' => 'application/json'
                            ]
                        ],
                        [
                            'name' => "Content-Type",
                            'description' => 'Наш API принимает только JSON запросы',
                            'in' => 'header',
                            'required' => true,
                            'schema' => [
                                'type' => "string",
                                'default' => 'application/json'
                            ]
                        ]
                    ],
                    'requestBody' => [
                        '$ref' => '#/components/requestBodies/User'
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Пользователь успешно создан',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
