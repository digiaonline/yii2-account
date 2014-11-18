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
use nord\yii\account\models\AccountToken;
use nord\yii\account\models\ForgotPasswordForm;
use nord\yii\account\models\LoginForm;
use nord\yii\account\models\PasswordForm;
use nord\yii\account\models\SignupForm;
use nord\yii\account\Module;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidParamException;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\Json;

class DataContract extends Component implements DataContractInterface
{
    // Class name types.
    const CLASS_ACCOUNT = 'account';
    const CLASS_TOKEN = 'token';
    const CLASS_USER_IDENTITY = 'userIdentity';
    const CLASS_LOGIN_FORM = 'loginForm';
    const CLASS_PASSWORD_FORM = 'passwordForm';
    const CLASS_SIGNUP_FORM = 'signupForm';
    const CLASS_FORGOT_PASSWORD_FORM = 'forgotPasswordForm';
    const CLASS_LOGIN_HISTORY = 'loginHistory';
    const CLASS_PASSWORD_HISTORY = 'passwordHistory';

    // Model status types.
    const STATUS_UNACTIVATED = 'unactivated';
    const STATUS_ACTIVATED = 'activated';
    const STATUS_UNUSED = 'unused';
    const STATUS_USED = 'used';

    /**
     * @var array map over classes to use by this contract.
     */
    public $classMap = [];

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

