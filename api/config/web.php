<?php
$prefix = (is_file(__DIR__ . '/local.db.php')) ? 'local.' : '';

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . "/{$prefix}db.php";
$urls = require __DIR__ . '/urls.php';

$request_headers        = apache_request_headers();
$http_origin            = $request_headers['Origin'];
$allowed_http_origins   = array(
    "http://prorab-app.local",
    "https://prorab-app.local",
);

if (!in_array($http_origin, $allowed_http_origins)) {
    $http_origin = "http://prorab-app.local";
}

$config = [
    'id' => 'basic',
    'name' => 'Прораб-API',
    'language' => 'ru_RU',
    'homeUrl' => '/',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        //        'assetManager' => [
        //            'baseUrl' => '/web/assets',
        //        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '1v3ZOSlsy3KMl5frcaEpFlqG9susz8Tn',
            //            'baseUrl' => '',
            'parsers' => [
                'multipart/form-data' => 'yii\web\MultipartFormDataParser',
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'response' => [
            // 'format' =>  \yii\web\Response::FORMAT_JSON,
            'on beforeSend' => function ($event) use ($http_origin) {
                $response = $event->sender;
                if ($response->format == \yii\web\Response::FORMAT_JSON && \Yii::$app->controller->id != 'authentication') {
                    $data = $response->data;
                    $response->data = [
                        'success' => $response->isSuccessful,
                        'data' => $data,
                        'status' => $response->statusCode
                    ];
                    $response->statusCode = 200;
                }
            },

        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\Account',
            'enableAutoLogin' => true,
            'enableSession' => false,
        ],
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
            'defaultRoles' => ['admin', 'moderator', 'tech', 'user'],
        ],
        'errorHandler' => [
            // 'behaviors' => \Yii::$app->errorHandler->getBehaviors(),
            'errorAction' => 'site/error',
        ],
//        'mailer' => [
//            'class' => 'yii\swiftmailer\Mailer',
//            // send all mails to a file by default. You have to set
//            // 'useFileTransport' to false and configure transport
//            // for the mailer to send real emails.
//            'useFileTransport' => true,
//            'enableSwiftMailerLogging' => true,
//            'transport' => [
//                'class' => 'Swift_SmtpTransport',
//                'host' => 'smtp.yandex.ru',
//                'username' => '',
//                'password' => '',
//                'port' => '465',
//                'encryption' => 'ssl',
//            ],
//        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'transport' => [
                'scheme' => 'smtps',
                'host' => 'smtp.yandex.ru',
                'username' => 'noreply@foreman-go.ru',
                'password' => 'kgv@cppMo5Hu?xXvm6TmTqUgEta*0{rpK8|W',
                'encryption' => 'ssl',
                'port' => 465,
//                'dsn' => 'native://default',
            ],
            'viewPath' => '@app/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => $urls,
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '5.128.156.25'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
