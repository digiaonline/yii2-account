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
use nord\yii\account\filters\ClientAuthFilter;
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
            [
                'class' => SignupFilter::className(),
                'only' => ['index'],
            ],
            [
                'class' => AccessControl::className(),
                'denyCallback' => [$this, 'goHome'],
                'rules' => [
                    [
                        'actions' => ['activate', 'connect', 'index'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            [
                'class' => TokenFilter::className(),
                'only' => ['activate'],
            ],
            [
                'class' => ClientAuthFilter::className(),
                'only' => ['connect'],
            ]
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
            $this->afterSignup();

            $account = $dataContract->findAccount(['username' => $model->username]);

            if ($this->module->enableActivation) {
                $this->sendActivationMail($account);
                return $this->redirect([Module::URL_ROUTE_SIGNUP_DONE]);
            } else {
                $dataContract->activateAccount($account);
                return $this->redirect([Module::URL_ROUTE_LOGIN]);
            }
        } else {
            return $this->render('index', [
                'model' => $model,
                'captchaClass' => $this->module->getClassName(Module::CLASS_CAPTCHA),
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

        $this->redirect([Module::URL_ROUTE_LOGIN]);
    }

    /**
     * Displays the 'connect' page.
     *
     * @param string $providerId provider identifier.
     * @return string|\yii\web\Response
     */
    public function actionConnect($providerId)
    {
        $dataContract = $this->module->getDataContract();
        $provider = $dataContract->findProvider($providerId);

        if ($provider === null || !empty($provider->accountId)) {
            $this->pageNotFound();
        }

        $model = $dataContract->createConnectForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $account = $dataContract->createAccount([
                'attributes' => [
                    'username' => $model->username,
                    'password' => $this->module->getTokenGenerator()->generate(),
                    'email' => $model->email
                ],
            ]);

            if (!$account->save()) {
                $this->fatalError();
            }

            $provider->updateAttributes(['accountId' => $account->id]);
            $this->afterSignup();
            Yii::$app->user->login($account, Module::getParam(Module::PARAM_LOGIN_EXPIRE_TIME));
            return $this->goBack();
        } else {
            return $this->render('connect', ['model' => $model, 'provider' => $provider]);
        }
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
        $actionUrl = $this->module->createUrl([Module::URL_ROUTE_ACTIVATE, 'token' => $token], true);
        $this->module->getMailSender()->sendActivationMail([
            'to' => [$account->email],
            'from' => $this->module->getParam(Module::PARAM_FROM_EMAIL_ADDRESS),
            'subject' => Module::t('email', 'Thank you for signing up'),
            'data' => ['actionUrl' => $actionUrl],
        ]);
    }
}
