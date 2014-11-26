<?php
$config = require(dirname(dirname(__DIR__)) . '/app/config/functional.php');

new yii\web\Application($config);
