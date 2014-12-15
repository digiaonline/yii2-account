<?php
/*
 * This file is part of Account.
 *
 * (c) 2014 Nord Software
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nord\yii\account\filters;

use nord\yii\account\controllers\Controller;
use nord\yii\account\Module;
use Yii;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

/**
 * @property Controller $owner
 */
class TokenFilter extends ActionFilter
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (Yii::$app->request->get('token') === null) {
            throw new ForbiddenHttpException(Module::t('errors', 'Invalid authentication token.'));
        }
        return true;
    }
}