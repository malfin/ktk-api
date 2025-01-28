<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sessions".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property int $type_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int|null $max_participants
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Files[] $files
 * @property SessionTypes $type
 * @property UserSessions[] $userSessions
 */
class Sessions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sessions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'type_id', 'start_date','description','end_date','max_participants'], 'required'],
            [['description'], 'string'],
            [['type_id', 'max_participants'], 'integer'],
            [['start_date', 'end_date', 'created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => SessionTypes::class, 'targetAttribute' => ['type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'type_id' => Yii::t('app', 'Type ID'),
            'start_date' => Yii::t('app', 'Start Date'),
            'end_date' => Yii::t('app', 'End Date'),
            'max_participants' => Yii::t('app', 'Max Participants'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Files]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(Files::class, ['session_id' => 'id']);
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(SessionTypes::class, ['id' => 'type_id']);
    }

    /**
     * Gets query for [[UserSessions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserSessions()
    {
        return $this->hasMany(UserSessions::class, ['session_id' => 'id']);
    }

    public function setLogs($action){
        $logs = new Logs();
        $logs->user_id = Yii::$app->user->id;
        $logs->action = $action;
        $logs->details = $this->title;
        $logs->ip_address = Yii::$app->request->getUserIP();
        $logs->save();
    }
}
