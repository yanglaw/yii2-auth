<?php

namespace auth\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "User".
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $auth_key
 * @property integer $status
 * @property string $last_visit_time
 * @property string $create_time
 * @property string $update_time
 * @property string $delete_time
 *
 * @property ProfileFieldValue $profileFieldValue
 */
class User extends ActiveRecord implements IdentityInterface
{
	const STATUS_DELETED = -1;
	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;
	const STATUS_SUSPENDED = -2;

	/**
	 * @var string the raw password. Used to collect password input and isn't saved in database
	 */
    public $verifyPassword;
    public $verifyEmail;
    public $verifyCode;

	public $password;

	private $_isSuperAdmin = null;

	private $statuses = [
		self::STATUS_DELETED => 'Deleted',
		self::STATUS_INACTIVE => 'Inactive',
		self::STATUS_ACTIVE => 'Active',
		self::STATUS_SUSPENDED => 'Suspended',
	];

	public function behaviors()
	{
		return [
			'timestamp' => [
				'class' => 'yii\behaviors\TimestampBehavior',
				'attributes' => [
					self::EVENT_BEFORE_INSERT => ['create_time', 'update_time'],
                    self::EVENT_BEFORE_UPDATE => 'update_time',
					self::EVENT_BEFORE_DELETE => 'delete_time',
				],
                /*
				'value' => function () {
					return new Expression('CURRENT_TIMESTAMP');
				}*/
			],
		];
	}

	public function getStatus($status = null)
	{
		if ($status === null) {
			return Yii::t('auth.user', $this->statuses[$this->user_status]);
		}
		return Yii::t('auth.user', $this->statuses[$status]);
	}

	/**
	 * Finds an identity by the given ID.
	 *
	 * @param string|integer $id the ID to be looked for
	 * @return IdentityInterface|null the identity object that matches the given ID.
	 */
	public static function findIdentity($user_id)
	{
		return static::findOne($user_id);
	}

	/**
	 * Finds user by username
	 *
	 * @param string $username
	 * @return null|User
	 */
	public static function findByUsername($username)
	{
		return static::find()
					 ->andWhere(['and', ['or', ['user_loginname' => $username], ['user_email' => $username]]])
					 ->one();
	}

    /**
     * Finds user by username
     *
     * @param string $username
     * @return null|User
     */
    public static function findByUsernameInactive($username)
    {
        return static::find()
            ->andWhere(['and', ['or', ['user_loginname' => $username], ['user_email' => $username]], ['user_status' => static::STATUS_INACTIVE]])
            ->one();
    }

	/**
	 * @inheritdoc
	 */
	public static function findIdentityByAccessToken($token, $type = null)
	{
		throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
	}

	/**
	 * Finds user by password reset token
	 *
	 * @param string $token password reset token
	 * @return static|null
	 */
	public static function findByPasswordResetToken($token)
	{
		$expire = Yii::$app->getModule('auth')->passwordResetTokenExpire;
		$parts = explode('_', $token);
		$timestamp = (int)end($parts);
		if ($timestamp + $expire < time()) {
			// token expired
			return null;
		}

		return static::findOne([
			'forgot_check_code' => $token,
			'user_status' => self::STATUS_ACTIVE,
		]);
	}

	/**
	 * @return int|string current user ID
	 */
	public function getId()
	{
		return $this->user_id;
	}

	/**
	 * @return string current user auth key
	 */
	public function getAuthKey()
	{
		return $this->activation_key;
	}

	/**
	 * @param string $authKey
	 * @return boolean if auth key is valid for current user
	 */
	public function validateAuthKey($authKey)
	{
		return $this->activation_key === $authKey;
	}

