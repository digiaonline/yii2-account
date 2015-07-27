<?php
/*
 * This file is part of Account.
 *
 * (c) 2014 Nord Software
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nord\yii\account\components\mailsender;

use Mandrill;
use yii\base\Component;
use yii\helpers\ArrayHelper;

class MandrillMailSender extends Component implements MailSenderInterface
{
    // Mandrill template names
    const TEMPLATE_ACTIVATE = 'activateAccount';
    const TEMPLATE_RESET_PASSWORD = 'resetPassword';

    /**
     * @var string Mandrill API key.
     */
    public $apiKey;

    /**
     * @var array message configurations.
     */
    public $messages = [];

    /**
     * @var Mandrill
     */
    private $_client;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->messages = ArrayHelper::merge(
            [
                static::TEMPLATE_ACTIVATE => ['template' => 'activate-account'],
                static::TEMPLATE_RESET_PASSWORD => ['template' => 'reset-password'],
            ],
            $this->messages
        );

        $this->_client = new Mandrill($this->apiKey);
    }

    /**
     * @inheritdoc
     */
    public function sendActivationMail(array $config = [])
    {
        $config['template'] = static::TEMPLATE_ACTIVATE;
        return $this->send($config);
    }

    /**
     * @inheritdoc
     */
    public function sendResetPasswordMail(array $config = [])
    {
        $config['template'] = static::TEMPLATE_RESET_PASSWORD;
        return $this->send($config);
    }

    /**
     * Sends an e-mail message.
     *
     * @param array $config mail configurations.
     * @return boolean whether the mail was sent successfully.
     */
    protected function send(array $config = [])
    {
        $message = $this->messages[$config['template']];

        $template = ArrayHelper::remove($message, 'template');

        $message['to'] = ArrayHelper::getValue($message, 'to', []);
        foreach (ArrayHelper::remove($config, 'to', []) as $email) {
            $message['to'][] = ['email' => $email, 'type' => 'to'];
        }

        $message['merge_language'] = ArrayHelper::remove($config, 'mergeLanguage', 'handlebars');

        // Note we are not batching emails so it should be safe to use the global variables
        $message['global_merge_vars'] = ArrayHelper::getValue($message, 'global_merge_vars', []);
        foreach (ArrayHelper::remove($config, 'data', []) as $key => $value) {
            $message['global_merge_vars'][] = ['name' => $key, 'content' => $value];
        }

        $async = ArrayHelper::remove($message, 'async', false);
        $ipPool = ArrayHelper::remove($message, 'ipPool');
        $sendAt = ArrayHelper::remove($message, 'sendAt');

        return $this->_client->messages->sendTemplate($template, [], $message, $async, $ipPool, $sendAt);
    }
}