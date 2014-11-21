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
use yii\base\Exception;
use yii\base\Model;
use yii\db\ActiveRecord;

class PasswordForm extends Model
{
    /**
     * @var Account
     */
    public $account;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $verifyPassword;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'verifyPassword'], 'required'],
            ['verifyPassword', 'compare', 'compareAttribute' => 'password'],
            ['password', 'validatePasswordHistory', 'on' => 'change']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password' => Module::t('labels', 'Password'),
            'verifyPassword' => Module::t('labels', 'Verify Password'),
        ];
    }

    /**
     * Validates that the password has not been used in the past.
     *
     * @param string $attribute attribute name.
     * @param array $params additional parameters.
     */
    public function validatePasswordHistory($attribute, $params)
    {
        if (Module::getInstance()->getDataContract()->isAccountPasswordUsed($this->account, $this->{$attribute})) {
            $this->addError($attribute, Module::t('errors', 'You have already used this password.'));
        }
    }

    /**
     * Changes the password for an account.
     *
     * @return bool whether the password was changed.
     */
    public function changePassword()
    {
        if ($this->validate()) {
            if ($this->account->changePassword($this->password)) {
                $dataContract = Module::getInstance()->getDataContract();
                $this->createHistoryEntry($this->account);
                if ((bool) $this->account->requireNewPassword) {
                    $dataContract->updateAccountAttributes($this->account, ['requireNewPassword' => 0]);
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Creates a password history entry.
     *
     * @param Account $account model instance.
     */
    public function createHistoryEntry(ActiveRecord $account)
    {
        Module::getInstance()->getDataContract()->createPasswordHistory([
            'accountId' => $account->getPrimaryKey(),
            'password' => $account->password,
        ]);
    }
}
