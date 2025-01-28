<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "files".
 *
 * @property int $id
 * @property int $user_id
 * @property int $session_id
 * @property string $file_type
 * @property string $file_path
 * @property string $uploaded_at
 * @property string|null $status
 *
 * @property Sessions $session
 * @property User $user
 */
class Files extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'session_id', 'file_type', 'file_path'], 'required'],
            [['user_id', 'session_id'], 'integer'],
            [['file_type', 'status'], 'string'],
            [['uploaded_at'], 'safe'],
            [['file_path'], 'string', 'max' => 255],
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
            'file_type' => Yii::t('app', 'File Type'),
            'file_path' => Yii::t('app', 'File Path'),
            'uploaded_at' => Yii::t('app', 'Uploaded At'),
            'status' => Yii::t('app', 'Status'),
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

    public function setLogs($action,$details){
        $logs = new Logs();
        $logs->user_id = Yii::$app->user->id;
        $logs->action = $action;
        $logs->details = $details;
        $logs->ip_address = Yii::$app->request->getUserIP();
        $logs->save();
    }
}
