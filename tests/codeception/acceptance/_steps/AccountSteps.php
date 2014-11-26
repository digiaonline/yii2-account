<?php
namespace AcceptanceTester;

class AccountSteps extends \AcceptanceTester
{
    public function signup(array $attributes)
    {
        $I = $this;
        $I->fillField(\SignupPage::$fieldEmail, $attributes['email']);
        $I->fillField(\SignupPage::$fieldUsername, $attributes['username']);
        $I->fillField(\SignupPage::$fieldPassword, $attributes['password']);
        $I->fillField(\SignupPage::$fieldVerifyPassword, $attributes['verifyPassword']);
        $I->click(\SignupPage::$buttonSubmit);
    }

    public function login($username, $password)
    {
        $I = $this;
        $I->fillField(\LoginPage::$fieldUsername, $username);
        $I->fillField(\LoginPage::$fieldPassword, $password);
        $I->click(\LoginPage::$buttonSubmit);
    }

    public function logout()
    {
        $I = $this;
        $I->amOnPage(\LogoutPage::$URL);
    }

    public function forgotPassword($email)
    {
        $I = $this;
        $I->fillField(\ForgotPasswordPage::$fieldEmail, $email);
        $I->click(\ForgotPasswordPage::$buttonSubmit);
    }

    public function resetPassword($newPassword)
    {
        $I = $this;
        $I->fillField(\ResetPasswordPage::$fieldPassword, $newPassword);
        $I->fillField(\ResetPasswordPage::$fieldVerifyPassword, $newPassword);
        $I->click(\ResetPasswordPage::$buttonSubmit);
    }
}
