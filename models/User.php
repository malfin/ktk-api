<?php


namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $patronymic
 * @property string $email
 * @property string $password_hash
 * @property int $role_id
 * @property string|null $birth_date
 * @property string|null $auth_key
 * @property string|null $access_token
 * @property string|null $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Files[] $files
 * @property Logs[] $logs
 * @property Roles $role
 * @property UserSessions[] $userSessions
 */
class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return '{{%users}}';
    }

    public function rules()
    {
        return [
            [['first_name', 'last_name', 'email', 'password_hash', 'birth_date'], 'required'],
            [['first_name', 'last_name', 'patronymic'], 'string', 'max' => 100],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['password_hash'], 'string', 'min' => 6],
            [['auth_key', 'access_token'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => ['active', 'inactive']],
            [['role_id'], 'integer'],
            [['birth_date'], 'date', 'format' => 'php:Y-m-d'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'patronymic' => 'Отчество',
            'email' => 'Email',
            'password_hash' => 'Пароль',
            'role_id' => 'Роль',
            'birth_date' => 'Дата рождения',
            'auth_key' => 'Ключ авторизации',
            'access_token' => 'Токен',
            'status' => 'Статус',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Поиск пользователя по ID
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Поиск пользователя по токену
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Получить ID пользователя
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Получить ключ аутентификации
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Проверить ключ аутентификации
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Проверить пароль
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Установить пароль (хеш)
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Генерация ключа аутентификации
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Генерация токена доступа
     */
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    /**
     * Gets query for [[Files]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(Files::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Logs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLogs()
    {
        return $this->hasMany(Logs::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Role]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Roles::class, ['id' => 'role_id']);
    }

    /**
     * Gets query for [[UserSessions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserSessions()
    {
        return $this->hasMany(UserSessions::class, ['user_id' => 'id']);
    }

    public function setLogs($action, $details)
    {
        $logs = new Logs();
        $logs->user_id = Yii::$app->user->id;
        $logs->action = $action;
        $logs->details = $details;
        $logs->ip_address = Yii::$app->request->getUserIP();
        $logs->save();
    }
}
