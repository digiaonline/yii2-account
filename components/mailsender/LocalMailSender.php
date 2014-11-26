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

abstract class LocalMailSender extends Component implements MailSenderInterface
{
    /**
     * Sends an e-mail message.
     *
     * @param array $config mail configurations.
     * @return boolean whether the mail was sent successfully.
     */
    abstract public function send(array $config = []);

    /**
     * @inheritdoc
     */
    public function sendActivationMail(array $config = [])
    {
        $config['body'] = Yii::$app->controller->renderPartial('/mail/activate', $config['data']);
        return $this->send($config);
    }

    /**
     * @inheritdoc
     */
    public function sendResetPasswordMail(array $config = [])
    {
        $config['body'] = Yii::$app->controller->renderPartial('/mail/resetPassword', $config['data']);
        return $this->send($config);
    }
}