<?php

namespace app\controllers;

use app\components\QueryActiveController;
use app\models\ChatMessage;
use Yii;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

class ChatMessageController extends QueryActiveController
{
    public $modelClass = 'app\models\ChatMessage';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'] = array_merge($behaviors['access']['rules'], [
            [
                'allow' => true,
                'actions' => ['create', 'index', 'view', 'user-messages'],
                'roles' => ['user']
            ],
            [
                'allow' => true,
                'actions' => ['user-messages-by-id'],
                'roles' => ['tech']
            ]
        ]);
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        return $actions;
    }

    public function actionUserMessages()
    {
        $newMessages = ChatMessage::find()->where([
            'to_user_id' => \Yii::$app->user->id,
            'viewed' => 0
        ])->all();
        foreach ($newMessages as $message) {
            $message->viewed = 1;
            $message->save();
        }
        $messages = ChatMessage::find()
            ->where('from_user_id = :from_user_id OR to_user_id = :to_user_id', [
                ':from_user_id' => \Yii::$app->user->id,
                ':to_user_id' => \Yii::$app->user->id
            ])->all();
        return $messages;
    }

    public function actionUserMessagesById($id)
    {
        $newMessages = ChatMessage::find()->where([
            'from_user_id' => $id,
            'viewed' => 0
        ])->all();
        foreach ($newMessages as $message) {
            $message->viewed = 1;
            $message->save();
        }
        $messages = ChatMessage::find()
            ->where('from_user_id = :from_user_id OR to_user_id = :to_user_id', [
                ':from_user_id' => $id,
                ':to_user_id' => $id
            ])->all();
        return $messages;
    }

    public function actionCreate()
    {
        /* @var $model \yii\db\ActiveRecord */
        $model = new $this->modelClass();

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $id = implode(',', $model->getPrimaryKey(true));
            $response->getHeaders()->set('Location', Url::toRoute(['view', 'id' => $id], true));
            $responseModel = $this->modelClass::find()->where(['id' => $model->id])->with('files')->one();
            $result = [
                'id' => $responseModel->id,
                'from_user_id' => $responseModel->from_user_id,
                'to_user_id' => $responseModel->to_user_id,
                'text' => $responseModel->text,
                'viewed' => $responseModel->viewed,
                'created' => $responseModel->created,
                'view_time' => $responseModel->view_time,
                'files' => []
            ];
            foreach ($responseModel->files as $file) {
                $result['files'][] = [
                    'id' => $file->id,
                    'file' => $file->file,
                    'type' => $file->type,
                ];
            }
            return $result;
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }
}
