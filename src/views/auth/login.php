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
use nord\yii\account\widgets\AuthChoice;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model nord\yii\account\models\LoginForm */

$this->title = Module::t('views', 'Login');
?>
<div class="authenticate-controller login-action">

    <div class="row">
        <div class="col-lg-5">

            <h1 class="page-header"><?= Html::encode($this->title); ?></h1>

            <?php if (Module::getInstance()->enableSignup): ?>
                <p class="help-block">
                    <?= Module::t(
                        'views',
                        'If you do not have an account &mdash; {signupLink}',
                        ['signupLink' => Html::a(Module::t('views', 'Sign up'), [Module::URL_ROUTE_SIGNUP], ['id' => 'signup-link'])]
                    ); ?>
                </p>
            <?php endif; ?>

            <p class="help-block">
                <?= Module::t(
                    'views',
                    'Did you forget your password? &mdash; {forgotLink}',
                    ['forgotLink' => Html::a(Module::t('views', 'Recover your password'), [Module::URL_ROUTE_FORGOT_PASSWORD], ['id' => 'forgotpassword-link'])]
                ); ?>
            </p>

            <?php $form = ActiveForm::begin(['id' => 'loginform']); ?>

            <fieldset>
                <?= $form->field($model, 'email'); ?>
                <?= $form->field($model, 'password')->passwordInput(); ?>
                <?= $form->field($model, 'rememberMe')->checkbox(); ?>
            </fieldset>

            <?= Html::submitButton(Module::t('views', 'Login'), ['class' => 'btn btn-lg btn-primary']); ?>

            <?php ActiveForm::end(); ?>

            <?php if (Module::getInstance()->enableClientAuth): ?>
                <hr/>

                <p class="help-block">
                    <?= Module::t('views', 'You may also log in using one of the providers below:'); ?>
                </p>

                <?= AuthChoice::widget(); ?>
            <?php endif; ?>

        </div>
    </div>

</div>
