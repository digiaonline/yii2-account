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

class ConnectForm extends Model
{
    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'username'], 'required'],
            ['username', 'string', 'min' => Module::getParam(Module::PARAM_MIN_USERNAME_LENGTH)],
            ['email', 'email'],
            [['username', 'email'], 'unique', 'targetClass' => Account::className()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Module::t('labels', 'Email'),
            'username' => Module::t('labels', 'Username'),
        ];
    }
}
