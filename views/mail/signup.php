<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user auth\models\User */

$activationLink = Yii::$app->urlManager->createAbsoluteUrl(['auth/default/activate', 'key' => $user->activation_key]);
?>
<p><?= Html::encode($user->user_loginname) ?>，您好！</p>
<br/>
<p>这封信是由 宠嗨网 发送的。</p>
<p>您收到这封邮件，是由于有人在 宠嗨网 注册新用户时使用了这个邮箱地址。如果您并没有访问过 宠嗨网，或没有进行上述操作，请忽略这封邮件。您不需要退订或进行其他进一步的操作。
    <br/>
<p>----------------------------------------------------------------------
<p>新用户注册说明
<p>----------------------------------------------------------------------
    <br/>
<p>如果您是 宠嗨网 的新用户，或在修改您的注册 Email 时使用了本地址，我们需要对您的地址有效性进行验证以避免垃圾邮件或地址被滥用。
<p>您只需点击下面的链接即可进行用户注册，以下链接有效期为3天。过期可以重新请求发送一封新的邮件验证：
<p><?= Html::a(Html::encode($activationLink), $activationLink) ?></p>
<p>（如果上面不是链接形式，请将该地址手工粘贴到浏览器地址栏再访问。）
    <br/>
<p>感谢您的访问，祝您使用愉快！
<p>此致
<p>宠嗨网运营团队
<p><?= Html::a(Html::encode('www.pethigh.com'), 'http://www.pethigh.com') ?></p>
