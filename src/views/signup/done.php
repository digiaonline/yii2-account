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

$this->title = Module::t('views', 'Sign up successfull');
?>
<div class="register-controller done-action">

    <h1 class="page-header"><?= Html::encode($this->title); ?></h1>

    <p class="lead"><?= Module::t('views',
            'You will soon receive an email with instructions on how to activate your account.'); ?></p>

    <p><?= Html::a(Module::t('views', 'Done'), Yii::$app->homeUrl, ['class' => 'btn btn-primary btn-lg']) ?></p>

</div>
