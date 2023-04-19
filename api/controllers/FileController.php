<?php

namespace app\controllers;

use yii\filters\AccessControl;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use app\components\UploadedFile;

class FileController extends ActiveController
{
    public $modelClass = 'app\models\Upload';
    public $uploadPath = '/uploads/';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['user']
                ],
            ],
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['update'], $actions['create'], $actions['delete'], $actions['index'], $actions['view']);

        return $actions;
    }

    public function actionUpload()
    {
        $file = UploadedFile::getInstanceByName('file');
        if ($file) {
            return $file->saveAs(\Yii::getAlias('@webroot') . $this->uploadPath . $file->name);
        }
        throw new ServerErrorHttpException('Ошибка загрузки файла');
    }
}
