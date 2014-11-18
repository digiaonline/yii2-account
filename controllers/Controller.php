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
use yii\web\Controller as ControllerBase;
use yii\web\HttpException;

/**
 * @property Module $module
 */
class Controller extends ControllerBase
{
    /**
     * Loads a token of a specific type.
     *
     * @param string $type token type.
     * @param string $token token string.
     * @return AccountToken
     */
    protected function loadToken($type, $token)
    {
        $model = $this->module->getDataContract()->findToken($type, $token);
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
        throw new HttpException(401, $message === null ? Module::t('errors', 'Access denied.') : $message);
    }

    /**
     * @param string $message error message.
     * @throws HttpException when called.
     */
    public function pageNotFound($message = null)
    {
        throw new HttpException(404, $message === null ? Module::t('errors', 'Page not found.') : $message);
    }

    /**
     * @param string $message error message.
     * @throws HttpException when called.
     */
    public function fatalError($message = null)
    {
        throw new HttpException(500, $message === null ? Module::t('errors', 'Something went wrong.') : $message);
    }
}