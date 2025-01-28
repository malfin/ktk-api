<?php
/**
 * @author aleksejpuhov
 * File: LogsController.php
 * Date: 26.01.2025
 * Time: 16:48
 */

namespace app\commands;

use yii\console\Controller;
use yii\db\Exception;

class LogsController extends Controller
{
    /**
     * @throws Exception
     */
    public function actionIndex()
    {
        \Yii::$app->db->createCommand()->batchInsert('{{%logs}}', [
            'user_id', 'action', 'details', 'ip_address'
        ], [
            [1, 'Login', 'User logged in', '192.168.1.1'],
            [2, 'Logout', 'User logged out', '192.168.1.2'],
            [3, 'Upload File', 'User uploaded file3.xlsx', '192.168.1.3'],
        ])->execute();

        echo "Logs seeded successfully.\n";
    }
}