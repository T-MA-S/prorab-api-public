<?php

$rule = fn ($controller, $pattern) => [
    'class' => 'yii\rest\UrlRule',
    'controller' => [$controller => $controller],
    'extraPatterns' => $pattern,
];

return [
    'enablePrettyUrl' => true,
    //            'enableStrictParsing' => true,
    'showScriptName' => false,
    'rules' => [
        $rule('user', [
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
        ]),
        $rule('account', []),
        $rule('country', []),
        $rule('region', []),
        $rule('city', ['GET geo' => 'geo']),

        $rule('object', [
            'GET all' => 'all',
            'GET user-view' => 'user-view',
            'GET user-objects' => 'user-objects',
            'GET get-model-list' => 'get-model-list',
            'GET get-price-ranges' => 'get-price-ranges',
            'GET for-moderation' => 'for-moderation',
        ]),
        $rule('category', [
            'GET account-list' => 'account-list',
            'GET admin-list' => 'admin-list',
            'GET search' => 'search',
        ]),
        $rule('additional-category', []),

        $rule('admin-action', []),
        $rule('admin-action-access', []),

        $rule('booking', []),
        $rule('tariff-plan', []),
        $rule('buying-tariff', []),

        $rule('complaint', []),
        $rule('favourites', []),
        $rule('mark', ['GET get-mark' => 'get-mark']),
        $rule('notification', ['GET amount-new' => 'amount-new']),
        $rule('order', [
            'PUT confirm' => 'confirm',
            'PUT reject' => 'reject',
            'PUT order-contacts' => 'order-contacts',
            'PUT cancel-confirmation' => 'cancel-confirmation',
            'GET get-confirmed' => 'get-confirmed',
        ]),

        $rule('promocode', []),
        $rule('promocode-activation', []),

        $rule('schedule-is-busy', []),
        $rule('file', ['POST upload' => 'upload']),

        $rule('chat-message', [
            'GET user-messages' => 'user-messages',
            'GET user-messages-by-id' => 'user-messages-by-id',
        ]),
        $rule('chat-message-file', ['POST upload' => 'upload']),

        $rule('contact-payment', [
            'PUT pay' => 'pay',
            'GET get-by-user-id' => 'get-by-user-id',
        ]),

        $rule('site', ['OPTIONS <action:\w+>' => 'options']),
        $rule('settings-type', []),
        $rule('settings', [
            'GET form-data' => 'form-data',
            'PUT update-all' => 'update-all',
        ]),

        $rule('page', []),
        $rule('page-content', []),

        $rule('image', []),
        $rule('charity', []),

        $rule('blog-section', []),
        $rule('blog-article', []),
        $rule('blog-comment', []),

        $rule('charity-fund', []),

        $rule('faq-type', []),
        $rule('faq-section', []),
        $rule('faq-element', []),
        $rule('review', [
            'GET reviews-by-id' => 'reviews-by-id',
            'POST send-review' => 'send-review'
        ]),
        '<controller:\w+>/<action:\w+>/' => '<controller>/<action>',
        'OPTIONS <controller:\w+>/<action:\w+>/' => 'site/<action>',
    ],
];