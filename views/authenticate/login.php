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
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model nord\yii\account\models\LoginForm */

$this->title = Module::t('views', 'Login');
?>
<div class="authenticate-controller login-action">

    <div class="row">
        <div class="col-lg-5">
            <h1><?= Html::encode($this->title); ?></h1>

            <?php if (Module::getInstance()->enableSignup): ?>
                <p class="help-block">
                    <?= Module::t(
                        'views',
                        'If you do not have an account &mdash; {signupLink}.',
                        ['signupLink' => Html::a(Module::t('views', 'Sign up'), ['/account/signup'])]
                    ); ?>
                </p>
            <?php endif; ?>

            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <fieldset>
                <?= $form->field($model, 'username'); ?>
                <?= $form->field($model, 'password')->passwordInput(); ?>
                <?= $form->field($model, 'rememberMe')->checkbox(); ?>
            </fieldset>

            <p class="help-block">
                <?= Module::t(
                    'views',
                    'If you forgot your password you can {link}.',
                    ['link' => Html::a('reset it', ['/account/password/forgot'])]
                ); ?>
            </p>

            <?= Html::submitButton(Module::t('views', 'Login'), ['class' => 'btn btn-primary']); ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>