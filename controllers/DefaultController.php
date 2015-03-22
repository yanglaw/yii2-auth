<?php

namespace auth\controllers;

use auth\models\PasswordResetRequestForm;
use auth\models\ResetPasswordForm;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\helpers\Security;
use auth\models\LoginForm;
use auth\models\User;

class DefaultController extends Controller
{
    /**
     * @var \auth\Module
     */
    public $module;

    protected $loginAttemptsVar = '__LoginAttemptsCount';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'backColor' => 0xfafafa,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionLogin()
    {
        // 已经登录，直接跳到首页面
        if (!\Yii::$app->user->isGuest) {
            $this->goHome();
        }

        // 初始化登录表单
        $model = new LoginForm();

        // 用户点击登录按钮，提交数据
        if ($model->load($_POST)) {
            if ($model->inActive()) { // 先判断是否是未激活
                $this->setLoginAttempts($this->getLoginAttempts() + 1);
                Yii::$app->getSession()->setFlash('inactive', Yii::t('auth.user', 'This user is not inactive, please check your email for the activing user link.'));
            } else if ($model->login()) { // 校验成功
                $this->setLoginAttempts(0); // 登录成功，清除登录失败次数
                return $this->goBack(); // 返回用户之前的链接
            } else {
                // 如果登录失败，则失败次数+1
                $this->setLoginAttempts($this->getLoginAttempts() + 1);
            }
        }

        // 如果登录次数过多，则出现验证码
        if ($this->getLoginAttempts() >= $this->module->attemptsBeforeCaptcha) {
            $model->scenario = 'withCaptcha';
        }

        // 显示登录页面
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    // 登录失败次数保存在 session 中
    protected function getLoginAttempts()
    {
        return Yii::$app->getSession()->get($this->loginAttemptsVar, 0);
    }

    protected function setLoginAttempts($value)
    {
        Yii::$app->getSession()->set($this->loginAttemptsVar, $value);
    }

    // 注销操作
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome(); // 返回首页
    }

    // 注册用户
    public function actionSignup()
    {
        $model = new User(); // 初始化用户模型
        $model->setScenario('signup'); // 设置场景为注册
        if ($model->load($_POST) && $model->save()) {
            if ($model->sendSignupEmail($model)) {
                Yii::$app->getSession()->setFlash('signup', Yii::t('auth.user', 'Thank you for your registration. Please check your email.'));
            } else {
                Yii::$app->getSession()->setFlash('signup', Yii::t('auth.user', 'Error on sending email, Please contact the webmaster'));
            }

        }

        // 注册用户表单
        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionRequestPasswordReset()
    {
        $model = new User();
        $model->setScenario('requestPasswordResetToken'); // 设置场景为取回密码
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = User::findOne([
                'user_status' => User::STATUS_ACTIVE,
                'user_email' => $model->user_email,
            ]);
            if ($user) {
                $user->generatePasswordResetToken();
                if($user->update(false)){
                    if ($user->sendRetrieveEmail($user)) {
                        Yii::$app->getSession()->setFlash('requestPasswordResetToken', Yii::t('auth.reset-password', 'Check your email for further instructions.'));
                        // return $this->goHome();
                    } else {
                        Yii::$app->getSession()->setFlash('requestPasswordResetToken', Yii::t('auth.reset-password', 'Sorry, we are unable to reset password for email provided.'));
                    }
                }
            } else {
                Yii::$app->getSession()->setFlash('requestPasswordResetToken', Yii::t('auth.reset-password', 'User is not ACTIVE, please active user first. '));
            }

        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('reset', Yii::t('auth.reset-password', 'New password was saved.'));

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * @param $token
     * @return string|\yii\web\Response
     * @throws BadRequestHttpException
     */
    public function actionActivate()
    {
        $user = User::findOne([
            'user_status' => '0',
            'activation_key' => $_GET['key'],
        ]);
        if ($user) {
            $user->activation_key = null;
            $user->user_status = 1;
            $update = $user->save(false);
            Yii::$app->getSession()->setFlash('active', Yii::t('auth.user', 'User has activation. Please login.'));
            return $this->goHome();
        } else {
            Yii::$app->getSession()->setFlash('expire', Yii::t('auth.user', 'Your activation key had expired, please re-active.'));
            return $this->render('expire');
        }
    }
}
