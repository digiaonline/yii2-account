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
/* @var $title string */
/* @var $reason string */
?>
<div class="password-controller change-action">

    <div class="row">
        <div class="col-lg-5">

            <h1 class="page-header"><?= Html::encode($title); ?></h1>

            <p class="lead"><?= Html::encode($reason); ?></p>

            <p class="help-block">
                <?= Module::t(
                    'views',
                    'Please enter a new password twice to change the your password.'
                ); ?>
            </p>

            <?php $form = ActiveForm::begin(['id' => 'passwordform']); ?>

            <fieldset>
                <?= $form->field($model, 'password')->passwordInput(); ?>
                <?= $form->field($model, 'verifyPassword')->passwordInput(); ?>
            </fieldset>

            <?= Html::submitButton(Module::t('views', 'Change Password'), ['class' => 'btn btn-lg btn-primary']); ?>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

</div>
