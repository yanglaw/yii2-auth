<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \auth\models\PasswordResetRequestForm */

$this->title = Yii::t('auth.user', 'Activation key expired');
$this->params['breadcrumbs'][] = $this->title;
?>
<!-- Wrapper -->
<div id="login">
    <div class="wrapper signup">
        <h1 class="glyphicons circle_remove"><?= Html::encode($this->title) ?> <i></i></h1>
        <!-- Box -->
        <div class="widget widget-heading-simple">
            <div class="widget-body">
                <?php
                if(Yii::$app->getSession()->hasFlash('expire')):
                    ?>
                    <div class="alert alert-danger">
                        <?php echo Yii::$app->getSession()->getFlash('expire'); ?>
                    </div>
                <?php endif; ?>
            </div>
            <!-- // Box END -->

        </div>
        <div class="innuniformjserT center">
            <a href="/auth/default/request-password-reset" class="btn btn-icon-stacked btn-block btn-success glyphicons retweet"><i></i><span><?=Html::encode(\Yii::t('auth.user', 'Activation key expired'));?></span><span class="strong"><?=Html::encode(\Yii::t('auth.user', 'Re-activation'));?></span></a>
        </div>
    </div>
</div>
<!-- // Wrapper END -->