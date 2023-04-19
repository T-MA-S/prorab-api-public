<?php

namespace app\controllers;

use app\models\Category;
use app\models\Favourites;
use app\models\Order;
use Yii;
use yii\helpers\Url;
use app\components\QueryActiveController;
use yii\web\ServerErrorHttpException;
use app\models\Objects;

class BlogCommentController extends QueryActiveController
{
    public $modelClass = 'app\models\BlogComment';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ['options', 'index', 'view'];
        $behaviors['access']['rules'] = array_merge($behaviors['access']['rules'], [
            [
                'allow' => true,
                'actions' => ['index', 'view'],
            ],
            [
                'allow' => true,
                'actions' => ['create'],
                'roles' => ['user']
            ],
        ]);
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create'], $actions['update'], $actions['delete']);

        return $actions;
    }

    public function actionCreate()
    {
        $model = new $this->modelClass();
        $save = false;

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->parent_id) {
            $parent = $this->modelClass::findOne($model->parent_id);;
            $save = $model->prependTo($parent);
        } else {
            $save = $model->makeRoot();
        }
        if ($save) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $id = implode(',', $model->getPrimaryKey(true));
            $response->getHeaders()->set('Location', Url::toRoute(['view', 'id' => $id], true));
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }

    public function actionUpdate($id)
    {
        $model = $this->modelClass::findOne($id);
        $save = false;


        if ($model->load(Yii::$app->getRequest()->getBodyParams(), '')) {
            if (empty($model->parent_id)) {
                if (!$model->isRoot()) {
                    $save = $model->makeRoot();
                } else {
                    $save = $model->save();
                }
            } else {
                if ($model->id != $model->parent_id) {
                    $parent = $this->modelClass::findOne($model->parent_id);
                    $save = $model->appendTo($parent);
                }
            }
            // }
        }

        if ($save === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $model;
    }

    public function actionDelete($id)
    {
        $model = $this->modelClass::findOne($id);
        $delete = false;

        if ($model->isRoot())
            $delete = $model->deleteWithChildren();
        else
            $delete = $model->delete();

        if ($delete === false) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }

        Yii::$app->getResponse()->setStatusCode(204);
    }
}
