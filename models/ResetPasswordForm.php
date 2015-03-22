<?php
namespace auth\models;

use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{
	public $password;
    public $verifyPassword;

	/**
	 * @var \auth\models\User
	 */
	private $_user;

	/**
	 * Creates a form model given a token.
	 *
	 * @param  string $token
	 * @param  array $config name-value pairs that will be used to initialize the object properties
	 * @throws \yii\base\InvalidParamException if token is empty or not valid
	 */
	public function __construct($token, $config = [])
	{
		if (empty($token) || !is_string($token)) {
            Yii::$app->getSession()->setFlash('invalid', Yii::t('auth.reset-password', 'Wrong password reset token.'));
		}
		$this->_user = User::findByPasswordResetToken($token);
		if (!$this->_user) {
            Yii::$app->getSession()->setFlash('invalid', Yii::t('auth.reset-password', 'Wrong password reset token.'));
		}
		parent::__construct($config);
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['password', 'required'],
			['password', 'string', 'min' => 6],
            ['verifyPassword', 'compare', 'compareAttribute' => 'password'],
		];
	}

	/**
	 * Resets password.
	 *
	 * @return boolean if password was reset.
	 */
	public function resetPassword()
	{
		$user = $this->_user;
		$user->setScenario('resetPassword');
		$user->user_password = $this->password;
		$user->removePasswordResetToken();

		return $user->save();
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'password' => \Yii::t('auth.user', 'Password'),
            'verifyPassword' => \Yii::t('auth.user', 'Verify password'),
		];
	}
}
