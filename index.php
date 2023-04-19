<?php

$request_headers        = apache_request_headers();
$http_origin            = $request_headers['Origin'];
$allowed_http_origins   = array(
    "http://reverse",
    "http://prorab-app.local",
    "https://prorab-app.local",
    "http://5.128.156.25",
    "https://prorab.local/",
    'http://formantestfront.tw1.ru',
    "http://tets.foreman-go.ru"
);
if (in_array($http_origin, $allowed_http_origins)) {
    @header("Access-Control-Allow-Origin: " . $http_origin);
} else {
    @header("Access-Control-Allow-Origin: *");
}

header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, Authorization');
header('Access-Control-Expose-Headers: *');

if (is_file(__DIR__ . '/ENV_DEV')) {
    // comment out the following two lines when deployed to production
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    defined('YII_ENV') or define('YII_ENV', 'dev');
}

require __DIR__ . '/api/vendor/autoload.php';
require __DIR__ . '/api/vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/api/config/web.php';

(new yii\web\Application($config))->run();
