<?php

namespace app\models;

use app\components\interfaces\AuthInterface;
use Yii;
use yii\web\IdentityInterface;
use yii\web\UnauthorizedHttpException;

/**
 * This is the model class for table "account".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $role
 * @property int $active
 * @property string|null $password_hash
 * @property string|null $password_reset_code
 * @property string|null $password_reset_code_expires
 * @property string|null $auth_key
 * @property string|null $access_token
 * @property int|null $expire_at
 * @property string|null $code
 * @property int|null $code_expire
 * @property string|null $created
 *
 * @property AdminActionAccess[] $adminActionAccesses
 * @property BuyingTariff[] $buyingTariffs
 * @property PromocodeActivation[] $promocodeActivations
 * @property User $user
 */
class Account extends \yii\db\ActiveRecord implements IdentityInterface, AuthInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'account';
    }

    public function fields()
    {
        $fields = parent::fields();

        unset(
            $fields['password_hash'],
            $fields['password_reset_code'],
            $fields['password_reset_code_expires'],
            $fields['auth_key'],
            $fields['access_token'],
            $fields['expire_at'],
            $fields['code'],
            $fields['code_expire'],
        );

        return $fields;
    }

    public function extraFields()
    {
        return ['user'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'active', 'expire_at', 'password_reset_code_expires', 'code_expire'], 'integer'],
            [['created'], 'safe'],
            [['role', 'password_hash', 'password_reset_code', 'auth_key', 'access_token', 'code'], 'string', 'max' => 255],
            [['user_id'], 'unique'],
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
            'send_messages' => 'Дублировать уведомления в мессенджеры',
            'messenger' => 'Мессенджеры',
            'role' => 'Роль',
            'active' => 'статус активности аккаунта',
            'password_hash' => 'Hash пароля',
            'password_reset_code' => 'Код для востановления пароля',
            'password_reset_code_expires' => 'Время действия кода востановления пароля',
            'auth_key' => 'Ключ авторизации',
            'access_token' => 'Токен доступа',
            'expire_at' => 'Время действия токена',
            'created' => 'Дата создания',
        ];
    }

    /**
     * Gets query for [[AdminActionAccesses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdminActionAccesses()
    {
        return $this->hasMany(AdminActionAccess::class, ['account_id' => 'id']);
    }

    /**
     * Gets query for [[BuyingTariffs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBuyingTariffs()
    {
        return $this->hasMany(BuyingTariff::class, ['account_id' => 'id']);
    }

    /**
     * Gets query for [[PromocodeActivations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPromocodeActivations()
    {
        return $this->hasMany(PromocodeActivation::class, ['account_id' => 'id']);
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
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // return static::findOne(['access_token' => $token]);
        $account = static::find()->where(['access_token' => $token])->one();
        if (!$account) {
            return false;
        }
        if ($account->expire_at < time()) {
            throw new UnauthorizedHttpException('Токен устарел ', -1);
        } else {
            return $account;
        }
    }

    public static function findByPasswordResetCode($code)
    {
        if ($code) {
            $account = static::find()->where(['password_reset_code' => $code])->one();
            if ($account) {
                if ($account->password_reset_code_expires < time()) {
                    throw new UnauthorizedHttpException('Токен устарел ' . time() . ' ' .  $account->id, -1);
                } else {
                    return $account;
                }
            }
        }
        return false;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function validateCode($code)
    {
        return Yii::$app->security->validatePassword($code, $this->code);
    }

    public function setCode($code)
    {
        $this->code = Yii::$app->security->generatePasswordHash($code);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
        return $this->access_token;
    }

    /**
     * Generates new password reset code
     */
    public function generatePasswordResetCode()
    {
        $this->password_reset_code = Yii::$app->security->generateRandomString() . '_' . time();
        $this->password_reset_code_expires = time() + self::EXPIRE_TIME;
    }

    public static function findByUsername($name)
    {
        return Account::find()->joinWith('user', true)->where(['user.email' => $name])->orWhere(['user.phone' => $name])->one();
    }
}
