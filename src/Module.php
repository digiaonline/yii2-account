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
use yii\base\Module as BaseModule;
use yii\captcha\Captcha;
use yii\captcha\CaptchaAction;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\User;

class Module extends BaseModule
{
    // Canonical module id.
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
    const CONTROLLER_AUTH = 'auth';
    const CONTROLLER_PASSWORD = 'password';
    const CONTROLLER_SIGNUP = 'signup';

    // Command types.
    const COMMAND_ACCOUNT = 'account';

    // Token types.
    const TOKEN_ACTIVATE = 'activate';
    const TOKEN_RESET_PASSWORD = 'resetPassword';
    const TOKEN_CHANGE_PASSWORD = 'changePassword';

    // Component identifiers.
    const COMPONENT_DATA_CONTRACT = 'dataContract';
    const COMPONENT_MAIL_SENDER = 'mailSender';
    const COMPONENT_PASSWORD_HASHER = 'passwordHasher';
    const COMPONENT_TOKEN_GENERATOR = 'tokenGenerator';

    // URL prefix.
    const URL_PREFIX = 'account';

    // URL routes.
    const URL_ROUTE_CAPTCHA = 'auth/captcha';
    const URL_ROUTE_CLIENT_AUTH = 'auth/client';
    const URL_ROUTE_LOGIN = 'auth/login';
    const URL_ROUTE_LOGOUT = 'auth/logout';
    const URL_ROUTE_SIGNUP = 'signup/index';
    const URL_ROUTE_SIGNUP_DONE = 'signup/done';
    const URL_ROUTE_ACTIVATE = 'signup/activate';
    const URL_ROUTE_CONNECT = 'signup/connect';
    const URL_ROUTE_CHANGE_PASSWORD = 'password/change';
    const URL_ROUTE_FORGOT_PASSWORD = 'password/forgot';
    const URL_ROUTE_FORGOT_PASSWORD_DONE = 'password/forgot-done';
    const URL_ROUTE_RESET_PASSWORD = 'password/reset';

    const REDIRECT_LOGIN = 'login';
    const REDIRECT_SIGNUP = 'signup';
    const REDIRECT_ACTIVATE = 'activate';
    const REDIRECT_CONNECT = 'connect';
    const REDIRECT_CHANGE_PASSWORD = 'changePassword';
    const REDIRECT_FORGOT_PASSWORD = 'forgotPassword';
    const REDIRECT_RESET_PASSWORD = 'resetPassword';

    // Translation category prefix.
    const I18N_PREFIX = 'nord/account/';

