<?php
/*
 * This file is part of Account.
 *
 * (c) 2014 Nord Software
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nord\yii\account;

use nord\yii\account\behaviors\PasswordAttributeBehavior;
use nord\yii\account\components\datacontract\DataContract;
use nord\yii\account\components\mailsender\MailSenderInterface;
use nord\yii\account\components\mailsender\YiiMailSender;
use nord\yii\account\components\passwordhasher\PasswordHasherInterface;
use nord\yii\account\components\passwordhasher\YiiPasswordHasher;
use nord\yii\account\components\tokengenerator\TokenGeneratorInterface;
use nord\yii\account\components\tokengenerator\YiiTokenGenerator;
use nord\yii\account\models\Account;
use nord\yii\account\models\AccountLoginHistory;
use nord\yii\account\models\AccountPasswordHistory;
use nord\yii\account\models\AccountProvider;
use nord\yii\account\models\AccountToken;
use nord\yii\account\models\ConnectForm;
use nord\yii\account\models\ForgotPasswordForm;
use nord\yii\account\models\LoginForm;
use nord\yii\account\models\PasswordForm;
use nord\yii\account\models\SignupForm;
use nord\yii\account\validators\PasswordStrengthValidator;
use Yii;
use yii\base\InvalidParamException;
use yii\captcha\Captcha;
use yii\captcha\CaptchaAction;
use yii\web\User;

class Module extends \yii\base\Module
{
    const MODULE_ID = 'account';

    // Class name types.
    const CLASS_ACCOUNT = 'account';
    const CLASS_TOKEN = 'token';
    const CLASS_LOGIN_HISTORY = 'loginHistory';
    const CLASS_PASSWORD_HISTORY = 'passwordHistory';
    const CLASS_PROVIDER = 'provider';
    const CLASS_LOGIN_FORM = 'loginForm';
    const CLASS_PASSWORD_FORM = 'passwordForm';
    const CLASS_SIGNUP_FORM = 'signupForm';
    const CLASS_CONNECT_FORM = 'connectForm';
    const CLASS_FORGOT_PASSWORD_FORM = 'forgotPasswordForm';
    const CLASS_WEB_USER = 'webUser';
    const CLASS_CAPTCHA = 'captcha';
    const CLASS_CAPTCHA_ACTION = 'captchaAction';
    const CLASS_PASSWORD_BEHAVIOR = 'passwordBehavior';
    const CLASS_PASSWORD_VALIDATOR = 'passwordValidator';

    // Model status types.
    const STATUS_UNACTIVATED = 'unactivated';
    const STATUS_ACTIVATED = 'activated';
    const STATUS_UNUSED = 'unused';
    const STATUS_USED = 'used';

    // Parameter types.
    const PARAM_FROM_EMAIL_ADDRESS = 'fromEmailAddress';
    const PARAM_MIN_USERNAME_LENGTH = 'minUsernameLength';
    const PARAM_MIN_PASSWORD_LENGTH = 'minPasswordLength';
    const PARAM_NUM_ALLOWED_FAILED_LOGIN_ATTEMPTS = 'numAllowedFailedLoginAttempts';
    const PARAM_LOGIN_EXPIRE_TIME = 'loginExpireTime';
    const PARAM_ACTIVATE_EXPIRE_TIME = 'activateExpireTime';
    const PARAM_RESET_PASSWORD_EXPIRE_TIME = 'resetPasswordExpireTime';
    const PARAM_PASSWORD_EXPIRE_TIME = 'passwordExpireTime';
    const PARAM_LOCKOUT_EXPIRE_TIME = 'lockoutExpireTime';
    const PARAM_TOKEN_EXPIRE_TIME = 'tokenExpireTime';

    // Controller types.
    const CONTROLLER_AUTHENTICATE = 'authenticate';
    const CONTROLLER_PASSWORD = 'password';
    const CONTROLLER_SIGNUP = 'signup';

    // Token types.
    const TOKEN_ACTIVATE = 'activate';
    const TOKEN_RESET_PASSWORD = 'resetPassword';
    const TOKEN_CHANGE_PASSWORD = 'changePassword';

    // Component identifiers.
    const COMPONENT_DATA_CONTRACT = 'dataContract';
    const COMPONENT_MAIL_SENDER = 'mailSender';
    const COMPONENT_PASSWORD_HASHER = 'passwordHasher';
    const COMPONENT_TOKEN_GENERATOR = 'tokenGenerator';

    /**
     * @var array map over classes used by this module.
     */
    public $classMap = [];
    /**
     * @var bool whether to enable activation (defaults to true).
     */
    public $enableActivation = true;
    /**
     * @var bool whether to enable signing up (defaults to true).
     */
    public $enableSignup = true;
    /**
     * @var bool whether to enable client authentication, e.g. Facebook (defaults to false).
     */
    public $enableClientAuth = false;
    /**
     * @var array list of clients that can be used for client authentication.
     */
    public $authClients = ['google' => ['class' => 'yii\authclient\clients\GoogleOpenId']];
    /**
     * @var bool whether to enable CAPTCHA on sign up (defaults to false).
     */
    public $enableCaptcha = false;
    /**
     * @var array configuration that is passed to the captcha action.
     */
    public $captchaOptions = [];
    /**
     * @var string name of the attribute to use for logging in.
     */
    public $loginAttribute = 'username';
    /**
     * @var string name of the password attribute.
     */
    public $passwordAttribute = 'password';
    /**
     * @var array configuration that is passed to the password validator.
     */
    public $passwordStrategy = [];
    /**
     * @var string message source to use for this module.
     */
    public $messageSource = 'yii\i18n\PhpMessageSource';
    /**
     * @var string default controller.
     */
    public $defaultController = 'login';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->initComponents();
        $this->initClassMap();
        $this->initParams();
        $this->registerTranslations();
    }

    /**
     * Initializes the components for this module (if not already initialized).
     */
    protected function initComponents()
    {
        $components = [
            self::COMPONENT_DATA_CONTRACT => [
                'class' => DataContract::className(),
            ],
            self::COMPONENT_MAIL_SENDER => array(
                'class' => YiiMailSender::className(),
            ),
            self::COMPONENT_PASSWORD_HASHER => [
                'class' => YiiPasswordHasher::className(),
            ],
            self::COMPONENT_TOKEN_GENERATOR => [
                'class' => YiiTokenGenerator::className(),
            ],
        ];
        foreach ($components as $id => $definition) {
            if (!$this->has($id)) {
                $this->set($id, $definition);
            }
        }
    }

    /**
     * Initializes the configuration parameters for this module.
     */
    protected function initParams()
    {
        $this->params = array_merge(
            [
                self::PARAM_FROM_EMAIL_ADDRESS => 'admin@example.com',
                self::PARAM_MIN_USERNAME_LENGTH => 4,
                self::PARAM_MIN_PASSWORD_LENGTH => 6,
                self::PARAM_NUM_ALLOWED_FAILED_LOGIN_ATTEMPTS => 10,
                self::PARAM_LOGIN_EXPIRE_TIME => 2592000, // 30 days
                self::PARAM_ACTIVATE_EXPIRE_TIME => 2592000, // 30 days
                self::PARAM_RESET_PASSWORD_EXPIRE_TIME => 86400, // 1 day
                self::PARAM_PASSWORD_EXPIRE_TIME => 0, // disabled
                self::PARAM_LOCKOUT_EXPIRE_TIME => 600, // 10 minutes
                self::PARAM_TOKEN_EXPIRE_TIME => 3600, // 1 hour
            ],
            $this->params
        );
    }

    /**
     * Registers the translations for this module.
     */
    public function registerTranslations()
    {
        Yii::$app->i18n->translations['nord/account/*'] = [
            'class' => $this->messageSource,
            'sourceLanguage' => 'en-US',
            'basePath' => '@nord/yii/account/messages',
        ];
    }

    /**
     * Generates a new random token and saves it in the database.
     *
     * @param string $type token type.
     * @param int $accountId account id.
     * @return string the generated token.
     */
    public function generateToken($type, $accountId)
    {
        $dataContract = $this->getDataContract();
        $attributes = ['accountId' => $accountId, 'type' => $type];
        while (!isset($attributes['token'])) {
            $attributes['token'] = Module::getInstance()->getTokenGenerator()->generate();
            if ($dataContract->findToken($attributes) !== null) {
                unset($attributes['token']);
            }
        }
        $dataContract->createToken(['attributes' => $attributes]);
        return $attributes['token'];
    }

    /**
     * @return MailSenderInterface
     */
    public function getMailSender()
    {
        return $this->get(static::COMPONENT_MAIL_SENDER);
    }

    /**
     * @return DataContract
     */
    public function getDataContract()
    {
        return $this->get(static::COMPONENT_DATA_CONTRACT);
    }

    /**
     * @return PasswordHasherInterface
     */
    public function getPasswordHasher()
    {
        return $this->get(static::COMPONENT_PASSWORD_HASHER);
    }

    /**
     * @return TokenGeneratorInterface
     */
    public function getTokenGenerator()
    {
        return $this->get(static::COMPONENT_TOKEN_GENERATOR);
    }

    /**
     * Returns a configuration parameter for this module.
     *
     * @param string $name parameter name.
     * @return string|int parameter value.
     */
    public static function getParam($name)
    {
        $params = self::getInstance()->params;
        if (!isset($params[$name])) {
            throw new InvalidParamException("Trying to get unknown parameter '$name'.");
        }
        return $params[$name];
    }

    /**
     * Returns the class name for a specific model class.
     *
     * @param string $type class type.
     * @throws InvalidParamException if the class cannot be found.
     * @return string class name.
     */
    public function getClassName($type)
    {
        if (!isset($this->classMap[$type])) {
            throw new InvalidParamException("Trying to get class name for unknown class '$type'.");
        }
        return $this->classMap[$type];
    }

    /**
     * Translates the the given text in this module.
     *
     * @param string $category message category.
     * @param string $message text to translate.
     * @param array $params additional parameters.
     * @return string translated text.
     */
    public static function t($category, $message, array $params = [])
    {
        return Yii::t('nord/account/' . $category, $message, $params);
    }

    /**
     * Initializes the class map.
     */
    protected function initClassMap()
    {
        $this->classMap = array_merge(
            [
                Module::CLASS_ACCOUNT => Account::className(),
                Module::CLASS_TOKEN => AccountToken::className(),
                Module::CLASS_PROVIDER => AccountProvider::className(),
                Module::CLASS_LOGIN_HISTORY => AccountLoginHistory::className(),
                Module::CLASS_PASSWORD_HISTORY => AccountPasswordHistory::className(),
                Module::CLASS_LOGIN_FORM => LoginForm::className(),
                Module::CLASS_PASSWORD_FORM => PasswordForm::className(),
                Module::CLASS_SIGNUP_FORM => SignupForm::className(),
                Module::CLASS_CONNECT_FORM => ConnectForm::className(),
                Module::CLASS_FORGOT_PASSWORD_FORM => ForgotPasswordForm::className(),
                Module::CLASS_WEB_USER => User::className(),
                Module::CLASS_CAPTCHA => Captcha::className(),
                Module::CLASS_CAPTCHA_ACTION => CaptchaAction::className(),
                Module::CLASS_PASSWORD_BEHAVIOR => PasswordAttributeBehavior::className(),
                Module::CLASS_PASSWORD_VALIDATOR => PasswordStrengthValidator::className(),
            ],
            $this->classMap
        );
    }
}
