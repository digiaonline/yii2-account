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

use nord\yii\account\models\Account;
use nord\yii\account\models\AccountLoginHistory;
use nord\yii\account\models\AccountPasswordHistory;
use nord\yii\account\models\AccountProvider;
use nord\yii\account\models\AccountToken;
use nord\yii\account\Module;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidParamException;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\Json;

class DataContract extends Component implements DataContractInterface
{
    /**
     * @var array map over model statuses to use by this contract.
     */
    public $statusMap = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->initStatusMap();
    }

    /**
     * @inheritdoc
     * @return Account
     */
    public function createAccount(array $config = [])
    {
        return $this->createInternal(Module::CLASS_ACCOUNT, $config, false);
    }

    /**
     * @inheritdoc
     * @return Account model instance.
     */
    public function findAccount($condition)
    {
        return $this->findInternal(Module::CLASS_ACCOUNT, $condition);
    }

    /**
     * @inheritdoc
     */
    public function updateAccountAttributes(ActiveRecord $model, array $config)
    {
        $this->updateAttributesInternal($model, $config);
    }

    /**
     * @inheritdoc
     */
    public function activateAccount(ActiveRecord $model)
    {
        $this->transitionInternal($model, $this->getStatusCode(Module::CLASS_ACCOUNT, Module::STATUS_ACTIVATED));
    }

    /**
     * @inheritdoc
     */
    public function isAccountActivated(ActiveRecord $model)
    {
        if (!Module::getInstance()->enableActivation) {
            return true;
        }
        return $model->status === $this->getStatusCode(Module::CLASS_ACCOUNT, Module::STATUS_ACTIVATED);
    }

    /**
     * @inheritdoc
     */
    public function isAccountLocked(ActiveRecord $model)
    {
        $numAllowedAttempts = Module::getParam(Module::PARAM_NUM_ALLOWED_FAILED_LOGIN_ATTEMPTS);
        $lockoutExpireTime = Module::getParam(Module::PARAM_LOCKOUT_EXPIRE_TIME);

        if ($numAllowedAttempts === 0) {
            return false;
        }

        /** @var ActiveRecord $modelClass */
        $modelClass = Module::getInstance()->getClassName(Module::CLASS_LOGIN_HISTORY);

        /** @var AccountLoginHistory $model */
        $model = $modelClass::find()
            ->where(['accountId' => $model->getPrimaryKey()])
            ->andWhere('UNIX_TIMESTAMP() - UNIX_TIMESTAMP(createdAt) < :expireTime',
                [':expireTime' => $lockoutExpireTime])
            ->andWhere('numFailedAttempts > :numAllowedAttempts', [':numAllowedAttempts' => $numAllowedAttempts])
            ->orderBy('createdAt DESC')
            ->one();

        return $model !== null;
    }

    /**
     * @inheritdoc
     */
    public function isAccountPasswordExpired(ActiveRecord $model)
    {
        $passwordExpireTime = Module::getParam(Module::PARAM_PASSWORD_EXPIRE_TIME);

        if ($passwordExpireTime === 0) {
            return false;
        }

        /** @var ActiveRecord $modelClass */
        $modelClass = Module::getInstance()->getClassName(Module::CLASS_PASSWORD_HISTORY);

        /** @var AccountPasswordHistory $model */
        $model = $modelClass::find()
            ->where(['accountId' => $model->getPrimaryKey()])
            ->andWhere('UNIX_TIMESTAMP() - UNIX_TIMESTAMP(createdAt) > :expireTime',
                [':expireTime' => $passwordExpireTime])
            ->orderBy('createdAt DESC')
            ->one();

        return $model !== null;
    }

    /**
     * @inheritdoc
     */
    public function isAccountPasswordUsed(ActiveRecord $model, $password)
    {
        $models = $this->getAccountPasswordHistory($model);
        foreach ($models as $model) {
            if (Module::getInstance()->getPasswordHasher()->validatePassword($password, $model->password)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getAccountNumFailedLoginAttempts(ActiveRecord $model)
    {
        /** @var ActiveRecord $modelClass */
        $modelClass = Module::getInstance()->getClassName(Module::CLASS_LOGIN_HISTORY);

        /** @var AccountLoginHistory $lastEntry */
        $lastEntry = $modelClass::find()
            ->where(['accountId' => $model->getPrimaryKey])
            ->orderBy('createdAt DESC')
            ->one();

        $numFailedAttempts = $lastEntry !== null ? $lastEntry->numFailedAttempts : 0;
        return ++$numFailedAttempts;
    }

    /**
     * @inheritdoc
     * @return AccountPasswordHistory[]
     */
    public function getAccountPasswordHistory(ActiveRecord $model)
    {
        /** @var ActiveRecord $modelClass */
        $modelClass = Module::getInstance()->getClassName(Module::CLASS_PASSWORD_HISTORY);
        return $modelClass::find()
            ->where(['accountId' => $model->getPrimaryKey()])
            ->orderBy('createdAt DESC')
            ->limit(10)
            ->all();
    }

    /**
     * @inheritdoc
     * @return AccountProvider model instance.
     */
    public function createProvider(array $config = [])
    {
        return $this->createInternal(Module::CLASS_PROVIDER, $config, false);
    }

    /**
     * @inheritdoc
     * @return AccountProvider model instance.
     */
    public function findProvider($condition)
    {
        return $this->findInternal(Module::CLASS_PROVIDER, $condition);
    }

    /**
     * @inheritdoc
     * @return AccountLoginHistory model instance.
     */
    public function createLoginHistory(array $config = [])
    {
        return $this->createInternal(Module::CLASS_LOGIN_HISTORY, $config);
    }

    /**
     * @inheritdoc
     * @return AccountPasswordHistory model instance.
     */
    public function createPasswordHistory(array $config = [])
    {
        return $this->createInternal(Module::CLASS_PASSWORD_HISTORY, $config);
    }

    /**
     * @inheritdoc
     * @return string the generated token.
     */
    public function createToken(array $config = [])
    {
        return $this->createInternal(Module::CLASS_TOKEN, $config);
    }

    /**
     * @inheritdoc
     * @return AccountToken
     */
    public function findToken($condition)
    {
        return $this->findInternal(Module::CLASS_TOKEN, $condition);
    }

    /**
     * @inheritdoc
     * @return AccountToken token model.
     */
    public function findValidToken($type, $token)
    {
        $tokenExpireTime = Module::getParam(Module::PARAM_TOKEN_EXPIRE_TIME);

        /** @var AccountToken $modelClass */
        $modelClass = Module::getInstance()->getClassName(Module::CLASS_TOKEN);

        return $modelClass::find()
            ->where([
                'type' => $type,
                'token' => $token,
                'status' => $this->getStatusCode(Module::CLASS_TOKEN, Module::STATUS_UNUSED),
            ])
            ->andWhere('(UNIX_TIMESTAMP() - UNIX_TIMESTAMP(createdAt)) < :expireTime',
                [':expireTime' => $tokenExpireTime])
            ->one();
    }

    /**
     * @inheritdoc
     */
    public function useToken(ActiveRecord $model)
    {
        $this->transitionInternal($model, $this->getStatusCode(Module::CLASS_TOKEN, Module::STATUS_USED));
    }

    /**
     * @inheritdoc
     */
    public function createLoginForm(array $config = [])
    {
        return $this->createModelInternal(Module::CLASS_LOGIN_FORM, $config);
    }

    /**
     * @inheritdoc
     */
    public function createSignupForm(array $config = [])
    {
        return $this->createModelInternal(Module::CLASS_SIGNUP_FORM, $config);
    }

    /**
     * @inheritdoc
     */
    public function createConnectForm(array $config = [])
    {
        return $this->createModelInternal(Module::CLASS_CONNECT_FORM, $config);
    }

    /**
     * @inheritdoc
     */
    public function createForgotPasswordForm(array $config = [])
    {
        return $this->createModelInternal(Module::CLASS_FORGOT_PASSWORD_FORM, $config);
    }

    /**
     * @inheritdoc
     */
    public function createPasswordForm(array $config = [])
    {
        return $this->createModelInternal(Module::CLASS_PASSWORD_FORM, $config);
    }

    /**
     * Returns the status code for a specific model status.
     *
     * @param string $className class name.
     * @param string $status status identifier.
     * @return int status code.
     */
    public function getStatusCode($className, $status)
    {
        if (!isset($this->statusMap[$className])) {
            throw new InvalidParamException("Trying to get status code for unknown class '$className'.");
        }
        if (!isset($this->statusMap[$className][$status])) {
            throw new InvalidParamException("Trying to get status code for unknown status '$status'.");
        }
        return $this->statusMap[$className][$status];
    }

    /**
     * Initializes the status map.
     */
    protected function initStatusMap()
    {
        $this->statusMap = array_merge(
            [
                Module::CLASS_ACCOUNT => [
                    Module::STATUS_UNACTIVATED => 0,
                    Module::STATUS_ACTIVATED => 1,
                ],
                Module::CLASS_TOKEN => [
                    Module::STATUS_UNUSED => 0,
                    Module::STATUS_USED => 1,
                ],
            ],
            $this->statusMap
        );
    }

    /**
     * Creates a new active record of the given class.
     *
     * @param string $className active record class name.
     * @param array $config object configuration.
     * @param boolean $runSave whether to save the model.
     * @return ActiveRecord model instance.
     * @throws Exception if the model cannot be saved.
     */
    protected function createInternal($className, array $config = [], $runSave = true)
    {
        /** @var ActiveRecord $model */
        $model = $this->createModelInternal($className, $config);

        if ($runSave && !$model->save()) {
            throw new Exception("Failed to save model '$className' with errors '" . Json::encode($model->getErrors()) . "'.");
        }

        return $model;
    }

    /**
     * Creates a new model of the given class.
     *
     * @param string $className model class name.
     * @param array $config object configuration.
     * @return Model model instance.
     */
    protected function createModelInternal($className, array $config = [])
    {
        $modelClass = Module::getInstance()->getClassName($className);
        return new $modelClass($config);
    }

    /**
     * Updates attributes for the given model.
     *
     * @param ActiveRecord $model model instance.
     * @param array $attributes model attributes.
     * @throws Exception if the attributes were not updated.
     */
    protected function updateAttributesInternal(ActiveRecord $model, array $attributes)
    {
        if ($model->updateAttributes($attributes) === 0) {
            throw new Exception("Failed to update attributes '" . Json::encode($attributes) . "' for model '" . $model::className() . "'.");
        }
    }

    /**
     * Returns a model class using the given condition.
     *
     * @param string $className class name.
     * @param mixed $condition search condition.
     * @return ActiveRecord|null model instance or null if not found.
     */
    protected function findInternal($className, $condition)
    {
        /** @var ActiveRecord $modelClass */
        $modelClass = Module::getInstance()->getClassName($className);
        return $modelClass::findOne($condition);
    }

    /**
     * Changes the status of a model class.
     *
     * @param ActiveRecord $model model instance.
     * @param int $status new status.
     */
    protected function transitionInternal(ActiveRecord $model, $status)
    {
        $this->updateAttributesInternal($model, ['status' => $status]);
    }
}