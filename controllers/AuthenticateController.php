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

use nord\yii\account\models\LoginForm;
use nord\yii\account\Module;
use Yii;
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
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => [$this, 'goHome'],
                'rules' => [
                    [
                        'actions' => ['login'],
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

            if ($account->requireNewPassword) {
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
}
