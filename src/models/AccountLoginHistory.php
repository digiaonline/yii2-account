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
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "account_login_history".
 *
 * @property integer $id
 * @property integer $accountId
 * @property integer $success
 * @property integer $numFailedAttempts
 * @property string $createdAt
 *
 * @property Account $account
 */
class AccountLoginHistory extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%account_login_history}}';
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
            [['accountId', 'success', 'numFailedAttempts'], 'integer'],
            [['createdAt'], 'safe']
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
            'success' => Module::t('labels', 'Success'),
            'numFailedAttempts' => Module::t('labels', '# Failed Attempts'),
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
