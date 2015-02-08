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
                    'If you already have an account &mdash; {loginLink}.',
                    ['loginLink' => Html::a(Module::t('views', 'Login'), [Module::URL_ROUTE_LOGIN])]
                ); ?>
            </p>

            <?php $form = ActiveForm::begin(['id' => 'signupform']); ?>

            <fieldset>
                <?= $form->field($model, 'email'); ?>
                <?= $form->field($model, 'username'); ?>
                <?= $form->field($model, 'password')->passwordInput(); ?>
                <?= $form->field($model, 'verifyPassword')->passwordInput(); ?>
                <?php if (Module::getInstance()->enableCaptcha): ?>
                    <?= $form->field($model, 'captcha')->widget($captchaClass, [
                        'captchaAction' => Module::URL_ROUTE_CAPTCHA,
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

        </div>
    </div>

</div>
