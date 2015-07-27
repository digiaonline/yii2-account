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

class YiiMailSender extends LocalMailSender
{
    /**
     * @inheritdoc
     */
    public function send(array $config = [])
    {
        return Yii::$app->mailer->compose()
            ->setTo($config['to'])
            ->setFrom($config['from'])
            ->setSubject($config['subject'])
            ->setHtmlBody($config['body'])
            ->send();
    }
}