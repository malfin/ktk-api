<?php
/**
 * @author aleksejpuhov
 * File: LogsController.php
 * Date: 26.01.2025
 * Time: 18:37
 */

namespace app\controllers;

use app\filters\auth\MALFINHttpBearerAuth;
use app\models\Logs;
use Yii;
use yii\filters\Cors;
use yii\rest\Controller;
use yii\web\Response;

class LogsController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        // Аутентификация через Bearer токен
        $behaviors['authenticator'] = [
            'class' => MALFINHttpBearerAuth::class,
        ];


        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => ['http://localhost:3000'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Allow-Credentials' => true,
                // Allow OPTIONS caching
                'Access-Control-Max-Age' => 3600,
                // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
            ],
        ];
        return $behaviors;
    }

    /**
     * Метод для получения логов по user_id и action
     *
     * @return array|Response
     */
    public function actionIndex()
    {
        if (Yii::$app->user->identity->role_id !== 1) {
            return $this->asJson([
                'error' => [
                    'code' => 403,
                    'message' => 'Доступ запрещен. Только администратор может менять роли'
                ]
            ])->setStatusCode(403);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Получаем параметры user_id и action из запроса
        $userId = Yii::$app->request->get('user_id');
        $action = Yii::$app->request->get('action');

        // Получаем логи с фильтрацией
        $logs = Logs::findLogs($userId, $action)->all();

        // Формируем ответ
        $data = array_map(function ($log) {
            return [
                'id' => $log->id,
                'user' => [
                    'id' => $log->user_id,
                    'name' => $log->user->first_name . ' ' . $log->user->last_name,
                    'email' => $log->user->email,
                ],
                'action' => $log->action,
                'details' => $log->details,
                'timestamp' => $log->created_at,
                'ip_address' => $log->ip_address,
            ];
        }, $logs);

        return [
            'data' => $data,
        ];
    }
}