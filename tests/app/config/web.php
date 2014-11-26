<?php

/**
* Web application configuration.
*/
return [
  'components' => [
    'request' => [
      // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
      'cookieValidationKey' => 'wIzNOYwMCKp3HVkCeMDwJlJO76xsdVfs',
    ],
    'urlManager' => [
      'showScriptName' => true,
      'rules' => [
        '<controller:\w+>/<id:\d+>' => '<controller>/view',
        '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
        '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
      ],
    ],
  ],
];
