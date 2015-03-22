<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;

$this->title = \Yii::t('auth.user', 'Sign up');

?>
<!-- Wrapper -->
<div id="login">
    <div class="wrapper signup">
        <h1 class="glyphicons user_add"><?= Html::encode($this->title) ?> <i></i></h1>
        <!-- Box -->
        <div class="widget widget-heading-simple">
            <div class="widget-body">
                <?php
                    if(Yii::$app->getSession()->hasFlash('signup')):
                    ?>
                <div class="success">
                    <?php echo Yii::$app->getSession()->getFlash('signup'); ?>
                </div>
                <?php
                    else:
                ?>

                <!-- // Form Start -->
                <?php $form = ActiveForm::begin([
                    'id' => 'registration-form',
                ]); ?>
                <div class="innerR">
                    <?= $form->field($model, 'user_loginname') ?>
                    <?= $form->field($model, 'user_password')->passwordInput() ?>
                    <?= $form->field($model, 'verifyPassword')->passwordInput() ?>
                    <?= $form->field($model, 'user_email') ?>
                    <?= $form->field($model, 'verifyEmail') ?>
                    <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), ['captchaAction' => 'default/captcha', 'options' => ['class' => 'form-control'],]) ?>
                    <?= Html::submitButton('<i></i><span class="strong">'.\Yii::t('auth.user', 'Sign up').'</span><span>成为宠嗨网会员</span>', ['class' => 'btn btn-icon-stacked btn-block btn-success glyphicons user_add'])?>
                    <p><?= Html::a(\Yii::t('auth.user', 'Already registered? Sign in!'), ['/auth/default/login']) ?></p>
                </div>
                <?php ActiveForm::end(); ?>
                <!-- // Form END -->
                    <?php endif; ?>
            </div>
            <!-- // Box END -->
        </div>
    </div>
</div>
<!-- // Wrapper END -->