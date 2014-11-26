yii2-account
============

[![Build Status](https://travis-ci.org/nordsoftware/yii2-account.svg?branch=test-suite)](https://travis-ci.org/nordsoftware/yii2-account)
[![Latest Stable Version](https://poser.pugx.org/nordsoftware/yii2-account/v/stable.svg)](https://packagist.org/packages/nordsoftware/yii2-account) [![Total Downloads](https://poser.pugx.org/nordsoftware/yii2-account/downloads.svg)](https://packagist.org/packages/nordsoftware/yii2-account) [![Latest Unstable Version](https://poser.pugx.org/nordsoftware/yii2-account/v/unstable.svg)](https://packagist.org/packages/nordsoftware/yii2-account) [![License](https://poser.pugx.org/nordsoftware/yii2-account/license.svg)](https://packagist.org/packages/nordsoftware/yii2-account)
[![Code Climate](https://codeclimate.com/github/nordsoftware/yii2-account/badges/gpa.svg)](https://codeclimate.com/github/nordsoftware/yii2-account)
[![Test Coverage](https://codeclimate.com/github/nordsoftware/yii2-account/badges/coverage.svg)](https://codeclimate.com/github/nordsoftware/yii2-account)

Account module for the Yii PHP framework.

## Why do I want this

This project was inspired by both the [http://github.com/mishamx/yii-user](yii-user)
and [https://github.com/dektrium/yii2-user](yii2-user) modules and was carefully developed with our expertise in Yii
following the best practices of the framework. The module uses Yii's own password methods in ```yii\base\Security```,
alternatively you can implement our ```PasswordHasherInterface``` to hash passwords differently. Other features include
interfaces for sending mail and creating authentication tokens; various password security features and much more.
For more details refer to the features section below.

## Features

 - Secure accounts (bcrypt encryption) __DONE__
 - Optional sign-up process (enabled by default) __DONE__
 - Captcha support on sign up __DONE__
 - Optional account activation (enabled by default) __DONE__
 - Log in / Log out __DONE__
 - Sign up and log in through third-party services __DONE__
 - Reset password __DONE__
 - Email sending (with token validation) __DONE__
 - Require new password every x days (disabled by default) __DONE__
 - Password history (encrypted) to prevent from using same password twice __DONE__
 - Lock accounts after x failed login attempts (disabled by default) __DONE__
 - Console command for creating accounts __DONE__
 - Proper README __WIP__

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist nordsoftware/yii2-account "*"
```

or add

```
"nordsoftware/yii2-account": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply modify your application configuration as follows:

```php
return [
    'bootstrap' => [
        'nord\yii\account\Bootstrap'
        // ...
    ],
    'modules' => [
        'account' => 'nord\yii\account\Module',
        // ...
    ],
    // ...
];
```

### Configuration

The following configurations are available for the ```nord\yii\account\Module``` class:

 * __classMap__ _array_ map over classes to use within the module.
 * __enableActivation__ _bool_ whether to enable account activation (defaults to ```true```).
 * __enableSignup__ _bool_ whether to enable the sign-up process (defaults to ```true```).
 * __enableCaptcha__ _bool_ whether to enable CAPTCHA when signing up (defaults to ```false```).
 * __enableClientAuth__ _bool_ whether to enable client authentication (defaults to ```false```).
 * __userConfig__ _array_ configuration passed to ```yii\web\User```.
 * __passwordConfig__ _array_ configuration passed to ```PasswordStrengthValidator```.
 * __captchaConfig__ _array_ configuration passed to ```CaptchaAction```.
 * __clientAuthConfig__ _array_ configuration passed to ```yii\authclient\Collection```.
 * __urlConfig__ _array_ configuration for the URLs used by the module.
 * __loginAttribute__ _string_ name of the attribute to use for logging in (defaults to ```username```).
 * __passwordAttribute__ _string_ name of the password attribute (defaults to ```password```).
 * __messageSource__ _string_ message source component to use for the module.
 * __messagePath__ _string_ message path to use for the module.

### Parameters

The following parameters are available for the ```nord\yii\account\Module``` class:

 * __fromEmailAddress__ _string_ from e-mail address used when sending e-mail.
 * __numAllowedFailedLoginAttempts__ _int_ number of failed login attempts before the account is locked (defaults to 10)
 * __minUsernameLength__ _int_ minimum length for usernames (defaults to 4).
 * __minPasswordLength__ _int_ minimum length for passwords (defaults to 6).
 * __loginExpireTime__ _int_ number of seconds for login cookie to expire (defaults to 30 days).
 * __activateExpireTime__ _int_ number of seconds for account activation to expire (defaults to 30 days).
 * __resetPasswordExpireTime__ _int_ number of seconds for password reset to expire (defaults to 1 day).
 * __passwordExpireTime__ _int_ number of seconds for passwords to expire (defaults to disabled).
 * __lockoutExpireTime__ _int_ number of seconds for account lockout to expire (defaults to 10 minutes).
 * __tokenExpireTime__ _int_ number of seconds for the authorization tokens to expire (defaults to 1 hour).


## Usage

Now you should be able to see the login page when you go to the following url:

```
index.php?r=account OR index.php/account
```

You can run the following command to generate an account from the command line:

```bash
php yii account/create demo demo1234
```

## Extending

This project was developed with a focus on re-usability, so before you start copy-pasting take a moment of your time
and read through this section to learn how to extend this module properly.

### Custom account model

You can use your own account model as long as you add the following fields to it:

 * __username__ _varchar(255) not null_ account username
 * __password__ _varchar(255) not null_ account password
 * __authKey__ _varchar(255) not null_ authentication key used for cookie authentication
 * __email__ _varchar(255) not null_ account email
 * __lastLoginAt__ _datetime null default null_ when the user last logged in
 * __createdAt__ _datetime null default null_ when the account was created
 * __status__ _int(11) default '0'_ account status (e.g. unactivated, activated)

Changing the model used by the extension is easy, simply configure it to use your class instead by adding it to the
class map for the module:

```php
'account' => [
    'class' => 'nord\yii\account\Module',
    'classMap' => [
        'account' => 'MyAccount',
    ],
],
```

### Custom models

You can use the class map to configure any classes used by the module, here is a complete list of the available classes:

 * __account__ _models\Account_ account model
 * __token__ _models\AccountToken_ account token model
 * __provider__ _models\AccountProvider_ account provider model
 * __loginHistory__ _models\AccountLoginHistory_ login history model
 * __passwordHistory__ _models\AccountPasswordHistory_ password history model
 * __loginForm__ _models\LoginForm_ login form
 * __passwordForm__ _models\PasswordForm_ base form that handles passwords
 * __signupForm__ _models\SignupForm_ signup form
 * __connectForm__ _models\ConnectForm_ connect form
 * __forgotPassword__ _models\ForgotPasswordForm_ forgot password form
 * __passwordBehavior__ _behaviors/PasswordAttributeBehavior_ password attribute behavior
 * __passwordValidator__ _validators/PasswordStrengthValidator_ password strength validator
 * __webUser__ _yii\web\User_ web user component
 * __captcha__ _yii\captcha\Captcha_ captcha widget
 * __captchaAction__ _yii\captcha\CaptchaAction_ captcha action

### Custom controllers

If you want to use your own controllers you can map them using the module's controller map:

```php
'account' => [
    'class' => 'nord\yii\account\Module',
    'controllerMap' => [
        'authenticate' => 'MyAuthenticateController',
    ],
],
```

## Custom components

If you want to change the components used by the module, here is a complete list of the available interfaces:

 * __dataContract__ _components\datacontract\DataContractInterface_ abstraction layer between the module and its data model (defaults to ```DataContract```)
 * __mailSender__ _components\mailsender\MailSenderInterface_ component used for sending e-mail (defaults to ```YiiMailSender```)
 * __passwordHasher__ _components\passwordhasher\PasswordHasherInterface_ component used for hashing password (defaults to ```YiiPasswordHasher```)
 * __tokenGenerator__ _components\tokengenerator\TokenGeneratorInterface_ component used for generating random tokens (default to ```YiiTokenGenerator```)

You might want to look at the bundled implementations before making your own because we already support e.g. sending mail through Mandrill.

## Contribute

If you wish to contribute to this project feel free to create a pull-request to the ```develop``` branch.

### Running tests

Coming soon ...

### Translate

If you wish to translate this project you can find the translation templates under ```messages/templates```.
When you are done with your translation you should create a pull-request to the ```develop``` branch.
