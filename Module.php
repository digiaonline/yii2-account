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

use nord\yii\account\components\datacontract\DataContract;
use nord\yii\account\components\mailsender\LocalMailSender;
use nord\yii\account\components\mailsender\MailSenderInterface;
use nord\yii\account\components\tokengenerator\RandomLibTokenGenerator;
use nord\yii\account\components\tokengenerator\TokenGeneratorInterface;
use Yii;
use yii\base\InvalidParamException;
use yii\base\Module as ModuleBase;
use yii\base\Exception;

class Module extends ModuleBase
{
    // Parameter types.
    const PARAM_FROM_EMAIL_ADDRESS = 'fromEmailAddress';
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
     * @var string message source to use for this module.
     */
    public $messageSource = 'yii\i18n\PhpMessageSource';

    /**
     * @var string default controller.
     */
    public $defaultController = 'login';

    /**
     * @var string namespace used by controllers in this module.
     */
    public $controllerNamespace = 'nord\yii\account\controllers';

    // TODO Add support for CAPTCHA

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->initComponents();
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
                'classMap' => $this->classMap,
            ],
            self::COMPONENT_MAIL_SENDER => [
                'class' => LocalMailSender::className(),
            ],
            self::COMPONENT_TOKEN_GENERATOR => [
                'class' => RandomLibTokenGenerator::className(),
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
     * @throws Exception if the token cannot be generated.
     * @return string the generated token.
     */
    public function generateToken($type, $accountId)
    {
        $token = Module::getInstance()->getTokenGenerator()->generate();
        $this->getDataContract()->createToken([
            'type' => $type,
            'accountId' => $accountId,
            'token' => $token,
        ]);
        return $token;
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
}
