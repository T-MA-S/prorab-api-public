<?php

return [
    'user' => [
        'type' => 1,
        'description' => 'Пользователь',
        'ruleName' => 'userRole',
    ],
    'tech' => [
        'type' => 1,
        'description' => 'Специалист техподдержки',
        'ruleName' => 'userRole',
        'children' => [
            'user',
        ],
    ],
    'moderator' => [
        'type' => 1,
        'description' => 'Модератор',
        'ruleName' => 'userRole',
        'children' => [
            'tech',
        ],
    ],
    'admin' => [
        'type' => 1,
        'description' => 'Администратор',
        'ruleName' => 'userRole',
        'children' => [
            'moderator',
        ],
    ],
];
