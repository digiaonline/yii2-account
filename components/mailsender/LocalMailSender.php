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

use Yii;
use yii\base\Component;

class LocalMailSender extends Component implements MailSenderInterface
{
    /**
     * @inheritdoc
     */
    public function sendActivationMail(array $config = [])
    {
        $config['body'] = Yii::$app->controller->renderPartial('/email/activate', $config['data']);
        return $this->send($config);
    }

    /**
     * @inheritdoc
     */
    public function sendResetPasswordMail(array $config = [])
    {
        $config['body'] = Yii::$app->controller->renderPartial('/email/resetPassword', $config['data']);
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
        $config['headers'] = isset($config['headers']) ? $config['headers'] : [];
        $config['headers']['from'] = $config['from'];

        return mail(
            implode(',', $config['to']),
            $config['subject'],
            $config['body'],
            $config['headers'],
            $config['params']
        );
    }
}