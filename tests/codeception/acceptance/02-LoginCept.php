<?php
$I = new AcceptanceTester\AccountSteps($scenario);
$I->wantTo('log in using an existing account and see the result');
$I->amOnPage(\LoginPage::$URL);
$I->login('demo', 'demo1234');
$I->dontSee('Incorrect username or password.');
