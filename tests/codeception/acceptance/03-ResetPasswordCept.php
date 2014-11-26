<?php
$I = new AcceptanceTester\AccountSteps($scenario);
$I->wantTo('reset my password, log in and see the result');

$I->amOnPage(\LoginPage::$URL);
$I->click('#forgotpassword-link');
$I->forgotPassword('demo@example.com');
$I->see('You have requested to reset your password');
$I->click('a');
$I->see('Reset password', 'h1');

$I->resetPassword('demo4321');
$I->see('Login', 'h1');
$I->login('demo', 'demo4321');
$I->dontSee('Incorrect username or password.');
