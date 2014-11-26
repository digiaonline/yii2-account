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

interface MailSenderInterface
{
    /**
     * Sends an account activation e-mail message.
     *
     * @param array $config mail configurations.
     * @return boolean whether the mail was sent successfully.
     */
    public function sendActivationMail(array $config = []);

    /**
     * Sends an account reset password e-mail message.
     *
     * @param array $config mail configurations.
     * @return boolean whether the mail was sent successfully.
     */
    public function sendResetPasswordMail(array $config = []);
}