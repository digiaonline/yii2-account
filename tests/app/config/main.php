<?php

/**
* Shared application configuration.
*/
return [
  'id' => 'account-test',
  'basePath' => dirname(__DIR__),
  'vendorPath' => YII_APP_BASE_PATH . '/vendor',
  'bootstrap' => [
    'nord\yii\account\Bootstrap',
  ],
  'components' => [
    'db' => [
      'class' => 'yii\db\Connection',
      'dsn' => 'mysql:host=localhost;dbname=account_test',
      'username' => 'root',
      'password' => 'root',
    ],
    /*
    'mailer' => [
      'useFileTransport' => true,
    ],
    */
  ],
  'modules' => [
    'account' => [
      'class' => 'nord\yii\account\Module',
      'components' => [
        'mailSender' => 'nord\yii\account\components\mailsender\DummyMailSender',
      ],
      'enableCaptcha' => false,
      /*
      'urlConfig' => [
        'prefix' => 'foobar',
        'routePrefix' => 'account',
        'rules' => [
          'looogin' => 'auth/login',
        ],
      ]
      */
    ],
  ],
];
