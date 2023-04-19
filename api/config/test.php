<?php
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/test_db.php';

/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'basic-tests',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language' => 'en-US',
    'components' => [
        'db' => $db,
        'mailer' => [
            'useFileTransport' => true,
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            //            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['user' => 'user'],
                    'extraPatterns' => [
                        'POST login' => 'login',
                        'GET identity' => 'identity',
                        'PUT identity-update' => 'identity-update',
                        'GET get-password-reset-code' => 'get-password-reset-code',
                        'PUT reset-password' => 'reset-password',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['account' => 'account'],
                    'extraPatterns' => [],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['country' => 'country'],
                    'extraPatterns' => [],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['region' => 'region'],
                    'extraPatterns' => [],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['city' => 'city'],
                    'extraPatterns' => [],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['category' => 'category'],
                    'extraPatterns' => [],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['object' => 'object'],
                    'extraPatterns' => [],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['additional-category' => 'additional-category'],
                    'extraPatterns' => [],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['admin-action' => 'admin-action'],
                    'extraPatterns' => [],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['admin-action-access' => 'admin-action-access'],
                    'extraPatterns' => [],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['booking' => 'booking'],
                    'extraPatterns' => [],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['tariff-plan' => 'tariff-plan'],
                    'extraPatterns' => [],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['buying-tariff' => 'buying-tariff'],
                    'extraPatterns' => [],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['complaint' => 'complaint'],
                    'extraPatterns' => [],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['favourites' => 'favourites'],
                    'extraPatterns' => [],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['favourites' => 'favourites'],
                    'extraPatterns' => [],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['mark' => 'mark'],
                    'extraPatterns' => [
                        'GET get-mark' => 'get-mark',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['notification' => 'notification'],
                    'extraPatterns' => [],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['order' => 'order'],
                    'extraPatterns' => [],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['promocode' => 'promocode'],
                    'extraPatterns' => [],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['promocode-activation' => 'promocode-activation'],
                    'extraPatterns' => [],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['schedule-is-busy' => 'schedule-is-busy'],
                    'extraPatterns' => [],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['file' => 'file'],
                    'extraPatterns' => [
                        'POST upload' => 'upload',
                    ],
                ],
                '<controller:\w+>/<action:\w+>/' => '<controller>/<action>',
            ],
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
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
            // but if you absolutely need it set cookie domain to localhost
            /*
            'csrfCookie' => [
                'domain' => 'localhost',
            ],
            */
        ],
    ],
    'params' => $params,
];
