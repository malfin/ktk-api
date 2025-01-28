<?php
/**
 * @author aleksejpuhov
 * File: SessionsController.php
 * Date: 26.01.2025
 * Time: 16:46
 */

namespace app\commands;

use yii\console\Controller;
use yii\db\Expression;

class SessionsController extends Controller
{
    public function actionIndex()
    {
        \Yii::$app->db->createCommand()->batchInsert('{{%sessions}}', [
            'title', 'description', 'type_id', 'start_date', 'end_date', 'max_participants'
        ], [
            ['Session 1', 'Description for session 1', 1, new Expression('NOW()'), new Expression('NOW() + INTERVAL 1 DAY'), 50],
            ['Session 2', 'Description for session 2', 2, new Expression('NOW() + INTERVAL 1 DAY'), new Expression('NOW() + INTERVAL 2 DAY'), 30],
            ['Session 3', 'Description for session 3', 3, new Expression('NOW() + INTERVAL 2 DAY'), new Expression('NOW() + INTERVAL 3 DAY'), 20],
        ])->execute();

        echo "Sessions seeded successfully.\n";
    }
}