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

class SignupForm extends PasswordForm
{
    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    //public $captcha;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['email', 'username'], 'required'],
                ['username', 'string', 'min' => 4],
                ['email', 'email'],
                [['username', 'email'], 'unique', 'targetClass' => Account::className()],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'email' => Module::t('labels', 'Email'),
                'username' => Module::t('labels', 'Username'),
            ]
        );
    }

    /**
     * TODO Write this
     *
     * @return bool
     */
    public function signup()
    {
        if ($this->validate()) {
            $dataContract = Module::getInstance()->getDataContract();
            $account = $dataContract->createAccount($this->attributes);

            if ($account->validate()) {
                if ($account->save(false/* already validated */)) {
                    $dataContract->createPasswordHistory([
                        'accountId' => $account->id,
                        'salt' => $account->salt,
                        'password' => $account->password,
                    ]);

                    return true;
                }
            }
            foreach ($account->getErrors('password') as $error) {
                $this->addError('password', $error);
            }
        }
        return false;
    }
}