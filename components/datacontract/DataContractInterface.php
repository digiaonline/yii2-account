<?php
/*
 * This file is part of Account.
 *
 * (c) 2014 Nord Software
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nord\yii\account\components\datacontract;

use yii\db\ActiveRecord;

interface DataContractInterface
{
    /**
     * Creates a new account model.
     *
     * @param array $config object configuration.
     * @return ActiveRecord model instance.
     */
    public function createAccount(array $config = []);

    /**
     * Returns an account using the given search condition.
     *
     * @param mixed $condition search condition.
     * @return ActiveRecord model instance.
     */
    public function findAccount($condition);

    /**
     * Updates the attributes for the given account.
     * 
     * @param ActiveRecord $model model instance.
     * @param array $config object configuration.
     */
    public function updateAccountAttributes(ActiveRecord $model, array $config);

    /**
     * Changes the status of the given account model to activated.
     *
     * @param ActiveRecord $model model instance.
     */
    public function activateAccount(ActiveRecord $model);

    /**
     * Returns whether the given account is activated.
     *
     * @param ActiveRecord $model model instance.
     * @return bool whether the account is activated.
     */
    public function isAccountActivated(ActiveRecord $model);

    /**
     * Returns whether the given account is locked.
     *
     * @param ActiveRecord $model model instance.
     * @return bool whether the account is locked.
     */
    public function isAccountLocked(ActiveRecord $model);

    /**
     * Returns whether the password for the given account has expired.
     *
     * @param ActiveRecord $model model instance.
     * @return bool whether the password has expired.
     */
    public function isAccountPasswordExpired(ActiveRecord $model);

    /**
     * Returns whether a password has been used for the given account.
     *
     * @param ActiveRecord $model model instance.
     * @param string $password password hash.
     * @return bool whether the password has been used already.
     */
    public function isAccountPasswordUsed(ActiveRecord $model, $password);

    /**
     * Returns the number of failed login attempts for the given account.
     *
     * @param ActiveRecord $model model instance.
     * @return int number of failed login attempts.
     */
    public function getAccountNumFailedLoginAttempts(ActiveRecord $model);

    /**
     * Returns the last ten models from the password history.
     *
     * @param ActiveRecord $model model instance.
     * @return ActiveRecord[] array of password history models.
     */
    public function getAccountPasswordHistory(ActiveRecord $model);

    /**
     * Creates a provider model.
     *
     * @param array $config object configuration.
     * @return ActiveRecord model instance.
     */
    public function createProvider(array $config = []);

    /**
     * Returns a provider using the given search condition.
     *
     * @param mixed $condition search condition.
     * @return ActiveRecord model instance.
     */
    public function findProvider($condition);

    /**
     * Creates a login history model.
     *
     * @param array $config object configuration.
     * @return ActiveRecord model instance.
     */
    public function createLoginHistory(array $config = []);

    /**
     * Creates a password history model.
     *
     * @param array $config object configuration.
     * @return ActiveRecord model instance.
     */
    public function createPasswordHistory(array $config = []);

    /**
     * Generates a new random token and saves it in the database.
     *
     * @param array $config object configuration.
     * @return string the generated token.
     */
    public function createToken(array $config = []);

    /**
     * Loads a token of a specific type.
     *
     * @param string $type token type.
     * @param string $token token string.
     * @return ActiveRecord token model.
     */
    public function findToken($type, $token);

    /**
     * Changes the status of the given token model as used.
     *
     * @param ActiveRecord $model model instance.
     */
    public function useToken(ActiveRecord $model);

    /**
     * Creates a new login form model.
     *
     * @param array $config object configuration.
     * @return ActiveRecord form model instance.
     */
    public function createLoginForm(array $config = []);

    /**
     * Creates a new signup form model.
     *
     * @param array $config object configuration.
     * @return ActiveRecord form model instance.
     */
    public function createSignupForm(array $config = []);

    /**
     * Creates a new connect form model.
     *
     * @param array $config object configuration.
     * @return ActiveRecord form model instance.
     */
    public function createConnectForm(array $config = []);

    /**
     * Creates a new forgot password form model.
     *
     * @param array $config object configuration.
     * @return ActiveRecord form model instance.
     */
    public function createForgotPasswordForm(array $config = []);

    /**
     * Creates a new password form model.
     *
     * @param array $config object configuration.
     * @return ActiveRecord form model instance.
     */
    public function createPasswordForm(array $config = []);
}