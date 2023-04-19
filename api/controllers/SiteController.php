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

class SiteController extends Controller
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
            'except' => ['index', 'error', 'options', 'send-push-notification', 'test', 'doc'],
            'class' =>  HttpBearerAuth::className(),
        ];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow'   => true,
                    'actions' => ['index', 'error', 'options', 'send-push-notification', 'test', 'doc']
                ],
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
            'error' => [
                'class' => 'app\components\actions\JsonErrorAction',
            ],
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

    public function actionPushSubscription()
    {
        $subscription = json_decode(file_get_contents('php://input'), true);

        if (!isset($subscription['endpoint'])) {
            echo 'Error: not a subscription';
            return;
        }

        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'POST':
                // create a new subscription entry in your database (endpoint is unique)
                break;
            case 'PUT':
                // update the key and token of subscription corresponding to the endpoint
                break;
            case 'DELETE':
                // delete the subscription corresponding to the endpoint
                break;
            default:
                echo "Error: method not handled";
                return;
        }
    }

    public function actionSendPushNotification()
    {
        $subscription = Subscription::create([
            'contentEncoding' => 'aes128gcm',
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/fYE93QE16kI:APA91bFjG5kEgb_gaFx2Eh0H_avuLBvfmHU_HbZ0libCW9v1uJpJKxsRtXxwGj-Wd8iH5gBY4skXyjaQ7g2zvrJmCNPWKlAiuKktUNRoQgoHb0jsztgwPfLifyuUQZhMpbZQpIwU4ecP',
            'expirationTime' => null,
            'keys' => [
                'auth' => 'BALIR2S319m1Vl1G-6cNuA',
                'p256dh' => 'BKtKGium8sv9VntAwkD2RLo7lC0F0to93HjVhnA9fGfnwdznqtdvpvYpS3GNgwFyTyY_e4hmeQTPCswUXdRVyzM'
            ]
        ]);

        $auth = array(
            'VAPID' => array(
                'subject' => 'https://prorab-app.local/',
                'publicKey' => 'BMopeGMnmPzqEWroicisUQnjxoRNDHbCmYZwPwdhQNb74-tk0hgIvFLqDen8b5gQh6CIePFc2BSkRZ_eV_xxKKM', // don't forget that your public key also lives in app.js
                'privateKey' => 'uU5UWDSTVN2P1boIEGx5pVPNQJ97pjIWpLCRLC7WA-0', // in the real world, this would be in a secret file
            ),
        );

        $webPush = new WebPush($auth);

        $report = $webPush->sendOneNotification(
            $subscription,
            'Хай Бро!'
        );

        // handle eventual errors here, and remove the subscription from your server if it is expired
        $endpoint = $report->getRequest()->getUri()->__toString();

        if ($report->isSuccess()) {
            echo "[v] Message sent successfully for subscription {$endpoint}.";
        } else {
            echo "[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}";
        }
    }

//    public function actionRegionParsing()
//    {
//        Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
//        return $this->render('region-parsing');
//    }
//
//    public function actionCityParsing()
//    {
//        Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
//        return $this->render('city-parsing');
//    }

//    public function actionTest()
//    {
//        Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
//        return $this->render('test');
//    }

    public function actionDoc()
    {
        $this->layout = 'doc';
        Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
        return $this->render('doc');
    }

    public function actionTest()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['test' => 'test_val'];
    }
}
