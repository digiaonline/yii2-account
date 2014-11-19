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

use nord\yii\account\components\datacontract\DataContract;
use nord\yii\account\filters\SignupFilter;
use nord\yii\account\filters\TokenFilter;
use nord\yii\account\models\SignupForm;
use nord\yii\account\Module;
use Yii;
use yii\db\ActiveRecord;
use yii\filters\AccessControl;

class SignupController extends Controller
{
    // Event types.
    const EVENT_AFTER_ACTIVATION = 'afterActivation';
    const EVENT_AFTER_SIGNUP = 'afterSignup';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'signup' => [
                'class' => SignupFilter::className(),
            ],
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => [$this, 'goHome'],
                'rules' => [
                    [
                        'actions' => ['activate', 'index'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'token' => [
                'class' => TokenFilter::className(),
                'only' => ['activate'],
            ],
        ];
    }

    /**
     * Displays the sign up page.
     */
    public function actionIndex()
    {
        $scenario = $this->module->enableCaptcha ? 'captcha' : 'default';
        $dataContract = $this->module->getDataContract();

        /** @var SignupForm $model */
        $model = $dataContract->createSignupForm(['scenario' => $scenario]);

        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            $account = $dataContract->findAccount(['username' => $model->username]);

            $this->afterSignup();

            if ($this->module->enableActivation) {
                $this->sendActivationMail($account);
                return $this->redirect(['done']);
            } else {
                $dataContract->activateAccount($account);
                return $this->redirect(['/account/authenticate/login']);
            }
        } else {
            return $this->render('index', [
                'model' => $model,
                'captchaClass' => $dataContract->getClassName(DataContract::CLASS_CAPTCHA),
            ]);
        }
    }

    /**
     * Activates an account.
     *
     * @param string $token authentication token.
     */
    public function actionActivate($token)
    {
        $tokenModel = $this->loadToken(Module::TOKEN_ACTIVATE, $token);

        $dataContract = $this->module->getDataContract();
        $account = $dataContract->findAccount($tokenModel->accountId);

        if ($account === null) {
            $this->pageNotFound();
        }

        $dataContract->activateAccount($account);
        $dataContract->useToken($tokenModel);

        $this->afterActivate();

        $this->redirect(['/account/authenticate/login']);
    }

    /**
     * Triggers the 'after signup' event.
     */
    public function afterSignup()
    {
        $this->trigger(self::EVENT_AFTER_SIGNUP);
    }

    /**
     * Triggers the 'after activate' event.
     */
    public function afterActivate()
    {
        $this->trigger(self::EVENT_AFTER_ACTIVATION);
    }

    /**
     * Displays the sign up done page.
     */
    public function actionDone()
    {
        return $this->render('done');
    }

    /**
     * Sends an activation email to owner of the given account.
     *
     * @param ActiveRecord $account account instance.
     */
    protected function sendActivationMail(ActiveRecord $account)
    {
        $token = $this->module->generateToken(Module::TOKEN_ACTIVATE, $account->id);
        $actionUrl = Yii::$app->getUrlManager()->createAbsoluteUrl(['/account/signup/activate', 'token' => $token]);

        $this->module->getMailSender()->sendActivationMail([
            'to' => [$account->email],
            'from' => $this->module->getParam(Module::PARAM_FROM_EMAIL_ADDRESS),
            'subject' => Module::t('email', 'Thank you for signing up'),
            'data' => ['actionUrl' => $actionUrl],
        ]);
    }
}
