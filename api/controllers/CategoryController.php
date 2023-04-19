<?php

namespace app\controllers;

use app\components\interfaces\StatusInterface as S;
use app\models\Category;
use app\models\Favourites;
use app\models\Order;
use Yii;
use yii\helpers\Url;
use app\components\QueryActiveController;
use yii\web\ServerErrorHttpException;
use app\models\Objects;

class CategoryController extends QueryActiveController
{
    public $modelClass = 'app\models\Category';
    public $searchModel = 'app\models\CategorySearch';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ['options', 'index', 'view', 'search'];
        $behaviors['access']['rules'] = array_merge($behaviors['access']['rules'], [
            [
                'allow' => true,
                'actions' => ['account-list', 'object-status-list-by-type', 'confirmed-order-client-status-list-by-type'],
                'roles' => ['user']
            ],
            [
                'allow' => true,
                'actions' => ['index', 'view', 'search'],
            ],
        ]);
        return $behaviors;
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        if (in_array($action->id, ['index'])) {
            foreach ($result as $key => $item) {
                if ($result[$key]['depth'] != 0) {
                    $result[$key]['amountOfChildren'] = Category::find()->where([
                        'parent_id' => $result[$key]['id'],
                    ])->count();
                }
            }
        }
        return $result;
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['admin-list'] = $actions['index'];
        $actions['search'] = $actions['index'];
        $actions['admin-list']['pagination'] = false;
        $actions['search']['pagination'] = false;
        unset($actions['create'], $actions['update'], $actions['delete']);

        return $actions;
    }

    public function actionAccountList()
    {
        $categories = Category::find()->select(['image', 'type', 'title', 'depth'])->where(['depth' => 0])->asArray()->all();
        foreach ($categories as $key => $category) {
            $categories[$key]['objectsCountByType'] = Objects::find()->where([
                'type' => $category['type'],
                'user_id' => Yii::$app->user->id
            ])->andWhere(['<>','status', S::DELETED])->count();

            $categories[$key]['favouritesObjectsCountByType'] = Favourites::find()->joinWith('object')->where([
                'object.type' => $category['type'],
                'favourites.user_id' => Yii::$app->user->id,
            ])->andWhere(['<>','object.status', S::DELETED])->count();

            $categories[$key]['newOrdersCountByType'] = Order::find()->joinWith('object')->where([
                'object.type' => $category['type'],
                'object.user_id' => Yii::$app->user->id,
            ])
            ->andWhere(['or',['<>', 'order.confirmed', 1], ['order.confirmed' => null]])
            ->andWhere(['or',['<>', 'order.rejected', 1], ['order.rejected' => null]])
            ->andWhere(['order.archive' => 0, 'order.deleted' => 0, 'order.status' => 1])
            ->andWhere(['<>','object.status', S::DELETED])
            ->count();

            $categories[$key]['confirmedOrdersCountByTypeClient'] = Order::find()->joinWith('object')->where([
                'order.confirmed' => 1,
                'object.type' => $category['type'],
                'order.user_id' => Yii::$app->user->id,
            ])
            ->andWhere(['or',['<>', 'order.rejected', 1], ['order.rejected' => null]])
            ->andWhere(['order.archive' => 0, 'order.deleted' => 0])
            ->andWhere(['<>','object.status', S::DELETED])->count();

            $categories[$key]['confirmedOrdersCountByTypeImplementer'] = Order::find()->joinWith('object')->where([
                'order.confirmed' => 1,
                'object.type' => $category['type'],
                'object.user_id' => Yii::$app->user->id,
            ])
            ->andWhere(['or',['<>', 'order.rejected', 1], ['order.rejected' => null]])
            ->andWhere(['order.archive' => 0, 'order.deleted' => 0])
            ->andWhere(['<>','object.status', S::DELETED])->count();
        }
        return $categories;
    }

    public function actionObjectStatusListByType($type)
    {
        $categories = Category::find()->select(['image', 'type', 'title', 'depth'])->where(['depth' => 0, 'type' => $type])->asArray()->all();
        foreach ($categories as $key => $category) {
            $categories[$key]['amountOfActive'] = Objects::find()->where([
                'status' => S::APPROVED,
                'type' => $category['type'],
                'user_id' => Yii::$app->user->id
            ])->count();

            $categories[$key]['amountOfModerated'] = Objects::find()->where([
                'type' => $category['type'],
                'user_id' => Yii::$app->user->id
            ])
            ->andWhere(['in','status', [S::REJECTED, S::AWAITING]])
            ->count();
            $categories[$key]['amountOfStopped'] = Objects::find()->where([
                'status' => S::DEACTIVATED,
                'type' => $category['type'],
                'user_id' => Yii::$app->user->id
            ])->count();
            $categories[$key]['amountOfDraft'] = 0;
        }
        return $categories;
    }

    public function actionConfirmedOrderClientStatusListByType($type)
    {
        $categories = Category::find()->select(['image', 'type', 'title', 'depth'])->where(['depth' => 0, 'type' => $type])->asArray()->all();
        foreach ($categories as $key => $category) {
            $categories[$key]['amountOfActive'] = Order::find()->joinWith('object')->where([
                'order.confirmed' => 1,
                'object.type' => $category['type'],
                'order.user_id' => Yii::$app->user->id,
            ])
            ->andWhere(['or',['<>', 'order.rejected', 1], ['order.rejected' => null]])
            ->andWhere(['archive' => 0, 'deleted' => 0])
            ->andWhere(['<>','object.status', S::DELETED])->count();

            $categories[$key]['amountOfArchive'] = Order::find()->joinWith('object')->where([
                'order.confirmed' => 1,
                'object.type' => $category['type'],
                'order.user_id' => Yii::$app->user->id,
            ])
            ->andWhere(['or',['<>', 'order.rejected', 1], ['order.rejected' => null]])
            ->andWhere(['archive' => 1, 'deleted' => 0])
            ->andWhere(['<>','object.status', S::DELETED])->count();
        }
        return $categories;
    }

    public function actionCreate()
    {
        $model = new $this->modelClass();
        $save = false;

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->parent_id) {
            $parent = $this->modelClass::findOne($model->parent_id);;
            $model->type = $parent->type;
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
                    $model->type = $parent->type;
                    $save = $model->appendTo($parent);
                }
            }
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
