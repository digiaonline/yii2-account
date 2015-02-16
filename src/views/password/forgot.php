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
/* @var $model nord\yii\account\models\ForgotPasswordForm */

$this->title = Module::t('views', 'Forgot Password');
?>
<div class="password-controller forgot-action">

    <div class="row">
        <div class="col-lg-5">

            <h1 class="page-header"><?= Html::encode($this->title); ?></h1>

            <p class="help-block">
                <?= Module::t(
                    'views',
                    'Please enter your e-mail address and we will send you instructions on how to reset your password.'
                ); ?>
            </p>

            <?php $form = ActiveForm::begin(['id' => 'forgotpasswordform']); ?>

            <fieldset>
                <?= $form->field($model, 'email'); ?>
            </fieldset>

            <?= Html::submitButton(Module::t('views', 'Send'), ['class' => 'btn btn-lg btn-primary']); ?>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

</div>
