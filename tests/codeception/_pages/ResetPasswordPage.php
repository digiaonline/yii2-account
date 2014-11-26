<?php

class ResetPasswordPage
{
    // include url of current page
    public static $URL = '?r=account/password/reset';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

    public static $fieldPassword = '#passwordform-password';
    public static $fieldVerifyPassword = '#passwordform-verifypassword';

    public static $buttonSubmit = '#passwordform button[type=submit]';

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
