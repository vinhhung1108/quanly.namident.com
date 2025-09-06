<?php

namespace app\models;

use Yii;

use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\helpers\Security;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $fist_name
 * @property string $last_name
 * @property string $email
 * @property int $phone_number
 * @property string $authKey
 * @property string $accessToken
 * @property int $level
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{

	public $new_password;
	public $repeat_password;

    const SCENARIO_CHANGEPASSWORD = 'changePwd';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['phone_number'], 'integer'],
            [['username', 'password', 'fist_name', 'last_name','role'], 'string', 'max' => 30],
            [['email'], 'string', 'max' => 80],
            [['auth_key'], 'string', 'max' => 50],
            [['access_token', 'password_reset_token'], 'string', 'max' => 200],
            [['username'], 'unique'],
            [['new_password', 'repeat_password'], 'required', 'on' => self::SCENARIO_CHANGEPASSWORD],
            [['repeat_password'], 'compare', 'compareAttribute'=>'new_password', 'on'=> self::SCENARIO_CHANGEPASSWORD],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'fist_name' => 'Fist Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'phone_number' => 'Phone Number',
            'auth_key' => 'Auth Key',
            'access_token' => 'Access Token',
			'password_reset_token' => 'PW reset Token',
            'role' => 'Role',
        ];
    }

    /**
     * {@inheritdoc}
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }
	
	/**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
         return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
       return static::findOne(['access_token' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
       return static::findOne(['username' => $username]);
    }
	
	/**
     * Finds user by password reset token
     *
     * @param  string      $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        $expire = \Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        if ($timestamp + $expire < time()) {
            // token expired
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function getId()
    {
         return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        // return $this->authKey;
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === $password;
    }
	
	/**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Security::generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Security::generateRandomKey();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Security::generateRandomKey() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    /** EXTENSION MOVIE **/
		
}
