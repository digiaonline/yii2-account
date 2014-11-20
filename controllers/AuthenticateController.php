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
use nord\yii\account\models\LoginForm;
use nord\yii\account\Module;
use Yii;
use yii\authclient\AuthAction;
use yii\authclient\ClientInterface;
use yii\filters\AccessControl;

class AuthenticateController extends Controller
{
    /**
     * @var string default action.
     */
    public $defaultAction = 'login';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'captcha' => array_merge(
                ['class' => $this->module->getClassName(Module::CLASS_CAPTCHA_ACTION)],
                $this->module->captchaOptions
            ),
            'client' => [
                'class' => AuthAction::className(),
                'successCallback' => [$this, 'afterAuthSuccess'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => [$this, 'goHome'],
                'rules' => [
                    [
                        'actions' => ['captcha', 'client', 'login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'verbs' => ['POST'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'clientAuth' => [
                'class' => ClientAuthFilter::className(),
                'only' => ['client'],
            ],
        ];
    }

    /**
     * Action that performs user login.
     */
    public function actionLogin()
    {
        $dataContract = $this->module->getDataContract();

        /** @var LoginForm $model */
        $model = $dataContract->createLoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $account = $dataContract->findAccount([$this->module->loginAttribute => $model->username]);

            if ($dataContract->isAccountPasswordExpired($account)) {
                $token = $this->module->generateToken(Module::TOKEN_CHANGE_PASSWORD, $account->id);
                Yii::$app->user->logout();
                return $this->redirect(['/account/password/change', 'token' => $token]);
            } else {
                return $this->goBack();
            }
        } else {
            return $this->render('login', ['model' => $model]);
        }
    }

    /**
     * Action that performs user logout.
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Invoked after a successful authentication with a client.
     *
     * @param ClientInterface $client client instance.
     * @return \yii\web\Response
     */
    public function afterAuthSuccess(ClientInterface $client)
    {
        $attributes = $client->getUserAttributes();
        $name = $client->getId();
        $dataContract = $this->module->getDataContract();
        $provider = $dataContract->findProvider(['name' => $name, 'clientId' => $attributes['id']]);

        if ($provider === null) {
            $provider = $dataContract->createProvider([
                'attributes' => ['name' => $name, 'clientId' => $attributes['id'], 'data' => $attributes]
            ]);
            if (!$provider->save(false)) {
                $this->fatalError();
            }
        }

        if ($provider->account !== null) {
            Yii::$app->user->login($provider->account, Module::getParam(Module::PARAM_LOGIN_EXPIRE_TIME));
            return $this->goHome();
        } else {
            return $this->redirect(['/account/signup/connect', 'providerId' => $provider->id]);
        }
    }
}
