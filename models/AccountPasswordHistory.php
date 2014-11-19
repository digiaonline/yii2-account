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
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "account_password_history".
 *
 * @property integer $id
 * @property integer $accountId
 * @property string $salt
 * @property string $password
 * @property string $createdAt
 *
 * @property Account $account
 */
class AccountPasswordHistory extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account_password_history';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'createdAt',
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['accountId', 'salt', 'password'], 'required'],
            [['accountId'], 'integer'],
            [['createdAt'], 'safe'],
            [['salt', 'password'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('labels', 'ID'),
            'accountId' => Module::t('labels', 'Account ID'),
            'salt' => Module::t('labels', 'Salt'),
            'password' => Module::t('labels', 'Password'),
            'createdAt' => Module::t('labels', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'accountId']);
    }
}
