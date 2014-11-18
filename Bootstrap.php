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

use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        // Override the web user configuration
        $app->set('user', [
            'class' => 'yii\web\User',
            'identityClass' => 'nord\yii\account\models\Account',
            'enableAutoLogin' => true,
            'loginUrl' => ['/account/authenticate/login'],
        ]);
    }
}