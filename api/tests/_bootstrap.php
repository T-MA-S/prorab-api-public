<?php

use Codeception\Util\Fixtures;

define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);
define("FIXTURES_DIR", __DIR__ . '/_fixtures/');

require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
require __DIR__ .'/../vendor/autoload.php';

//Fixtures::add('users', require(FIXTURES_DIR . 'users.php'));
//Fixtures::add('accounts', require(FIXTURES_DIR . 'accounts.php'));