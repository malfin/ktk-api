<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_sessions".
 *
 * @property int $id
 * @property int $user_id
 * @property int $session_id
 * @property string|null $status
 * @property string $registered_at
 *
 * @property Sessions $session
 * @property User $user
 */
class UserSessions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_sessions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'session_id'], 'required'],
            [['user_id', 'session_id'], 'integer'],
            [['status'], 'string'],
            [['registered_at'], 'safe'],
            [['session_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sessions::class, 'targetAttribute' => ['session_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'session_id' => Yii::t('app', 'Session ID'),
            'status' => Yii::t('app', 'Status'),
            'registered_at' => Yii::t('app', 'Registered At'),
        ];
    }

    /**
     * Gets query for [[Session]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSession()
    {
        return $this->hasOne(Sessions::class, ['id' => 'session_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
