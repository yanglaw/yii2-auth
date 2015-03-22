<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \auth\models\PasswordResetRequestForm */

$this->title = Yii::t('auth.reset-password', 'Request password reset');
$this->params['breadcrumbs'][] = $this->title;
?>
<!-- Wrapper -->
<div id="login">
    <div class="wrapper signup">
        <h1 class="glyphicons refresh"><?= Html::encode($this->title) ?> <i></i></h1>
        <!-- Box -->
        <div class="widget widget-heading-simple">
            <div class="widget-body">
                <?php
                if(Yii::$app->getSession()->hasFlash('requestPasswordResetToken')):
                    ?>
                    <div class="alert alert-info">
                        <?php echo Yii::$app->getSession()->getFlash('requestPasswordResetToken'); ?>
                    </div>
                <?php
                else:
                    ?>
                    <!-- // Form Start -->
                    <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

                    <h5><?= Yii::t('auth.reset-password', 'Please fill out your email. A link to reset password will be sent there.') ?></h5>
                    <div class="separator bottom"></div>
                    <?= $form->field($model, 'user_email') ?>

                    <?= $form->field($model, 'verifyCode')->widget(\yii\captcha\Captcha::className(), ['captchaAction' => 'default/captcha', 'options' => ['class' => 'form-control'],]) ?>
                    <?= Html::submitButton('<i></i><span class="strong">'.\Yii::t('auth.reset-password', 'Send').'</span><span>'.\Yii::t('auth.reset-password', 'Become a member').'</span>', ['class' => 'btn btn-icon-stacked btn-block btn-success glyphicons user_add'])?>

                    <?php ActiveForm::end(); ?>
                    <!-- // Form END -->
                <?php endif; ?>
            </div>
            <!-- // Box END -->
        </div>
    </div>
</div>
<!-- // Wrapper END -->