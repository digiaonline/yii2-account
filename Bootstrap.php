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

use nord\yii\account\commands\AccountCommand;
use nord\yii\account\components\datacontract\DataContract;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;

class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        if (!$app->hasModule(Module::MODULE_ID)) {
            throw new InvalidConfigException("Failed to bootstrap the 'account' module.");
        }
        if ($app instanceof \yii\web\Application) {
            $this->bootstrapWebApplication($app);
        } elseif ($app instanceof \yii\console\Application) {
            $this->bootstrapConsoleApplication($app);
        }
    }

    /**
     * Bootstraps the module for the console application.
     *
     * @param \yii\console\Application $app application instance.
     */
    protected function bootstrapConsoleApplication($app)
    {
        /** @var Module $module */
        $module = $app->getModule(Module::MODULE_ID);
        $module->controllerNamespace = 'nord\yii\account\commands';
        $module->defaultController = 'account';

        $app->controllerMap[$module->id] = [
            'class' => AccountCommand::className(),
            'module' => $module,
        ];
    }

    /**
     * Bootstraps the module for the web application.
     *
     * @param \yii\web\Application $app application instance.
     */
    protected function bootstrapWebApplication($app)
    {
        /** @var Module $module */
        $module = $app->getModule(Module::MODULE_ID);
        $dataContract = $module->getDataContract();
        $app->set('user', [
            'class' => $dataContract->getClassName(DataContract::CLASS_WEB_USER),
            'identityClass' => $dataContract->getClassName(DataContract::CLASS_ACCOUNT),
            'enableAutoLogin' => true,
            'loginUrl' => ['/account/authenticate/login'],
        ]);
    }
}