<?php
require(__DIR__ . '/env.php');

/**
* Application configuration for acceptance tests
*/
return yii\helpers\ArrayHelper::merge(
  require(__DIR__ . '/config.php'),
  [
  ]
);
