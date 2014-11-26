<?php
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

defined('YII_APP_BASE_PATH') or define('YII_APP_BASE_PATH', dirname(dirname(dirname(__DIR__))));

require(YII_APP_BASE_PATH . '/vendor/autoload.php');
require(YII_APP_BASE_PATH . '/vendor/yiisoft/yii2/Yii.php');

$config = require(dirname(__DIR__) . '/config/acceptance.php');

(new yii\web\Application($config))->run();
