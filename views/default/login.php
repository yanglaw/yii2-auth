<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var auth\models\LoginForm $model
 */
$this->title = \Yii::t('auth.user', 'Login');
$this->params['breadcrumbs'][] = $this->title;
?>
<!-- Wrapper -->
<div id="login">
    <div class="container">
        <div class="wrapper">
            <h1 class="glyphicons unlock"><?= Html::encode($this->title) ?><i></i></h1>
            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
            ]); ?>
            <!-- Box -->
            <div class="widget widget-heading-simple widget-body-gray">
                <div class="widget-body">
                    <?php
                    if(Yii::$app->getSession()->hasFlash('active')):
                        ?>
                        <div class="alert alert-info">
                            <?php echo Yii::$app->getSession()->getFlash('active'); ?>
                        </div>
                    <?php endif;
                    if(Yii::$app->getSession()->hasFlash('inactive')):?>
                        <div class="alert alert-info">
                            <?php echo Yii::$app->getSession()->getFlash('inactive'); ?>
                        </div>
                    <?php endif;
                    if(Yii::$app->getSession()->hasFlash('reset')):?>
                        <div class="alert alert-info">
                            <?php echo Yii::$app->getSession()->getFlash('reset'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="form">

                        <!-- Form -->
                        <?= $form->field($model, 'username')->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>
                            <?= $form->field($model, 'password')->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>
                        <?php if ($model->scenario == 'withCaptcha'): ?>
                            <?=
                            $form->field($model, 'verifyCode')->widget(Captcha::className(), ['captchaAction' => 'default/captcha', 'options' => ['class' => 'form-control'],]) ?>
                        <?php endif; ?>

                        <div class="separator bottom"></div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="uniformjs">
                                    <label class="checkbox"><?= $form->field($model, 'rememberMe')->checkbox() ?></label>
                                </div>
                            </div>
                            <div class="col-md-4 center">
                                <?= Html::submitButton(\Yii::t('auth.user', 'Login'), ['class' => 'btn btn-primary btn-block']) ?>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div><!-- // Form END -->
                </div>
            </div>
            <!-- // Box END -->
            <div class="innuniformjserT center">
                <a href="/auth/default/request-password-reset" class="btn btn-icon-stacked btn-block btn-success glyphicons circle_question_mark"><i></i><span><?=Html::encode(\Yii::t('auth.user', 'I forgot my account'));?></span><span class="strong"><?=Html::encode(\Yii::t('auth.user', 'Retrieve my password'));?></span></a>
            </div>
        </div>

    </div>

</div>
<!-- // Wrapper END -->
