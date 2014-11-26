<?php

class SignupPage
{
    // include url of current page
    public static $URL = '?r=account/signup/index';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

     static $fieldEmail = '#signupform-email';
     static $fieldUsername = '#signupform-username';
     static $fieldPassword = '#signupform-password';
     static $fieldVerifyPassword = '#signupform-verifypassword';
     static $buttonSubmit = '#signupform button[type=submit]';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: EditPage::route('/123-post');
     */
     public static function route($param)
     {
        return static::$URL.$param;
     }


}
