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
     * @return int exit code.
     * @throws Exception
     */
    public function actionCreate($username, $password)
    {
        $dataContract = $this->module->getDataContract();
        $account = $dataContract->createAccount(
            [
                'attributes' => [
                    'username' => $username,
                    'password' => $password,
                    'email' => $username . '@example.com',
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
}
