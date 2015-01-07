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
 * This is the model class for table "account_token".
 *
 * @property integer $id
 * @property integer $accountId
 * @property string $type
 * @property string $token
 * @property string $createdAt
 * @property integer $status
 *
 * @property Account $account
 */
class AccountToken extends ActiveRecord
{
    const STATUS_UNUSED = 0;
    const STATUS_USED = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%account_token}}';
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
            [['accountId', 'type', 'token'], 'required'],
            [['accountId', 'status'], 'integer'],
            [['createdAt'], 'safe'],
            [['type', 'token'], 'string', 'max' => 255],
            [['accountId', 'type', 'token'], 'unique', 'targetAttribute' => ['accountId', 'type', 'token']]
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
            'type' => Module::t('labels', 'Type'),
            'token' => Module::t('labels', 'Token'),
            'createdAt' => Module::t('labels', 'Created At'),
            'status' => Module::t('labels', 'Status'),
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
