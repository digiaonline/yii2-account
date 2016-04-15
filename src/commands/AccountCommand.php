<?php

namespace nord\yii\account\commands;

use nord\yii\account\components\datacontract\DataContract;
use nord\yii\account\Module;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\Json;

/**
 * Allows you to create user accounts.
 *
 * @property Module $module
 */
class AccountCommand extends Controller
{
    /**
     * @var string default action.
     */
    public $defaultAction = 'create';

    /**
     * Creates a new account with the given username and password.
     *
     * @param string $username desired username.
     * @param string $password desired password.
     * @return integer exit code.
     * @throws Exception
     */
    public function actionCreate($username, $password)
    {
        $dataContract = $this->module->getDataContract();
        
        $account = $dataContract->createAccount(
            [
                'attributes' => [
                    $this->module->usernameAttribute => $username,
                    $this->module->passwordAttribute => $password,
                    $this->module->emailAttribute => $username . '@example.com',
                    'status' => $dataContract->getStatusCode(Module::CLASS_ACCOUNT,
                        Module::STATUS_ACTIVATED)
                ]
            ]);

        if (!$account->save()) {
            throw new Exception("Failed to create account with errors: \n" . Json::encode($account->getErrors()));
        }

        echo "Account $username:$password created.\n";

        return 0;
    }


    /**
     * Sends a test activation e-mail to the specified e-mail address
     *
     * @param string $email
     */
    public function actionTestActivationEmail($email)
    {
        $actionUrl = 'http://example.com/activate/' . $email;

        $this->module->getMailSender()->sendActivationMail([
            'to'      => [$email],
            'from'    => $this->module->getParam(Module::PARAM_FROM_EMAIL_ADDRESS),
            'subject' => Module::t('email', 'Thank you for signing up'),
            'data'    => ['actionUrl' => $actionUrl],
        ]);
    }


    /**
     * Sends a test "recover pasword" e-mail to the specified e-mail address
     *
     * @param string $email
     */
    public function actionTestResendPasswordEmail($email)
    {
        $actionUrl = 'http://example.com/recoverPassword/' . $email;

        $this->module->getMailSender()->sendResetPasswordMail([
            'to'      => [$email],
            'from'    => $this->module->getParam(Module::PARAM_FROM_EMAIL_ADDRESS),
            'subject' => Module::t('email', 'Thank you for signing up'),
            'data'    => ['actionUrl' => $actionUrl],
        ]);
    }
    
}
