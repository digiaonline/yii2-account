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
/* @var $model nord\yii\account\models\SignupForm */

$this->title = Module::t('views', 'Sign up');
?>
<div class="register-controller index-action">

    <div class="row">
        <div class="col-lg-5">

            <h1><?= Html::encode($this->title); ?></h1>

            <p class="help-block">
                <?= Module::t(
                    'views',
                    'If you already have an account &ndash; {loginLink}.',
                    ['loginLink' => Html::a(Module::t('views', 'Log in'), ['/account/authenticate/login'])]
                ); ?>
            </p>

            <?php $form = ActiveForm::begin(['id' => 'signup-form']); ?>

            <fieldset>
                <?= $form->field($model, 'email'); ?>
                <?= $form->field($model, 'username'); ?>
                <?= $form->field($model, 'password')->passwordInput(); ?>
                <?= $form->field($model, 'verifyPassword')->passwordInput(); ?>
            </fieldset>

            <?= Html::submitButton(Module::t('views', 'Create Account'), ['class' => 'btn btn-primary']); ?>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

</div>