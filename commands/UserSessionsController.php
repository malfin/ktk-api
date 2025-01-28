<?php
/**
 * @author aleksejpuhov
 * File: UserSessionsController.php
 * Date: 26.01.2025
 * Time: 16:47
 */

namespace app\commands;

use yii\console\Controller;
use yii\db\Exception;

class UserSessionsController extends Controller
{
    /**
     * @throws Exception
     */
    public function actionIndex()
    {
        \Yii::$app->db->createCommand()->batchInsert('{{%user_sessions}}', [
            'user_id', 'session_id', 'status'
        ], [
            [1, 1, 'approved'],
            [2, 2, 'pending'],
            [3, 3, 'canceled'],
        ])->execute();

        echo "User sessions seeded successfully.\n";
    }
}