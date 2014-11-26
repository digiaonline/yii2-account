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
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model nord\yii\account\models\ConnectForm */
/* @var $provider nord\yii\account\models\AccountProvider */

$this->title = Module::t('views', 'Connect with {provider}', ['provider' => ucfirst($provider->name)]);
?>
<div class="register-controller connect-action">

    <div class="row">
        <div class="col-lg-5">

            <h1 class="page-header"><?= Html::encode($this->title); ?></h1>

            <p class="help-text">
                <?= Module::t('views', 'Please enter your e-mail address and desired username and to create your account.'); ?>
            </p>

            <?php $form = ActiveForm::begin(['id' => 'connectform']); ?>

            <fieldset>
                <?= $form->field($model, 'email'); ?>
                <?= $form->field($model, 'username'); ?>
            </fieldset>

            <?= Html::submitButton(Module::t('views', 'Connect'), ['class' => 'btn btn-lg btn-primary']); ?>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

</div>
