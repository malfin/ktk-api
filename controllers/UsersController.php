<?php
/**
 * @author aleksejpuhov
 * File: UsersController.php
 * Date: 26.01.2025
 * Time: 18:21
 */

namespace app\controllers;

use app\filters\auth\MALFINHttpBearerAuth;
use app\models\User;
use Yii;
use yii\filters\Cors;
use yii\rest\Controller;
use yii\web\Response;

class UsersController extends Controller
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

    public function actionIndex()
    {
        if (Yii::$app->user->identity->role_id !== 1) {
            return $this->asJson([
                'error' => [
                    'code' => 403,
                    'message' => 'Доступ запрещен. Только администратор может смотреть пользователей.'
                ]
            ])->setStatusCode(403);
        }
        // Получаем всех пользователей из базы данных
        $users = User::find()->all();

        // Формируем ответ
        $data = [];
        foreach ($users as $user) {
            $data[] = [

                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'patronymic' => $user->patronymic,
                'email' => $user->email,
                'role' => $user->role_id == 1 ? 'admin' : 'user',  // Пример: преобразуем роль на основе role_id
                'birth_date' => $user->birth_date,
                'status' => $user->status,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,

            ];
        }

        // Возвращаем ответ в формате JSON
        return $this->asJson([
            'message' => 'Данные успешно получены',
            'results' => $data
        ]);
    }

    public function actionRoleUpdate($id)
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

        // Загружаем данные пользователя
        $user = User::findOne($id);

        if (!$user) {
            Yii::$app->response->statusCode = 404;
            return [
                'error' => [
                    'code' => 404,
                    'message' => 'Пользователь не найден'
                ]
            ];
        }

        // Загружаем данные из запроса
        $data = Yii::$app->request->getBodyParams();

        if (isset($data['role'])) {
            $role = $data['role'];

            // Здесь можно добавить проверку на допустимые роли (например, 'admin', 'user', 'manager')
            if (!in_array($role, [1, 2])) {
                Yii::$app->response->statusCode = 400;

                return [
                    'error' => [
                        'code' => 400,
                        'message' => 'Недопустимая роль'
                    ]
                ];
            }

            // Обновляем роль пользователя
            $user->role_id = $role;  // Пример обновления, если у вас связь через role_id
            if ($user->save()) {
                Yii::$app->response->statusCode = 200;
                $user->setLogs('Пользователи','Роль пользователя успешно обновлена!');
                return [
                    'data' => [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->first_name . ' ' . $user->last_name,
                            'role' => $user->role->name,
                        ],
                        'code' => 200,
                        'message' => 'Роль пользователя успешно обновлена',
                    ],
                ];
            } else {
                Yii::$app->response->statusCode = 500;
                return [
                    'error' => [
                        'code' => 500,
                        'message' => 'Не удалось обновить роль пользователя'
                    ]
                ];
            }
        }

        // Если роль не передана
        Yii::$app->response->statusCode = 400;
        return [
            'error' => [
                'code' => 400,
                'message' => 'Роль не указана'
            ]
        ];
    }
}