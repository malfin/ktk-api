<?php
/**
 * @author aleksejpuhov
 * File: CustomHttpBearerAuth.php
 * Date: 26.01.2025
 * Time: 17:45
 */

namespace app\filters\auth;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Response;

class MALFINHttpBearerAuth extends HttpBearerAuth
{
    /**
     * Перекрываем метод обработки ошибки авторизации.
     */
    public function handleFailure($response)
    {
        // Перехватываем ошибку авторизации и меняем код ошибки на 403 с кастомным сообщением
        Yii::$app->response->statusCode = 403; // Доступ запрещён
        Yii::$app->response->format = Response::FORMAT_JSON; // Форматируем ответ как JSON
        Yii::$app->response->data = [
            'message' => 'Forbidden for you' // Ваше сообщение
        ];

        // Прерываем дальнейшее выполнение
        return Yii::$app->response->data;
    }
}
