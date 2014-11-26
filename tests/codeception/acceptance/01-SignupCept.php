<?php
$I = new \AcceptanceTester\AccountSteps($scenario);
$I->wantTo('sign-up and see the result');
$I->amOnPage(\LoginPage::$URL);
$I->click('#signup-link');
$I->signup([
  'email' => 'demo@example.com',
  'username' => 'demo',
  'password' => 'demo1234',
  'verifyPassword' => 'demo1234',
]);
$I->see('Thank you for signing up');
$I->click('a');
$I->see('Login', 'h1');
