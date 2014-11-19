<?php
/*
 * This file is part of Account.
 *
 * (c) 2014 Nord Software
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nord\yii\account\controllers;

use nord\yii\account\filters\TokenFilter;
use nord\yii\account\models\PasswordForm;
use nord\yii\account\Module;
use Yii;
use yii\db\ActiveRecord;
use yii\filters\AccessControl;

class PasswordController extends Controller
{
    /**
     * @var string default action.
     */
    public $defaultAction = 'forgot';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['change', 'forgot', 'forgotDone', 'reset'],
                        'allow' => true,
                        'roles' => ['?'],

                    ],
                ],
            ],
            'token' => [
                'class' => TokenFilter::className(),
                'only' => ['change', 'reset'],
            ],
        ];
    }

    /**
     * Displays the 'change password' page.
     *
     * @param string $token authentication token.
     */
    public function actionChange($token)
    {
        $tokenModel = $this->loadToken(Module::TOKEN_CHANGE_PASSWORD, $token);
        $dataContract = $this->module->getDataContract();

        /** @var PasswordForm $model */
        $model = $dataContract->createPasswordForm(['scenario' => 'change']);
        $model->account = $dataContract->findAccount($tokenModel->accountId);

        if ($model->load(Yii::$app->request->post()) && $model->changePassword()) {
            $dataContract->useToken($tokenModel);
            return $this->redirect(['/account/authenticate/login']);
        } else {
            return $this->render('change', [
                'model' => $model,
                'title' => Module::t('views', 'Change password'),
                'reason' => Module::t('views', 'Your password has expired.'),
            ]);
        }
    }

    /**
     * Displays the 'forgot password' page.
     */
    public function actionForgot()
    {
        $dataContract = $this->module->getDataContract();
        $model = $dataContract->createForgotPasswordForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $account = $dataContract->findAccount(['email' => $model->email]);
            $this->sendResetPasswordMail($account);
            return $this->redirect('forgotDone');
        } else {
            return $this->render('forgot', ['model' => $model]);
        }
    }

    /**
     * Displays the page after submitting the 'forgot password' from successfully.
     */
    public function actionForgotDone()
    {
        return $this->render('forgotDone');
    }

    /**
     * Displays the 'reset password' page.
     *
     * @param string $token authentication token.
     */
    public function actionReset($token)
    {
        $tokenModel = $this->loadToken(Module::TOKEN_RESET_PASSWORD, $token);
        $dataContract = $this->module->getDataContract();

        /** @var PasswordForm $model */
        $model = $dataContract->createPasswordForm(['scenario' => 'change']);
        $model->account = $dataContract->findAccount($tokenModel->accountId);

        if ($model->load(Yii::$app->request->post()) && $model->changePassword()) {
            $dataContract->useToken($tokenModel);
            return $this->redirect(['/account/authenticate/login']);
        } else {
            return $this->render('change', [
                'model' => $model,
                'title' => Module::t('views', 'Reset password'),
                'reason' => Module::t('views', 'You have requested to reset your password.'),
            ]);
        }
    }

    /**
     * Sends a reset password email to owner of the given account.
     *
     * @param ActiveRecord $account model instance.
     */
    protected function sendResetPasswordMail(ActiveRecord $account)
    {
        $token = $this->module->generateToken(Module::TOKEN_RESET_PASSWORD, $account->id);
        $actionUrl = Yii::$app->getUrlManager()->createAbsoluteUrl(['/account/password/reset', 'token' => $token]);

        $this->module->getMailSender()->sendResetPasswordMail([
            'to' => [$account->email],
            'from' => $this->module->getParam(Module::PARAM_FROM_EMAIL_ADDRESS),
            'subject' => Module::t('email', 'Reset password'),
            'data' => ['actionUrl' => $actionUrl],
        ]);
    }
}
