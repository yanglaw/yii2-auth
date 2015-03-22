<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user auth\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['auth/default/reset-password', 'token' => $user->forgot_check_code]);
?>
<p>尊敬的用户：</p>
<br/>
<p>您好！</p>
<p>您提交找回密码请求，请点击下面的链接修改用户 <?= Html::encode($user->user_loginname) ?> 的密码:</p>
<p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
<p>（如果您无法点击这个链接，请将此链接复制到浏览器地址栏后访问）</p>
<p>为了保证您帐号的安全性，该链接有效期为24小时，并且点击一次后将失效!</p>
<p>设置并牢记密码保护问题将更好地保障您的帐号安全。</p>
<p>如果您误收到此电子邮件，则可能是其他用户在尝试帐号设置时的误操作，如果您并未发起该请求，则无需再进行任何操作，并可以放心地忽略此电子邮件。</p>
<p>若您担心帐号安全，建议您立即登录，进入“个人设置”，修改密码。</p>
<p>感谢您使用 <?= Html::encode(\Yii::$app->name) ?> ！</p>
<br/>
<p>宠嗨网</p>
<p>此邮件为自动发送，请勿回复！</p>
