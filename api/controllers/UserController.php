<?php

namespace app\controllers;

use app\components\helpers\ReferralHelper;
use app\components\traits\ModeratableActions;
use Yii;
use app\models\Account;
use app\models\ChatMessage;
use app\models\PasswordForm;
use app\models\LoginForm;
use app\models\PhoneForm;
use app\models\PhoneLoginForm;
use app\models\SignUpForm;
use yii\filters\AccessControl;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use yii\web\UnauthorizedHttpException;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataFilter;
use app\models\User;
use app\models\ContactPayment;
use Egulias\EmailValidator\Result\Reason\CharNotAllowed;
use Throwable;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\ServerErrorHttpException;

class UserController extends ActiveController
{
    use ModeratableActions;
    
    public $modelClass = User::class;

    public $elementName = 'пользователей';

    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => [
                    'http://prorab-app.local',
                    'https://prorab-app.local',
                    'http://5.128.156.25',
                    'http://formantestfront.tw1.ru',
                    'http://tets.foreman-go.ru/'
                ],
                'Access-Control-Request-Method' => ['*'], //['POST', 'PUT', 'GET', 'PATCH', 'DELETE', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'], //['Authorization', 'Accept', 'ORIGIN'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Expose-Headers' => ['*'],
            ],
        ];

        $behaviors['authenticator'] = [
            'except' => ['sign-up','login', 'phone-login', 'guest-view', 'get-code', 'mail-confirm', 'get-password-reset-code', 'reset-password','options'],
            'class' => HttpBearerAuth::class,
        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow'   => true,
                    'actions' => [ 'sign-up', 'login', 'phone-login', 'guest-view', 'get-code', 'mail-confirm', 'get-password-reset-code', 'reset-password', 'options']
                ],
                [
                    'allow'         => true,
                    'actions'       => ['update', 'delete'],
                    'roles'         => ['user'],
                    'matchCallback' => function ($rule) {
                        $id = Yii::$app->request->get('id');
                        return $id == Yii::$app->user->id;
                    },
                ],
                [
                    'allow'   => true,
                    'actions' => ['identity', 'use-promocode', 'identity-update', 'enable-messenger', 'disable-messenger', 'view', 'contact', 'replenish', 'send-mail-confirm-code'],
                    'roles'   => ['user']
                ],
                [
                    'allow' => true,
                    'actions' => ['update', 'delete', 'approve', 'replenish-by-admin', 'reject', 'delete-avatar', 'for-moderation'],
                    'roles' => ['moderator']
                ],
                [
                    'allow'   => true,
                    'roles'   => ['admin']
                ],
            ],
        ];

        return $behaviors;
    }

    protected function verbs()
    {
        return array_merge(parent::verbs(), [
            'reject' => ['PUT', 'PATCH'],
            'approve' => ['PUT', 'PATCH'],
            'remove-avatar' => ['PATCH'],
            'for-moderation' => ['GET'],
            'replenish' => ['PATCH'],
            'replenish-by-admin' => ['PATCH'],
            'use-promocode' => ['POST']
        ]);
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        $actions['create'] = [
            'class' => 'app\components\CreateAction',
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
            'scenario' => $this->createScenario,
        ];
        $actions['index']['prepareSearchQuery'] = [$this, 'prepareSearchQuery'];
        $actions['with-messages'] = $actions['index'];
        $actions['with-messages']['pagination'] = false;
        $actions['guest-view'] = $actions['view'];
        $get = \Yii::$app->request->queryParams;
        if (array_key_exists('pagination', $get)) {
            $actions['index']['pagination'] = $get['pagination'] ?: false;
        }
        return $actions;
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        if (in_array($action->id, ['with-messages'])) {
            foreach ($result as $key => $item) {
                $result[$key]['lastMessage'] = ChatMessage::getLastMessage($result[$key]['id']);
            }
        }
        return $result;
    }

    public function prepareSearchQuery()
    {
        $searchModel = new \app\models\UserSearch();
        $get = \Yii::$app->request->queryParams;
        $query = $searchModel->search($get);
        $dataFilter = new ActiveDataFilter(['searchModel' => $this->modelClass]);

        if ($this->action->id == 'with-messages') {
            $query->leftJoin('chat_message ch', 'ch.from_user_id = user.id OR ch.to_user_id = user.id')
                ->leftJoin('account a', 'a.user_id = user.id')
                ->where(['>','ch.from_user_id', 0])->andWhere(['a.role' => 'user'])->orderBy(['ch.id' => SORT_DESC]);
        }

        if ($dataFilter->load($get)) {
            $filter = $dataFilter->build(false);
            if (!empty($filter)) {
                if ($get['expand']) {
                    foreach (explode(', ', $get['expand']) as $expand) {
                        $query->joinWith($expand);
                    }
                }
                $query->andWhere($filter);
            }
        }

        return $query;
    }

    public function actionReplenish()
    {
        $id = defined('YII_DEBUG') ? \Yii::$app->params['debugUserId'] : \Yii::$app->user->id;

        $user = User::findOrFail($id);

        return $user->replenishByPayment(Yii::$app->getRequest()->getBodyParams());
    }

    public function actionReplenishByAdmin($id)
    {
        $user = User::findOrFail($id);

        return $user->replenishByAdmin(Yii::$app->getRequest()->getBodyParams());
    }

    public function actionSignUp($ref = '')
    {
        if($ref) {
            ReferralHelper::setReferrerId($ref);
        }

        $model = new SignUpForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $signUp = $model->signUp();
        if ($model->validate() &&  $signUp === true) {
            if ($model->sendCode()) {
                return [
                    'message' => 'Мы отправили код на ваш телефон'
                ];
            }
        }

        return $model;
    }

    public function actionLogin()
    {
        $model = new LoginForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $accessToken = $model->login();
        if ($accessToken) {
            return [
                'id'           => Yii::$app->user->id,
                'phone'        => Yii::$app->user->identity->user->phone,
                'email'        => Yii::$app->user->identity->user->email,
                'role'         => Yii::$app->user->identity->role,
                'access_token' => $accessToken,
            ];
        } else {
            return $model;
        }
    }

    public function actionPhoneLogin()
    {
        $model = new PhoneLoginForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $accessToken = $model->login();
        if ($accessToken) {
            return [
                'id'           => Yii::$app->user->id,
                'phone'        => Yii::$app->user->identity->user->phone,
                'email'        => Yii::$app->user->identity->user->email,
                'role'         => Yii::$app->user->identity->role,
                'access_token' => $accessToken,
            ];
        } else {
            return $model;
        }
    }

    public function actionGetCode()
    {
        $model = new PhoneForm();
        $model->load(Yii::$app->getRequest()->get(), '');
        if ($model->sendCode()) {
            return [
                'message' => 'Мы отправили код на ваш телефон'
            ];
        }
        return $model;
    }

    public function actionUsePromocode()
    {
        $data = Yii::$app->getRequest()->getBodyParams();
        if(!array_key_exists('code', $data)){
            throw new BadRequestHttpException('Код не указан');
        }

        $id = defined('YII_DEBUG') ? \Yii::$app->params['debugUserId'] : \Yii::$app->user->id;

        $user = User::findOrFail($id);

        if(
            $user->referal_status !== User::REFERAL_USED &&
            $user->referal_status !== User::REFERAL_ACTIVE
        ) {
            $userCode = User::findOne(['referal_code' => $data['code']]);
            if(!$userCode){
                throw new NotFoundHttpException('Такой код не найден');
            }

            $user->referal_status = User::REFERAL_ACTIVE;
            $user->referal_user_id = $userCode->id;

            if($user->save()){
                return $user->id;
            } else {
                throw new ServerErrorHttpException('Ошибка при активации кода');
            }
        } else {
            throw new NotAcceptableHttpException('Реферальная система уже активированна');
        }

        return false;
    }

    public function actionDeleteAvatar($id = false)
    {
        if($id === false){
            $user = Yii::$app->user->identity->user;
        } else {
            $user = User::findOne($id);
        }

        if(!$user){
            throw new NotFoundHttpException('Пользователь не найден');
        }

        return $user->deleteAvatar();
    }

    public function actionIdentity()
    {
        return Yii::$app->user->identity->user;
    }

    public function actionIdentityUpdate()
    {
        $user = Yii::$app->user->identity->user;
        $user->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($user->save() === false && !$user->hasErrors()) {
            throw new ServerErrorHttpException('Что-то пошло не так.');
        }
        return $user;
    }

    public function actionChangePassword()
    {
        $passwordForm = new PasswordForm();
        $passwordForm->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($passwordForm->validate()) {
            return $passwordForm->update();
        }
        return $passwordForm;
    }

    public function actionGetPasswordResetCode($email)
    {
        $model = $this->modelClass::find()->where(['email' => $email])->one();
        if (isset($model)) {
            $model->account->generatePasswordResetCode();
            if ($model->account->save()) {
                return [
                    'username'            => $model->name,
                    'password_reset_code' => $model->account->password_reset_code,
                ];
            } else {
                $model->addError('email', 'Ошибка генерации токена, попробуйте ещё раз.');
                return $model;
            }
        } else {
            $model->addError('email', 'Пользовытеля с таким E-mail не существует.');
            return $model;
        }
    }

    public function actionResetPassword()
    {
        $params = Yii::$app->getRequest()->getBodyParams();
        $account = Account::findByPasswordResetCode($params['password_reset_code']);
        if ($account) {
            $model = $account->user;
            $model->password = $params['password'];
            if (!$model->save()) {
                return $model;
            }
        } else {
            throw new UnauthorizedHttpException('Неверный токен ', -1);
        }
    }

    public function actionContact($id)
    {
        $model = $this->modelClass::findOne($id);

        if (isset($model)) {
            $paid = false;
            $payment = ContactPayment::find()->where([
                'user_request_id' => Yii::$app->user->id,
                'user_response_id' => $id
            ])->one();
            if (isset($payment)) {
                $paid = $payment->paid ? true : false;
            }

            if ($paid) {
                return [
                    'id'       => $model->id,
                    'email'    => $model->email,
                    'phone'    => $model->phone,
                    'telegram' => $model->telegram,
                    'whatsapp' => $model->whatsapp,
                    'viber'    => $model->viber,
                ];
            } else {
                throw new UnauthorizedHttpException('Это платная услуга, для того чтобы открыть контакты нужно оплатить доступ', -1);
            }
        } else {
            throw new NotFoundHttpException('Пользователь не найдет');
        }
    }

    public function actionMailConfirm()
    {
         $params = Yii::$app->getRequest()->getBodyParams();
         $user = User::findByMailConfirmCode($params['code']);
         if (!empty($user)) {
            $user->mail_confirmed = 1;
            $user->save();
            return $user;
         } else {
            throw new NotFoundHttpException('Битая ссылка');
         }
    }

    public function actionSendMailConfirmCode()
    {
        $user = User::findOne(Yii::$app->user->id);
        $user->generateMailConfirmCode();
        if ($user->save()) {
            Yii::$app->mailer->compose('user/mail-confirm', ['url' => $user->getMailConfirmUrl()])
                ->setFrom(Yii::$app->params['senderEmail'])
                ->setTo($user->email)
                ->setSubject('Подтверждение почты для сайта ' . Yii::$app->params['siteUrl'])
                ->send();
            return $user;
        } else {
            throw new ServerErrorHttpException('Возникла ошибка при генерации кода подтверждения почты');
        }
    }

    public function actionEnableMessenger()
    {
        $user = User::findOne(Yii::$app->user->id);
        if($user->setMessengerStatus(1)){
            return 'success';
        } else {
            throw new ServerErrorHttpException('Возникла ошибка при включении рассылки в месенджер');
        }
    }

    public function actionDisableMessenger()
    {
        $user = User::findOne(Yii::$app->user->id);
        if($user->setMessengerStatus(0)){
            return 'success';
        } else {
            throw new ServerErrorHttpException('Возникла ошибка при отключении рассылки в месенджер');
        }
    }

    public function actionUploading()
    {
        $get =Yii::$app->getRequest()->get();
        $attribute = $get['field'];
        $result = $this->modelClass::find()
            ->joinWith('account')
            ->where([
                'between',
                'account.created',
                $get['dateStart'],
                $get['dateEnd']
            ])
            ->all();
        $path = \Yii::getAlias('@webroot') . '/uploads/UserInfoUpload.csv';
        $fp = fopen($path, 'w');
        foreach ($result as $item){
            fputcsv($fp, [$item->$attribute]);
        }
        fclose($fp);
        return ['link' => Url::home('https') . 'uploads/UserInfoUpload.csv'];
    }
}
