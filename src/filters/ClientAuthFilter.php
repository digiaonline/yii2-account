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
use yii\base\ActionFilter;

/**
 * @property Controller $owner
 */
class ClientAuthFilter extends ActionFilter
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!Module::getInstance()->enableClientAuth) {
            $this->owner->pageNotFound();
        }
        return true;
    }
}