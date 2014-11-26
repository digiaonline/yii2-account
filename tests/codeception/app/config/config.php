<?php

/**
* Application configuration shared by all test types
*/
return [
  'id' => 'account-tests',
  'basePath' => dirname(__DIR__),
  'components' => [
    'db' => [
      'class' => 'yii\db\Connection',
      'dsn' => 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
      'username' => DB_USER,
      'password' => DB_PASS,
    ],
    /*
    'mailer' => [
      'useFileTransport' => true,
    ],
    */
    'urlManager' => [
      'showScriptName' => true,
    ],
  ],
];
