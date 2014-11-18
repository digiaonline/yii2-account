<?php
/*
 * This file is part of Account.
 *
 * (c) 2014 Nord Software
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use nord\yii\account\Module;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $actionUrl string */
?>
<?= Module::t('email', 'Reset password'); ?><br><br>
<?= Module::t('email', 'Please click the link below to reset the password for your account:'); ?><br>
<?= Html::a($actionUrl, $actionUrl); ?>