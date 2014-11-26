<?php

namespace nord\yii\account\models;

use nord\yii\account\behaviors\JsonAttributeBehavior;
use nord\yii\account\Module;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "account_provider".
 *
 * @property integer $id
 * @property integer $accountId
 * @property string $name
 * @property string $clientId
 * @property string $data
 *
 * @property Account $account
 */
class AccountProvider extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account_provider';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'json' => [
                'class' => JsonAttributeBehavior::className(),
                'attributes' => ['data'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['accountId', 'name', 'clientId'], 'required'],
            [['accountId'], 'integer'],
            [['data'], 'string'],
            [['name', 'clientId'], 'string', 'max' => 255],
            [['accountId', 'name', 'clientId'], 'unique', 'targetAttribute' => ['accountId', 'name', 'clientId']]
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
            'name' => Module::t('labels', 'Name'),
            'clientId' => Module::t('labels', 'Client ID'),
            'data' => Module::t('labels', 'Data'),
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
