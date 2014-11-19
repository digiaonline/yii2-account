<?php
/*
 * This file is part of Account.
 *
 * (c) 2014 Nord Software
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nord\yii\account\models;

use nord\yii\account\Module;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Expression;

class LoginForm extends Model
{
    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var boolean
     */
    public $rememberMe = true;

    /**
     * @var Account
     */
    private $_account;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Module::t('labels', 'Username'),
            'password' => Module::t('labels', 'Password'),
            'rememberMe' => Module::t('labels', 'Remember me'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $dataContract = Module::getInstance()->getDataContract();
            $account = $this->getAccount();

            if (!$account || !$account->validatePassword($this->password)) {
                $this->addError($attribute, Module::t('errors', 'Incorrect username or password.'));
            }

            if (!$dataContract->isAccountActivated($account)) {
                $this->addError($attribute, Module::t('errors', 'Your account has not yet been activated.'));
            }

            if ($dataContract->isAccountLocked($account)) {
                $this->addError($attribute,
                    Module::t('errors', 'Your account has been locked due to too many failed login attempts.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $account = $this->getAccount();
            $duration = $this->rememberMe ? Module::getParam(Module::PARAM_LOGIN_EXPIRE_TIME) : 0;
            $success = Yii::$app->user->login($account, $duration);
            $this->createHistoryEntry($account, $success);
            $dataContract = Module::getInstance()->getDataContract();
            $dataContract->updateAccountAttributes($account, ['lastLoginAt' => new Expression('NOW()')]);
            if ($dataContract->isAccountPasswordExpired($account)) {
                $dataContract->updateAccountAttributes($account, ['requireNewPassword' => 1]);
            }
            return $success;
        } else {
            return false;
        }
    }

    /**
     * Creates a login history entry.
     *
     * @param ActiveRecord $account account instance.
     * @param bool $success whether login was successful.
     */
    protected function createHistoryEntry(ActiveRecord $account, $success)
    {
        $dataContract = Module::getInstance()->getDataContract();
        $dataContract->createLoginHistory([
            'accountId' => $account->getPrimaryKey(),
            'success' => $success,
            'numFailedAttempts' => $success ? 0 : $dataContract->getAccountNumFailedLoginAttempts($account),
        ]);
    }

    /**
     * Finds an account by [[username]]
     *
     * @return Account
     */
    public function getAccount()
    {
        if ($this->_account === null) {
            $this->_account = Module::getInstance()->getDataContract()->findAccount(['username' => $this->username]);
        }
        return $this->_account;
    }
}