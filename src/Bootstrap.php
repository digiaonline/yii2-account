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
use yii\authclient\clients\GoogleOpenId;
use yii\authclient\Collection;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\GroupUrlRule;

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
        $app->setAliases(['@nord/yii/account' => __DIR__]);
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
        $module->defaultController = Module::COMMAND_ACCOUNT;

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

        // prepend the URL rules to the URL manager
        $app->getUrlManager()->addRules([new GroupUrlRule($module->urlConfig)], false/* append */); 

        // Configure the web user component
        $app->set('user', ArrayHelper::merge([
                'class' => $module->getClassName(Module::CLASS_WEB_USER),
                'identityClass' => $module->getClassName(Module::CLASS_ACCOUNT),
                'loginUrl' => [Module::URL_ROUTE_LOGIN],
                'enableAutoLogin' => true,
            ],
            $module->userConfig
        ));

        // configure client authentication if necessary
        if ($module->enableClientAuth && !$app->has('authClientCollection')) {
            $app->set('authClientCollection', ArrayHelper::merge([
                    'class' => Collection::className(),
                    'clients' => ['google' => ['class' => GoogleOpenId::className()]],
                ],
                $module->clientAuthConfig
            ));
        }
    }
}
