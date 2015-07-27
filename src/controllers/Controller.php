<?php
/*
 * This file is part of Account.
 *
 * (c) 2014 Nord Software
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nord\yii\account\controllers;

use nord\yii\account\models\AccountToken;
use nord\yii\account\Module;
use Yii;
use yii\web\Controller as BaseController;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * @property Module $module
 */
class Controller extends BaseController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->layout = $this->module->layout;
    }

    /**
     * Loads a token of a specific type.
     *
     * @param string $type token type.
     * @param string $token token string.
     * @return AccountToken
     */
    protected function loadToken($type, $token)
    {
        $model = $this->module->loadToken($type, $token);
        if ($model === null) {
            $this->accessDenied(Module::t('errors', 'Invalid authentication token.'));
        }
        return $model;
    }

    /**
     * @param string $message error message.
     * @throws HttpException when called.
     */
    public function accessDenied($message = null)
    {
        throw new ForbiddenHttpException($message === null ? Module::t('errors', 'Access denied.') : $message);
    }

    /**
     * @param string $message error message.
     * @throws HttpException when called.
     */
    public function pageNotFound($message = null)
    {
        throw new NotFoundHttpException($message === null ? Module::t('errors', 'Page not found.') : $message);
    }

    /**
     * @param string $message error message.
     * @throws HttpException when called.
     */
    public function fatalError($message = null)
    {
        throw new ServerErrorHttpException($message === null ? Module::t('errors', 'Something went wrong.') : $message);
    }
}