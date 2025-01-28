<?php
/**
 * @author aleksejpuhov
 * File: ActivitiesController.php
 * Date: 26.01.2025
 * Time: 16:57
 */

namespace app\controllers;

use app\filters\auth\MALFINHttpBearerAuth;
use app\models\Files;
use app\models\Sessions;
use Yii;
use yii\rest\Controller;
use yii\web\Response;

class ActivitiesController extends Controller
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


    public function actionAvailable(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Получаем все активные сессии
        $sessions = Sessions::find()->with('type')->asArray()->all();
        Yii::$app->response->statusCode = 200;

        $data = array_map(function ($session) {
            unset($session['type_id']);
            return $session;
        }, $sessions);

        // Формируем ответ
        return [
            'data' => $data,
        ];
    }

    public function actionHomework($id)
    {
        //  Находим файл домашнего задания по ID сессии
        $file = Files::find()
            ->where(['session_id' => $id, 'file_type' => 'assignment'])
            ->one();

        if (!$file) {
            Yii::$app->response->statusCode = 404;
            return [
                'message' => 'Файл не найден.',
            ];
        }

        $filePath = Yii::getAlias('@webroot') . $file->file_path;

        if (!file_exists($filePath)) {
            Yii::$app->response->statusCode = 404;
            return [
                'message' => 'Файл отсутствует на сервере.',
            ];
        }

        // Отправляем файл клиенту
        return Yii::$app->response->sendFile($filePath, basename($file->file_path));
    }

    public function actionUploadHomework($id)
    {
        // Проверяем, что файл был передан
        if (empty($_FILES['file'])) {
            Yii::$app->response->statusCode = 400; // Bad Request
            return [
                'message' => 'Файл не был загружен.',
            ];
        }

        $file = $_FILES['file'];

        // Убедимся, что файл корректного типа (например, PDF, DOCX и т.д.)
        if (!in_array($file['type'], ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])) {
            Yii::$app->response->statusCode = 415; // Unsupported Media Type
            return [
                'message' => 'Неверный формат файла. Пожалуйста, загрузите PDF или Word документ.',
            ];
        }

        // Устанавливаем путь для сохранения файла
        $uploadDir = Yii::getAlias('@webroot/uploads/homework/');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Создаем директорию, если она не существует
        }

        $filePath = $uploadDir . basename($file['name']);

        // Перемещаем загруженный файл в директорию
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            Yii::$app->response->statusCode = 500; // Internal Server Error
            return [
                'message' => 'Произошла ошибка при сохранении файла.',
            ];
        }

        // Сохраняем информацию о файле в базу данных
        $fileModel = new Files();
        $fileModel->user_id = Yii::$app->user->id; // ID пользователя
        $fileModel->session_id = $id; // ID сессии
        $fileModel->file_type = 'assignment'; // Тип файла
        $fileModel->file_path = '/uploads/homework/' . basename($file['name']);
        $fileModel->status = 'pending'; // Статус файла

        if ($fileModel->save()) {
            Yii::$app->response->statusCode = 201; // Created
            $fileModel->setLogs('Загрузка файла', 'Файл успешно отправлен');
            return [
                'data' => [
                    'message' => 'Файл успешно отправлен',
                ],
            ];
        } else {
            Yii::$app->response->statusCode = 500; // Internal Server Error
            return [
                'message' => 'Произошла ошибка при сохранении данных о файле.',
            ];
        }
    }

    public function actionCreate()
    {

        if (Yii::$app->user->identity->role_id !== 1) {
            return $this->asJson([
                'error' => [
                    'code' => 403,
                    'message' => 'Доступ запрещен. Только администратор может создавать занятия.'
                ]
            ])->setStatusCode(403);
        }
        $model = new Sessions();  // Создаем новый объект модели Sessions

        // Загружаем данные из POST-запроса в модель
        $model->load(Yii::$app->request->post(), '');

        // Если модель прошла валидацию
        if ($model->validate()) {
            // Сохраняем модель в базе данных
            if ($model->save()) {

                $model->setLogs('Занятие успешно создано!');


                return $this->asJson([
                    'message' => 'Занятие успешно создано!',
                    'data' => [
                        'id' => $model->id,
                        'title' => $model->title,
                        'type' => $model->type->name,
                        'description' => $model->description,
                        'start_date' => $model->start_date,
                        'end_date' => $model->end_date,
                        'max_participants' => $model->max_participants,
                    ]
                ])->setStatusCode(201);
            }
        } else {
            // Если валидация не прошла, собираем ошибки
            $errors = [];
            foreach ($model->errors as $field => $messages) {
                $errors[$field] = $messages;
            }

            // Если ошибок нет (что маловероятно), добавляем стандартные ошибки
            if (empty($errors)) {
                $errors = [
                    'title' => ['Поле Заголовок обязательно для заполнения.'],
                    'type_id' => ['Поле Тип занятия обязательно для заполнения.'],
                    'description' => ['Поле Описание обязательно для заполнения.'],
                    'start_date' => ['Поле Дата начала обязательно для заполнения.'],
                    'end_date' => ['Поле Дата окончания обязательно для заполнения.'],
                    'max_participants' => ['Поле Максимальное количество участников обязательно для заполнения.'],
                ];
            }

            // Формируем ответ с ошибками
            return $this->asJson([
                'error' => [
                    'code' => 422,
                    'message' => 'Ошибка валидации',
                    'errors' => $errors,
                ]
            ])->setStatusCode(422);
        }
    }

    public function actionDelete($id)
    {
        if (Yii::$app->user->identity->role_id !== 1) {
            return $this->asJson([
                'error' => [
                    'code' => 403,
                    'message' => 'Доступ запрещен. Только администратор может удалять занятия.'
                ]
            ])->setStatusCode(403);
        }
        // Находим занятие по ID
        $model = Sessions::findOne($id);

        // Если занятие не найдено
        if (!$model) {
            return $this->asJson([
                'error' => [
                    'code' => 404,
                    'message' => 'Занятие не найдено',
                ]
            ])->setStatusCode(404);
        }

        // Удаляем занятие
        if ($model->delete()) {
            $model->setLogs('Занятие успешно удалено!');
            return $this->asJson([
                'data' => [
                    'message' => 'Занятие успешно удалено',
                ]
            ])->setStatusCode(204);
        }

        // Если не удалось удалить, возвращаем ошибку
        return $this->asJson([
            'error' => [
                'code' => 500,
                'message' => 'Ошибка при удалении занятия',
            ]
        ])->setStatusCode(500);
    }

    public function actionUpdate($id)
    {
        if (Yii::$app->user->identity->role_id !== 1) {
            return $this->asJson([
                'error' => [
                    'code' => 403,
                    'message' => 'Доступ запрещен. Только администратор может изменять занятия.'
                ]
            ])->setStatusCode(403);
        }
        // Находим занятие по ID
        $model = Sessions::findOne($id);

        // Если занятие не найдено
        if (!$model) {
            return $this->asJson([
                'error' => [
                    'code' => 404,
                    'message' => 'Занятие не найдено',
                ]
            ])->setStatusCode(404);
        }

        // Загружаем данные из PUT-запроса в модель
        $data = Yii::$app->request->getBodyParams();
        $model->load($data, '');

        // Если модель прошла валидацию
        if ($model->validate()) {
            // Сохраняем модель в базе данных
            if ($model->save()) {
                $model->setLogs('Занятие успешно обновлено!');
                return $this->asJson([
                    'message' => 'Занятие успешно обновлено',
                    'data' => [
                        'id' => $model->id,
                        'title' => $model->title,
                        'type' => $model->type->name,
                        'description' => $model->description,
                        'start_date' => $model->start_date,
                        'end_date' => $model->end_date,
                        'max_participants' => $model->max_participants,
                    ]
                ])->setStatusCode(200);
            } else {
                return $this->asJson([
                    'error' => [
                        'code' => 500,
                        'message' => 'Ошибка при обновлении занятия',
                    ]
                ])->setStatusCode(500);
            }
        } else {
            // Если валидация не прошла, собираем ошибки
            $errors = [];
            foreach ($model->errors as $field => $messages) {
                $errors[$field] = $messages;
            }

            return $this->asJson([
                'error' => [
                    'code' => 422,
                    'message' => 'Ошибка валидации',
                    'errors' => $errors,
                ]
            ])->setStatusCode(422);
        }
    }

    /**
     * Поиск по курсам, мастер-классам и индивидуальным занятиям
     * @return array
     */
    public function actionSearch()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Получаем параметр query из GET-запроса
        $query = Yii::$app->request->get('query');

        // Если query не передан, возвращаем пустой результат
        if (!$query) {
            return [
                'data' => [],
            ];
        }

        // Ищем курсы по названию и описанию
        $sessions = Sessions::find()
            ->where(['like', 'title', $query])
            ->orWhere(['like', 'description', $query])
            ->asArray()
            ->all();

        // Формируем результат
        $data = array_map(function ($session) {
            return [
                'id' => $session['id'],
                'title' => $session['title'],
                'description' => $session['description'],
                'type_id' => $session['type_id'],
                'start_date' => $session['start_date'],
                'end_date' => $session['end_date'],
                'max_participants' => $session['max_participants'],
            ];
        }, $sessions);

        return [
            'data' => $data,
        ];
    }

    public function actionSelectCourse($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Получаем все активные сессии
        $sessions = Sessions::findOne($id);
        Yii::$app->response->statusCode = 200;

        // Формируем ответ
        return $this->asJson([
            'messages' => 'Занятия успешно загружены',
            'results' => [
                'id'=>$sessions->id,
                'title'=>$sessions->title,
                'description'=>$sessions->description,
                'type'=>$sessions->type->name,
                'start_date'=>$sessions->start_date,
                'end_date'=>$sessions->end_date,
                'max_participants'=>$sessions->max_participants,
                'created_at'=>$sessions->created_at,
                'updated_at'=>$sessions->updated_at,
            ],
        ])->setStatusCode(200);
    }

}