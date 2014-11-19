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
use phpnode\yii\password\PasswordBehavior;
use phpnode\yii\password\strategies\BcryptStrategy;
use phpnode\yii\password\strategies\PasswordStrategy;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "account".
 *
 * @property integer $id
 * @property string $salt
 * @property string $username
 * @property string $passwordHash
 * @property string $authKey
 * @property string $email
 * @property string $passwordStrategy
 * @property integer $requireNewPassword
 * @property string $createdAt
 * @property string $lastLoginAt
 * @property integer $status
 *
 * @property string $password write-only password
 *
 * @method bool validatePassword($password)
 * @method bool changePassword($password, $runValidation = true)
 * @method PasswordStrategy getStrategy()
 */
class Account extends ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'email'], 'required'],
            [['requireNewPassword', 'status'], 'integer', 'integerOnly' => true],
            [['salt', 'username', 'password', 'authKey', 'email', 'passwordStrategy'], 'string', 'max' => 255],
            [['username', 'email'], 'unique'],
            [['createdAt', 'lastLoginAt'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('labels', 'ID'),
            'salt' => Module::t('labels', 'Salt'),
            'username' => Module::t('labels', 'Username'),
            'password' => Module::t('labels', 'Password'),
            'authKey' => Module::t('labels', 'Authentication Key'),
            'email' => Module::t('labels', 'Email'),
            'passwordStrategy' => Module::t('labels', 'Password Strategy'),
            'requireNewPassword' => Module::t('labels', 'Require New Password'),
            'createdAt' => Module::t('labels', 'Created At'),
            'updatedAt' => Module::t('labels', 'Updated At'),
            'lastLoginAt' => Module::t('labels', 'Last Login At'),
            'status' => Module::t('labels', 'Status'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => PasswordBehavior::className(),
                'defaultStrategy' => 'bcrypt',
                'strategies' => [
                    'bcrypt' => [
                        'class' => BcryptStrategy::className(),
                    ],
                ],
            ],
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
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->authKey = Module::getInstance()->getTokenGenerator()->generate();
            }
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->authKey = Module::getInstance()->getTokenGenerator()->generate();
    }
}
