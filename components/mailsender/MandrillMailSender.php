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

class MandrillMailSender extends Component implements MailSenderInterface
{
    // Mandrill template names
    const TEMPLATE_ACTIVATE = 'activation';
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

        $this->messages = array_merge(
            [
                static::TEMPLATE_ACTIVATE => ['template' => 'activation'],
                static::TEMPLATE_RESET_PASSWORD => ['template' => 'resetPassword'],
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
     * @return bool whether the mail was sent successfully.
     */
    protected function send(array $config = [])
    {
        $message = $this->messages[$config['template']];

        $template = $message['template'];
        unset($message['template']);

        $message['to'] = $config['to'];
        unset($config['to']);

        $message['from_email'] = $config['from'];
        unset($config['from']);

        $content = isset($config['data']) ? $config['data'] : [];
        unset($config['data']);

        $async = isset($message['async']) ? $message['async'] : false;
        unset($message['async']);

        $ipPool = isset($message['ipPool']) ? $message['ipPool'] : null;
        unset($message['ipPool']);

        $sendAt = isset($message['sendAt']) ? $message['sendAt'] : null;
        unset($message['sendAt']);

        return $this->_client->messages->sendTemplate($template, $content, $message, $async, $ipPool, $sendAt);
    }
}