	/**
	 * @param string $password password to validate
	 * @return bool if password provided is valid for current user
	 */
	public function validatePassword($password)
	{
		return Yii::$app->getSecurity()->validatePassword($password, $this->user_password);
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return Yii::$app->getModule('auth')->tableMap['User'];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['user_status', 'default', 'value' => static::STATUS_ACTIVE, 'on' => 'signup'],
			['user_status', 'safe'],

			['user_loginname', 'filter', 'filter' => 'trim'],
			['user_loginname', 'required'],
			['user_loginname', 'unique', 'message' => Yii::t('auth.user', 'This username has already been taken.')],
			['user_loginname', 'string', 'min' => 4, 'max' => 40, 'message'=> Yii::t('auth.user', 'Incorrect username (length between 4 and 20 characters).')],

			['user_email', 'filter', 'filter' => 'trim'],
			['user_email', 'required'],
			['user_email', 'email'],
			['user_email', 'unique', 'message' => Yii::t('auth.user', 'This email address has already been taken.'), 'on' => 'signup'],
			['user_email', 'exist', 'message' => Yii::t('auth.reset-password', 'There is no user with such email.'), 'on' => 'requestPasswordResetToken'],

			['user_password', 'required', 'on' => 'signup'],
			['user_password', 'string', 'min' => 6, 'max' => 128, 'message'=> Yii::t('auth.user', 'Incorrect password (minimal length 6 symbols).')],

            ['verifyPassword', 'compare', 'compareAttribute' => 'user_password', 'on' => 'signup'],
            ['verifyEmail', 'compare', 'compareAttribute' => 'user_email', 'on' => 'signup'],
            ['verifyCode', 'captcha', 'captchaAction' => 'auth/default/captcha'],
		];
	}

	public function scenarios()
	{
		return [
			'signup' => ['user_loginname', 'user_email', 'user_password', 'verifyPassword', 'verifyEmail', 'verifyCode'],
			'profile' => ['username', 'email', 'password'],
			'resetPassword' => ['user_password', 'verifyPassword'],
            'requestPasswordResetToken' => ['user_email', 'verifyCode'],
			'login' => ['login_time'],
		] + parent::scenarios();
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'user_loginname' => Yii::t('auth.user', 'Username'),
			'user_email' => Yii::t('auth.user', 'Email'),
			'user_password' => Yii::t('auth.user', 'Password'),
            'verifyPassword' => Yii::t('auth.user', 'Verify password'),
            'verifyEmail' => Yii::t('auth.user', 'Verify email'),
            'verifyCode' => Yii::t('auth.user', 'Verify Code'),
			'password_hash' => Yii::t('auth.user', 'Password Hash'),
			'password_reset_token' => Yii::t('auth.user', 'Password Reset Token'),
			'activation_key' => Yii::t('auth.user', 'Auth Key'),
			'user_status' => Yii::t('auth.user', 'Status'),
			'login_time' => Yii::t('auth.user', 'Last Visit Time'),
			'create_time' => Yii::t('auth.user', 'Create Time'),
			'update_time' => Yii::t('auth.user', 'Update Time'),
			'delete_time' => Yii::t('auth.user', 'Delete Time'),
		];
	}

	/**
	 * @return \yii\db\ActiveRelation
	 */
	public function getProfileFieldValue()
	{
		return $this->hasOne(ProfileFieldValue::className(), ['id' => 'user_id']);
	}

	public function beforeSave($insert)
	{
		if (parent::beforeSave($insert)) {
			if (($this->isNewRecord || in_array($this->getScenario(), ['resetPassword', 'profile'])) && !empty($this->user_password)) {
				$this->user_password = Yii::$app->getSecurity()->generatePasswordHash($this->user_password);
			}
			if ($this->isNewRecord) {
				$this->activation_key = Yii::$app->getSecurity()->generateRandomString();
			}
			if ($this->getScenario() !== \yii\web\User::EVENT_AFTER_LOGIN) {
				$this->setAttribute('update_time', new Expression('CURRENT_TIMESTAMP'));
			}

			return true;
		}
		return false;
	}

	public function delete()
	{
		$db = static::getDb();
		$transaction = $this->isTransactional(self::OP_DELETE) && $db->getTransaction() === null ? $db->beginTransaction() : null;
		try {
			$result = false;
			if ($this->beforeDelete()) {
				$this->setAttribute('user_status', static::STATUS_DELETED);
				$this->save(false);
			}
			if ($transaction !== null) {
				if ($result === false) {
					$transaction->rollback();
				} else {
					$transaction->commit();
				}
			}
		} catch (\Exception $e) {
			if ($transaction !== null) {
				$transaction->rollback();
			}
			throw $e;
		}
		return $result;
	}

	/**
	 * Returns whether the logged in user is an administrator.
	 *
	 * @return boolean the result.
	 */
	public function getIsSuperAdmin()
	{
		if ($this->_isSuperAdmin !== null) {
			return $this->_isSuperAdmin;
		}

		$this->_isSuperAdmin = in_array($this->user_loginname, Yii::$app->getModule('auth')->superAdmins);
		return $this->_isSuperAdmin;
	}

	public function login($duration = 0)
	{
		return Yii::$app->user->login($this, $duration);
	}

	/**
	 * Generates new password reset token
	 */
	public function generatePasswordResetToken()
	{
		$this->forgot_check_code = Yii::$app->security->generateRandomString() . '_' . time();
	}

	/**
	 * Removes password reset token
	 */
	public function removePasswordResetToken()
	{
		$this->forgot_check_code = null;
	}

    public function sendRetrieveEmail($user)
    {
        return \Yii::$app->mailer->compose('@auth/views/mail/passwordResetToken', ['user' => $user])
            ->setFrom([\Yii::$app->getModule('auth')->supportEmail => \Yii::$app->name])
            ->setTo($this->user_email)
            ->setSubject(Yii::t('auth.reset-password', 'Password reset for {name}', ['name' => \Yii::$app->name]))
            ->send();
    }

    public function sendSignupEmail($user)
    {
        return \Yii::$app->mailer->compose('@auth/views/mail/signup', ['user' => $user])
            ->setFrom([\Yii::$app->getModule('auth')->supportEmail => \Yii::$app->name])
            ->setTo($this->user_email)
            ->setSubject(Yii::t('auth.user', '[{name}] congratulation! {user_name} has successfully sign up!', ['name' => \Yii::$app->name, 'user_name' => $user->user_loginname]))
            ->send();
    }

    /**
     * 获取用户数量，包含所有用户数量。
     */
    public static function getUsersCount(){
        $count =  User::find()->count();
        return $count;
    }
}
