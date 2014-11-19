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
use yii\authclient\widgets\AuthChoice;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model nord\yii\account\models\SignupForm */
/* @var $captchaClass yii\captcha\Captcha */

$this->title = Module::t('views', 'Sign up');
?>
<div class="register-controller index-action">

    <div class="row">
        <div class="col-lg-5">

            <h1 class="page-header"><?= Html::encode($this->title); ?></h1>

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
                <?php if (Module::getInstance()->enableCaptcha): ?>
                    <?= $form->field($model, 'captcha')->widget($captchaClass, [
                        'captchaAction' => '/account/authenticate/captcha',
                        'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-9">{input}</div></div>',
                        'imageOptions' => ['height' => 35],
                    ]); ?>

                    <p class="help-block">
                        <?= Module::t(
                            'views',
                            'Please enter the verification code in the field above.'
                        ); ?>
                    </p>
                <?php endif; ?>
            </fieldset>

            <?= Html::submitButton(Module::t('views', 'Create Account'), ['class' => 'btn btn-lg btn-primary']); ?>

            <?php ActiveForm::end(); ?>

            <?php if (Module::getInstance()->enableClientAuth): ?>
                <hr/>

                <p class="help-block">
                    <?= Module::t('views', 'You may also sign up using one of the providers below:'); ?>
                </p>

                <?= AuthChoice::widget(['baseAuthUrl' => ['/account/authenticate/client']]); ?>
            <?php endif; ?>

        </div>
    </div>

</div>