<?php
/**
 * @author aleksejpuhov
 * File: UserController.php
 * Date: 26.01.2025
 * Time: 15:59
 */

namespace app\controllers;

use app\filters\auth\MALFINHttpBearerAuth;
use app\models\User;
use Yii;
use yii\rest\Controller;

class AuthController extends Controller
{

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => MALFINHttpBearerAuth::class,
            'only' => ['logout'], // Применяется только для actionLogout
        ];

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => ['http://*'],
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


    // Регистрация

    public function actionRegistration(): array
    {
        $data = Yii::$app->request->post();
        $user = new User();

        // Загружаем данные в модель
        if ($user->load($data, '') && $user->validate()) {
            // Если валидация прошла успешно
            $user->setPassword($data['password_hash']);
            $user->role_id = 2;
            $user->generateAccessToken();

            if ($user->save()) {
                Yii::$app->response->statusCode = 201;
                $user->setLogs('Регистрация', 'Успешно!');
                return [
                    'data' => [
                        'user' => [
                            'name' => "{$user->last_name} {$user->first_name} {$user->patronymic}",
                            'email' => $user->email,
                        ],
                        'code' => 201,
                        'message' => 'Пользователь создан',
                    ],
                ];
            }
        }
        // Если валидация не прошла, собираем ошибки
        $errors = [];
        foreach ($user->errors as $field => $messages) {
            $errors[$field] = $messages;
        }

        if (empty($errors)) {
            $errors = [
                'first_name' => ['Поле Имя обязательно для заполнения.'],
                'last_name' => ['Поле Фамилия обязательно для заполнения.'],
                'email' => ['Поле Email обязательно для заполнения.'],
                'password' => ['Поле Пароль обязательно для заполнения.'],
                'birth_date' => ['Поле Дата рождения обязательно для заполнения.'],
            ];
        }

        // Формируем ответ с ошибками
        return [
            'error' => [
                'code' => 422,
                'message' => 'Ошибка валидации',
                'errors' => $errors,
            ],
        ];
    }

    // Авторизация

    public function actionAuthorization(): array
    {
        // Получаем данные из запроса
        $data = Yii::$app->request->post();

        if (!isset($data['email']) || !isset($data['password'])) {
            return [
                'error' => [
                    'code' => 422,
                    'message' => 'Ошибка валидации',
                    'errors' => [
                        'email' => ['Поле email обязательно для заполнения.'],
                        'password' => ['Поле password обязательно для заполнения.'],
                    ],
                ],
            ];
        }

        $user = User::findOne(['email' => $data['email']]);

        if (!$user) {
            Yii::$app->response->statusCode = 401;
            return [

                'error' => [
                    'code' => 401,
                    'message' => 'Неправильный логин или пароль',
                ],
            ];
        }

        // Проверяем, правильный ли пароль
        if (!$user->validatePassword($data['password'])) {
            Yii::$app->response->statusCode = 401;
            $user->setLogs('Авторизация', 'Неправильный логин или пароль');
            return [
                'error' => [
                    'code' => 401,
                    'message' => 'Неправильный логин или пароль',
                ],
            ];
        }

        // Генерация токена для сессии
        $user->generateAccessToken();
        $user->save();

        Yii::$app->response->statusCode = 200;
        $user->setLogs('Авторизация', 'Пользователь успешно вошёл в аккаунт!');
        // Возвращаем успешный ответ с токеном и данными пользователя
        return [
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => "{$user->first_name} {$user->patronymic} {$user->last_name}",
                    'birth_date' => $user->birth_date,
                    'email' => $user->email,
                ],
                'token' => $user->access_token,
            ],
        ];
    }

    public function actionLogout(): array
    {
        // Получаем токен из заголовков запроса
        $token = Yii::$app->request->getHeaders()->get('Authorization');

        // Проверяем, что токен существует
        if ($token) {
            // Убираем "Bearer " из начала токена, чтобы осталась только строка токена
            $token = str_replace('Bearer ', '', $token);

            // Ищем пользователя с этим токеном
            $user = User::findOne(['access_token' => $token]);

            if ($user) {
                // Удаляем токен (очищаем значение)
                $user->access_token = null;

                // Сохраняем изменения в базе данных
                if ($user->save()) {
                    $user->setLogs('Авторизация', 'Выход из системы');
                    // Возвращаем успешный ответ с кодом 204 (без тела ответа)
                    Yii::$app->response->setStatusCode(204);
                    return [];
                }
            }
        }

        // Если нет авторизованного пользователя или токен не найден, возвращаем ошибку 403
        Yii::$app->response->statusCode = 403;
        return [
            'message' => 'Login failed',
        ];
    }

}