        $this->initClassMap();
        $this->initStatusMap();
    }

    /**
     * @inheritdoc
     * @return Account
     */
    public function createAccount(array $config = [])
    {
        return $this->createInternal(self::CLASS_ACCOUNT, $config, false);
    }

    /**
     * @inheritdoc
     * @return Account model instance.
     */
    public function findAccount($condition)
    {
        return $this->findInternal(self::CLASS_ACCOUNT, $condition);
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
        $this->transitionInternal($model, self::CLASS_ACCOUNT, self::STATUS_ACTIVATED);
    }

    /**
     * @inheritdoc
     */
    public function isAccountActivated(ActiveRecord $model)
    {
        if (!Module::getInstance()->enableActivation) {
            return true;
        }
        return $model->status === $this->getStatusCode(self::CLASS_ACCOUNT, self::STATUS_ACTIVATED);
    }

    /**
     * @inheritdoc
     */
    public function isAccountLocked(ActiveRecord $model)
    {
        $numAllowedAttempts = Module::getInstance()->getParam(Module::PARAM_NUM_ALLOWED_FAILED_LOGIN_ATTEMPTS);
        $lockoutExpireTime = Module::getInstance()->getParam(Module::PARAM_LOCKOUT_EXPIRE_TIME);

        if ($numAllowedAttempts === 0) {
            return false;
        }

        /** @var ActiveRecord $modelClass */
        $modelClass = $this->getClassName(self::CLASS_LOGIN_HISTORY);

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
        $passwordExpireTime = Module::getInstance()->getParam(Module::PARAM_PASSWORD_EXPIRE_TIME);

        if ($passwordExpireTime === 0) {
            return false;
        }

        /** @var ActiveRecord $modelClass */
        $modelClass = $this->getClassName(self::CLASS_PASSWORD_HISTORY);

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
        /** @var Account $model */
        $strategy = $model->getStrategy();
        $models = $this->getAccountPasswordHistory($model);
        foreach ($models as $model) {
            $strategy->setSalt($model->salt);
            if ($model->password === $strategy->encode($password)) {
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
        $modelClass = $this->getClassName(self::CLASS_LOGIN_HISTORY);

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
        $modelClass = $this->getClassName(self::CLASS_PASSWORD_HISTORY);
        return $modelClass::find()
            ->where(['accountId' => $model->getPrimaryKey()])
            ->orderBy('createdAt DESC')
            ->limit(10)
            ->all();
    }

    /**
     * @inheritdoc
     * @throws Exception if the history entry cannot be saved.
     * @return AccountLoginHistory model instance.
     */
    public function createLoginHistory(array $config = [])
    {
        return $this->createInternal(self::CLASS_LOGIN_HISTORY, $config);
    }

    /**
     * @inheritdoc
     * @throws Exception if the history entry cannot be saved.
     * @return AccountPasswordHistory model instance.
     */
    public function createPasswordHistory(array $config = [])
    {
        return $this->createInternal(self::CLASS_PASSWORD_HISTORY, $config);
    }

    /**
     * @inheritdoc
     * @throws Exception if the token cannot be generated.
     * @return string the generated token.
     */
    public function createToken(array $config = [])
    {
        return $this->createInternal(self::CLASS_TOKEN, $config);
    }

    /**
     * @inheritdoc
     * @return AccountToken token model.
     */
    public function findToken($type, $token)
    {
        $tokenExpireTime = Module::getInstance()->getParam(Module::PARAM_TOKEN_EXPIRE_TIME);

        /** @var AccountToken $modelClass */
        $modelClass = $this->getClassName(self::CLASS_TOKEN);

        return $modelClass::find()
            ->where([
                'type' => $type,
                'token' => $token,
                'status' => $this->getStatusCode(self::CLASS_TOKEN, self::STATUS_UNUSED),
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
        $this->transitionInternal($model, self::CLASS_TOKEN, self::STATUS_USED);
    }

    /**
     * @inheritdoc
     */
    public function createLoginForm(array $config = [])
    {
        return $this->createModelInternal(self::CLASS_LOGIN_FORM, $config);
    }

    /**
     * @inheritdoc
     */
    public function createSignupForm(array $config = [])
    {
        return $this->createModelInternal(self::CLASS_SIGNUP_FORM, $config);
    }

    /**
     * @inheritdoc
     */
    public function createForgotPasswordForm(array $config = [])
    {
        return $this->createModelInternal(self::CLASS_FORGOT_PASSWORD_FORM, $config);
    }

    /**
     * @inheritdoc
     */
    public function createPasswordForm(array $config = [])
    {
        return $this->createModelInternal(self::CLASS_PASSWORD_FORM, $config);
    }

    /**
     * Initializes the class map.
     */
    protected function initClassMap()
    {
        $this->classMap = array_merge(
            [
                self::CLASS_ACCOUNT => Account::className(),
                self::CLASS_TOKEN => AccountToken::className(),
                self::CLASS_LOGIN_HISTORY => AccountLoginHistory::className(),
                self::CLASS_PASSWORD_HISTORY => AccountPasswordHistory::className(),
                self::CLASS_LOGIN_FORM => LoginForm::className(),
                self::CLASS_PASSWORD_FORM => PasswordForm::className(),
                self::CLASS_SIGNUP_FORM => SignupForm::className(),
                self::CLASS_FORGOT_PASSWORD_FORM => ForgotPasswordForm::className(),
            ],
            $this->classMap
        );
    }

    /**
     * Initializes the status map.
     */
    protected function initStatusMap()
    {
        $this->statusMap = array_merge(
            [
                self::CLASS_ACCOUNT => [
                    self::STATUS_UNACTIVATED => 0,
                    self::STATUS_ACTIVATED => 1,
                ],
                self::CLASS_TOKEN => [
                    self::STATUS_UNUSED => 0,
                    self::STATUS_USED => 1,
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
        $modelClass = $this->getClassName($className);
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
        $modelClass = $this->getClassName($className);
        return $modelClass::findOne($condition);
    }

    /**
     * Changes the status of a model class.
     *
     * @param ActiveRecord $model model instance.
     * @param string $className class name.
     * @param string $status new status.
     */
    protected function transitionInternal(ActiveRecord $model, $className, $status)
    {
        $this->updateAttributesInternal($model, ['status' => $this->getStatusCode($className, $status)]);
    }

    /**
     * Returns the class name for a specific model class.
     *
     * @param string $type class type.
     * @throws InvalidParamException if the class cannot be found.
     * @return string class name.
     */
    protected function getClassName($type)
    {
        if (!isset($this->classMap[$type])) {
            throw new InvalidParamException("Trying to get class name for unknown class '$type'.");
        }
        return $this->classMap[$type];
    }

    /**
     * Returns the status code for a specific model status.
     *
     * @param string $className class name.
     * @param string $status status identifier.
     * @return int status code.
     */
    protected function getStatusCode($className, $status)
    {
        if (!isset($this->statusMap[$className])) {
            throw new InvalidParamException("Trying to get status code for unknown class '$className'.");
        }
        if (!isset($this->statusMap[$className][$status])) {
            throw new InvalidParamException("Trying to get status code for unknown status '$status'.");
        }
        return $this->statusMap[$className][$status];
    }
}