    /**
     * @var array map over classes used by this module.
     */
    public $classMap = [];
    /**
     * @var boolean whether to enable account activation (defaults to true).
     */
    public $enableActivation = true;
    /**
     * @var boolean whether to enable signing up (defaults to true).
     */
    public $enableSignup = true;
    /**
     * @var boolean whether to enable CAPTCHA on sign up (defaults to false).
     */
    public $enableCaptcha = false;
    /**
     * @var boolean whether to enable client authentication, e.g. Facebook (defaults to false).
     */
    public $enableClientAuth = false;
    /**
     * @var array configuration that is passed to the captcha action.
     */
    public $captchaConfig = [];
    /**
     * @var array configuration that is passed to the web user.
     */
    public $userConfig = [];
    /**
     * @var array configuration that is passed to the password validator.
     */
    public $passwordConfig = [];
    /**
     * @var array list of clients that can be used for client authentication.
     */
    public $clientAuthConfig = [];
    /**
     * @var array configuration over the URLs used by this module.
     */
    public $urlConfig = [];
    /**
     * @var array configuration over the redirects done by this module.
     */
    public $redirectConfig = [];
    /**
     * @var string name of the username attribute (defaults to 'username').
     */
    public $usernameAttribute = 'username';
    /**
     * @var string name of the email attribute (defaults to 'email').
     */
    public $emailAttribute = 'email';
    /**
     * @var string name of the password attribute (defaults to 'password').
     */
    public $passwordAttribute = 'password';
    /**
     * @var string message source to use for this module.
     */
    public $messageSource = 'yii\i18n\PhpMessageSource';
    /**
     * @var string path to message files used by yii\i18n\PhpMessageSource.
     */
    public $messagePath = '@nord/account/messages';
    /**
     * @var string path to the application layout to use for this module.
     */
    public $layout = '@app/views/layouts/main';
    /**
     * @var string name of the key to use for setting flash messages.
     */
    public $flashMessageKey = 'account';
    /**
     * @var string default controller.
     */
    public $defaultController = self::CONTROLLER_AUTH;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->initComponents();
        $this->initClassMap();
        $this->initParams();
        $this->initUrlConfig();
        $this->initRedirectConfig();
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
        $this->params = ArrayHelper::merge(
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
     * Initializes the URL configuration for this module.
     */
    protected function initUrlConfig()
    {
        if (!isset($this->urlConfig['prefix'])) {
            $this->urlConfig['prefix'] = self::URL_PREFIX;
        }
        if (!isset($this->urlConfig['routePrefix'])) {
            $this->urlConfig['routePrefix'] = self::URL_PREFIX;
        }
        if (!isset($this->urlConfig['rules'])) {
            $this->urlConfig['rules'] = [];
        }
        $this->urlConfig['rules'] = array_merge(
            $this->urlConfig['rules'],
            [
                'captcha' => self::URL_ROUTE_CAPTCHA,
                'clientAuth' => self::URL_ROUTE_CLIENT_AUTH,
                'login' => self::URL_ROUTE_LOGIN,
                'logout' => self::URL_ROUTE_LOGOUT,
                'signup' => self::URL_ROUTE_SIGNUP,
                'signupDone' => self::URL_ROUTE_SIGNUP_DONE,
                'activate/<token:[a-zA-Z0-9_-]+>' => self::URL_ROUTE_ACTIVATE,
                'connect' => self::URL_ROUTE_CONNECT,
                'changePassword/<token:[a-zA-Z0-9_-]+>' => self::URL_ROUTE_CHANGE_PASSWORD,
                'forgotPassword' => self::URL_ROUTE_FORGOT_PASSWORD,
                'forgotPasswordDone' => self::URL_ROUTE_FORGOT_PASSWORD_DONE,
                'resetPassword/<token:[a-zA-Z0-9_-]+>' => self::URL_ROUTE_RESET_PASSWORD,
            ]
        );
    }

    /**
     * Initializes the redirect configuration for this module.
     */
    protected function initRedirectConfig()
    {
        $this->redirectConfig = ArrayHelper::merge(
            [
                self::REDIRECT_SIGNUP => $this->enableActivation ? [self::URL_ROUTE_SIGNUP_DONE] : [self::URL_ROUTE_LOGIN],
                self::REDIRECT_ACTIVATE => [self::URL_ROUTE_LOGIN],
                self::REDIRECT_CHANGE_PASSWORD => [self::URL_ROUTE_LOGIN],
                self::REDIRECT_FORGOT_PASSWORD => [self::URL_ROUTE_FORGOT_PASSWORD_DONE],
                self::REDIRECT_RESET_PASSWORD => [self::URL_ROUTE_LOGIN],
            ],
            $this->redirectConfig
        );
    }

    /**
     * Registers the translations for this module.
     */
    protected function registerTranslations()
    {
        Yii::$app->i18n->translations[self::I18N_PREFIX . '*'] = [
            'class' => $this->messageSource,
            'sourceLanguage' => 'en-US',
            'basePath' => $this->messagePath,
            'fileMap' => $this->createTranslationFileMap(),
        ];
    }

    /**
     * Creates a map from the translation category to file for this module.
     *
     * @return array the file map.
     */
    protected function createTranslationFileMap()
    {
        $fileMap = [];
        $directory = Yii::getAlias('@nord/account/messages/templates');
        $files = FileHelper::findFiles($directory);
        foreach ($files as $filePath) {
            $fileName = substr($filePath, strrpos($filePath, '/') + 1);
            $category = substr($fileName, 0, strrpos($fileName, '.'));
            $fileMap["nord/account/$category"] = $fileName;
        }
        return $fileMap;
    }

    /**
     * Generates a new truly unique random token and saves it in the database.
     *
     * @param string $type token type.
     * @param integer $accountId account id.
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
     * Loads a token of a specific type.
     *
     * @param string $type token type.
     * @param string $token token string.
     * @return AccountToken
     */
    public function loadToken($type, $token)
    {
        return $this->getDataContract()->findValidToken($type, $token);
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
     * @return string|integer parameter value.
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
     * Creates a URL route for this module.
     *
     * @param string $route desired route.
     * @return string the generated route.
     */
    public function createRoute($route)
    {
        return '/' . $this->urlConfig['routePrefix'] . '/' . $route;
    }

    /**
     * Returns the redirect URL for a specific type.
     *
     * @param string $type redirect type.
     * @return mixed the redirect URL.
     */
    public function getRedirectUrl($type)
    {
        return isset($this->redirectConfig[$type]) ? $this->redirectConfig[$type] : Yii::$app->user->getReturnUrl();
    }

    /**
     * Creates an URL to this module.
     *
     * @param string|array $route URL route.
     * @param boolean|string $scheme URL scheme (defaults to 'false', meaning relative URL).
     * @return string the generated URL.
     */
    public function createUrl($route, $scheme = false)
    {
        $route[0] = $this->createRoute($route[0]);
        return Url::toRoute($route, $scheme);
    }

    /**
     * Initializes the class map.
     */
    protected function initClassMap()
    {
        $this->classMap = ArrayHelper::merge(
            [
                self::CLASS_ACCOUNT => Account::className(),
                self::CLASS_TOKEN => AccountToken::className(),
                self::CLASS_PROVIDER => AccountProvider::className(),
                self::CLASS_LOGIN_HISTORY => AccountLoginHistory::className(),
                self::CLASS_PASSWORD_HISTORY => AccountPasswordHistory::className(),
                self::CLASS_LOGIN_FORM => LoginForm::className(),
                self::CLASS_PASSWORD_FORM => PasswordForm::className(),
                self::CLASS_SIGNUP_FORM => SignupForm::className(),
                self::CLASS_CONNECT_FORM => ConnectForm::className(),
                self::CLASS_FORGOT_PASSWORD_FORM => ForgotPasswordForm::className(),
                self::CLASS_WEB_USER => User::className(),
                self::CLASS_CAPTCHA => Captcha::className(),
                self::CLASS_CAPTCHA_ACTION => CaptchaAction::className(),
                self::CLASS_PASSWORD_BEHAVIOR => PasswordAttributeBehavior::className(),
                self::CLASS_PASSWORD_VALIDATOR => PasswordStrengthValidator::className(),
            ],
            $this->classMap
        );
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
        return Yii::t(self::I18N_PREFIX . $category, $message, $params);
    }
}
