<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \auth\models\ResetPasswordForm */

$this->title = \Yii::t('auth.reset-password', 'Reset password');
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
                if(Yii::$app->getSession()->hasFlash('invalid')):
                    ?>
                    <div class="alert alert-danger">
                        <?php echo Yii::$app->getSession()->getFlash('invalid'); ?>
                    </div>
                <?php
                else:
                    ?>
                    <!-- // Form Start -->
                    <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
                    <?= $form->field($model, 'password')->label(\Yii::t('auth.reset-password', 'Please choose your new password'))->passwordInput() ?>
                    <?= $form->field($model, 'verifyPassword')->passwordInput() ?>

                    <?= Html::submitButton('<i></i><span class="strong">'.\Yii::t('auth.reset-password', 'Save').'</span><span>'.\Yii::t('auth.reset-password', 'Become a member').'</span>', ['class' => 'btn btn-icon-stacked btn-block btn-success glyphicons user_add'])?>

                    <?php ActiveForm::end(); ?>
                    <!-- // Form END -->
                <?php endif; ?>
            </div>
            <!-- // Box END -->
        </div>
    </div>
</div>
<!-- // Wrapper END -->