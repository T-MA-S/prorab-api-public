<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use linslin\yii2\curl;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\filters\ContentNegotiator;
use yii\filters\auth\HttpBearerAuth;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\RegisteredClaims;
use CoderCat\JWKToPEM\JWKConverter;

class AuthenticationController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                // restrict access to
                'Origin' => ['http://prorab-app.local', 'https://prorab-app.local', 'http://5.128.156.25'],
                // Allow only POST and PUT methods
                'Access-Control-Request-Method' => ['*'], //['POST', 'PUT', 'GET', 'PATCH', 'DELETE', 'OPTIONS'],
                // Allow only headers 'X-Wsse'
                'Access-Control-Request-Headers' => ['*'], //['Authorization', 'Accept', 'ORIGIN'],
                // Allow credentials (cookies, authorization headers, etc.) to be exposed to the browser
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Expose-Headers' => ['*'],
            ],
        ];

        $behaviors['authenticator'] = [
            'except' => ['index', 'options', 'jwks', 'test'],
            'class' =>  HttpBearerAuth::className(),
        ];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow'   => true,
                    'actions' => ['index', 'options', 'jwks', 'test']
                ],
                [
                    'allow'   => true,
                    'actions' => ['notification'],
                    'roles' => ['user']
                ]
            ],
        ];

        return $behaviors;
    }

    public function beforeAction($action)
    {
        if (in_array($action->id, ['error'])) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        }
        return parent::beforeAction($action);
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
        return $this->render('index');
    }


    public function actionJwks()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['keys' => [
            [
                "kty" => "RSA",
                "e" => "AQAB",
                "use" => "sig",
                "kid" => "jMOOP4TgqVlbPN4K9sgaJit3Wjl8g1c80FBRwWc2pWY",
                "alg" => "RS256",
                "n" => "uUqFwuB6ltKEWae3Z1Dr0vHFTQLdAICUoEyA64h4tsa2PKnPKsyqf_6qJqMmBQvQdlmf76VvWv-9unDP7tby8VMTV4V4FQF9VidqH0fVuAWjVxjwkdcryyRx6KSyVa807qN-zPWubHxsAAI7LQ1CetlbYNh6bHoruA_JDRlVohg4AR5B4U-ebmQNHlVa6TITOCSTANb5tCNZ_iF1-hzKX9juOqrTPcpwXoOubzPwmZX6eCcqtw8rEbd7rMvy05KA15Qrpti7TMnHWQVU4dLQcTNS5DYH2VRIX3R27lihgZAogWtb3HsHVr46bO718zt86sTMk1svVHeR8skdRgc8lw"
            ],
            [
                "kty" => "RSA",
                "e" => "AQAB",
                "use" => "enc",
                "kid" => "ZSu2NYdPqGExoMISj6Nthu2Ddb2uRnkWvtojgyjjU5Q",
                "alg" => "RSA-OAEP",
                "n" => "1UV1s04ouUp-YypPe1--2uQTLTXP7A0S4MU-4wp203hV4YO5HZSC2zJuqWMBxW1mpqhPm6DUfR-JkKvevk5aCdkpto6Dw8yC7RfnTIwPRJY-KqnKMsAVnPSIDjEEFHC5T9d9aM4oQjy1MBwaZV7vKMlpLPi7I_ooKMZ6nPQmuaTdo92yHQ9sBE1T1LKH-8-jgPz9DDbTGLG8Q2UrxWudyu2exeSRl_rYvEHjPSAe6_A-r_RkLgotIRV9HguxCVnhdAbJKWjTRTfKtjVrE-PppvLVE52BalxiHVgFM_hymnjZsM8GntmmjK-bBWDi2go2sc0or7EA_JiWKS5e7x9XUw"
            ]
        ]];
    }

    public function actionNotification()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        print_r ('Notification');
    }

    public function actionTest()
    {

    }
}
