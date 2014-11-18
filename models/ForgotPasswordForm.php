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
use yii\base\Model;

class ForgotPasswordForm extends Model
{
    /**
     * @var string
     */
    public $email;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'validateAccountExists'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Module::t('labels', 'Email'),
        ];
    }

    /**
     * Validates that the account exists.
     *
     * @param string $attribute attribute name.
     * @param array $params additional parameters.
     */
    public function validateAccountExists($attribute, $params)
    {
        $account = Module::getInstance()->getDataContract()->findAccount(['email' => $this->email]);
        if ($account === null) {
            $this->addError($attribute, Module::t('errors', 'There is no account is associated with this e-mail address.'));
        }
    }
}