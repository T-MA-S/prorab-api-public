<?php

return [
    'enablePrettyUrl' => true,
    //            'enableStrictParsing' => true,
    'showScriptName' => false,
    'rules' => [
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['user' => 'user'],
            'extraPatterns' => [
                'POST sign-up' => 'sign-up',
                'POST login' => 'login',
                'GET guest-view' => 'guest-view',
                'POST phone-login' => 'phone-login',
                'GET identity' => 'identity',
                'GET get-code' => 'get-code',
                'GET with-messages' => 'with-messages',
                'PUT identity-update' => 'identity-update',
                'PUT change-password' => 'change-password',
                'GET get-password-reset-code' => 'get-password-reset-code',
                'PUT reset-password' => 'reset-password',
                'GET contact' => 'contact',
                'PUT mail-confirm' => 'mail-confirm',
                'PUT send-mail-confirm-code' => 'send-mail-confirm-code',
                'GET uploading' => 'uploading',
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
            'extraPatterns' => [
                'GET geo' => 'geo',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['category' => 'category'],
            'extraPatterns' => [
                'GET account-list' => 'account-list',
                'GET admin-list' => 'admin-list',
                'GET object-status-list-by-type' => 'object-status-list-by-type',
                'GET confirmed-order-client-status-list-by-type' => 'confirmed-order-client-status-list-by-type',
                'GET search' => 'search',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['object' => 'object'],
            'extraPatterns' => [
                'GET all' => 'all',
                'GET user-view' => 'user-view',
                'GET user-objects' => 'user-objects',
                'GET get-model-list' => 'get-model-list',
                'GET get-price-ranges' => 'get-price-ranges',
                'GET for-moderation' => 'for-moderation',
                'GET confirmed-order-implementer-status-list' => 'confirmed-order-implementer-status-list',

            ],
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
                'GET all' => 'all',
                'GET on-moderation' => 'on-moderation',
                'PUT approve' => 'approve',
                'PUT reject' => 'reject',
                'POST create-user-mark' => 'create-user-mark'
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['notification' => 'notification'],
            'extraPatterns' => [
                'GET amount-new' => 'amount-new',
                'GET send' => 'send'
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['order' => 'order'],
            'extraPatterns' => [
                'PUT confirm' => 'confirm',
                'PUT reject' => 'reject',
                'PUT add-deleted' => 'add-deleted',
                'PUT order-contacts' => 'order-contacts',
                'PUT cancel-confirmation' => 'cancel-confirmation',
                'GET archive' => 'archive',
                'GET get-confirmed' => 'get-confirmed',
                'GET amount-by-user' => 'amount-by-user',
                'GET amount-confirmed-by-user' => 'amount-confirmed-by-user'
            ],
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
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['chat-message' => 'chat-message'],
            'extraPatterns' => [
                'GET user-messages' => 'user-messages',
                'GET user-messages-by-id' => 'user-messages-by-id',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['chat-message-file' => 'chat-message-file'],
            'extraPatterns' => [],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['contact-payment' => 'contact-payment'],
            'extraPatterns' => [
                'PUT pay' => 'pay',
                'GET get-by-user-id' => 'get-by-user-id',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['site' => 'site'],
            'extraPatterns' => [
                'OPTIONS <action:\w+>' => 'options'
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['settings-type' => 'settings-type'],
            'extraPatterns' => [],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['settings' => 'settings'],
            'extraPatterns' => [
                'GET form-data' => 'form-data',
                'PUT update-all' => 'update-all',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['page' => 'page'],
            'extraPatterns' => [],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['page-content' => 'page-content'],
            'extraPatterns' => [],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['image' => 'image'],
            'extraPatterns' => [],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['charity' => 'charity'],
            'extraPatterns' => [],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['blog-section' => 'blog-section'],
            'extraPatterns' => [],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['blog-article' => 'blog-article'],
            'extraPatterns' => [],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['blog-comment' => 'blog-comment'],
            'extraPatterns' => [],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['charity-fund' => 'charity-fund'],
            'extraPatterns' => [],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['partner-category' => 'partner-category'],
            'extraPatterns' => [],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['partner-element' => 'partner-element'],
            'extraPatterns' => [],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['faq-type' => 'faq-type'],
            'extraPatterns' => [],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['faq-section' => 'faq-section'],
            'extraPatterns' => [],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['faq-element' => 'faq-element'],
            'extraPatterns' => [],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['wallet' => 'wallet'],
            'extraPatterns' => [
                'PUT put-money' => 'put-money',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['bill' => 'bill'],
            'extraPatterns' => [],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['dictionary' => 'dictionary'],
            'extraPatterns' => [],
        ],
        '<controller:\w+>/<action:\w+>/' => '<controller>/<action>',
        'OPTIONS <controller:\w+>/<action:\w+>/' => 'site/<action>',
    ]
];