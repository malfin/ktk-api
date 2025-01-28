<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "logs".
 *
 * @property int $id
 * @property int $user_id
 * @property string $action
 * @property string|null $details
 * @property string $created_at
 * @property string|null $ip_address
 *
 * @property User $user
 */
class Logs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'logs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'action'], 'required'],
            [['user_id'], 'integer'],
            [['details'], 'string'],
            [['created_at'], 'safe'],
            [['action'], 'string', 'max' => 255],
            [['ip_address'], 'string', 'max' => 50],
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
            'action' => Yii::t('app', 'Action'),
            'details' => Yii::t('app', 'Details'),
            'created_at' => Yii::t('app', 'Created At'),
            'ip_address' => Yii::t('app', 'Ip Address'),
        ];
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

    public static function findLogs($userId = null, $action = null)
    {
        $query = self::find();

        if ($userId !== null) {
            $query->andWhere(['user_id' => $userId]);
        }

        if ($action !== null) {
            $query->andWhere(['action' => $action]);
        }

        return $query;
    }
}
