<?php
/**
 * @author aleksejpuhov
 * File: FilesController.php
 * Date: 26.01.2025
 * Time: 16:48
 */

namespace app\commands;

use yii\console\Controller;
use yii\db\Exception;

class FilesController extends Controller
{
    /**
     * @throws Exception
     */
    public function actionIndex()
    {
        \Yii::$app->db->createCommand()->batchInsert('{{%files}}', [
            'user_id', 'session_id', 'file_type', 'file_path', 'status'
        ], [
            [1, 1, 'assignment', '/path/to/file1.pdf', 'pending'],
            [2, 2, 'submission', '/path/to/file2.docx', 'reviewed'],
            [3, 3, 'assignment', '/path/to/file3.xlsx', 'pending'],
        ])->execute();

        echo "Files seeded successfully.\n";
    }
}