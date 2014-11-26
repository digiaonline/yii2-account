<?php
$config = require(dirname(dirname(__DIR__)) . '/app/config/acceptance.php');

new yii\web\Application($config);

\Codeception\Util\Autoload::registerSuffix('Steps', __DIR__.DIRECTORY_SEPARATOR.'_steps');
