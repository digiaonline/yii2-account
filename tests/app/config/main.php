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
      'dsn' => 'mysql:host=' . DB_HOST . ';dbname=account_test',
      'username' => DB_USERNAME,
      'password' => DB_PASSWORD,
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
        'mailSender' => [
          'class' => 'nord\yii\account\components\mailsender\DummyMailSender',
        ],
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
