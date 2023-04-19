<?php

namespace app\controllers;

use Yii;
use app\models\Mark;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use app\components\QueryActiveController;
use app\components\traits\ModeratableActions;

class MarkController extends QueryActiveController
{
    use ModeratableActions;

    public $modelClass = Mark::class;

    public $elementName = 'отзывов';
    
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ['options', 'index', 'view'];
        $behaviors['access']['rules'] = array_merge($behaviors['access']['rules'], [
            [
                'allow' => true,
                'actions' => ['index', 'view']
            ],
            [
                'allow' => true,
                'actions' => ['create-user-mark', 'get-mark', 'user-marks', 'create'],
                'roles' => ['user']
            ],
            [
                'allow' => true,
                'actions' => ['all', 'on-moderation', 'for-moderation', 'approve', 'reject'],
                'roles' => ['moderator']
            ]
        ]);
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        $actions['all'] = $actions['index'];

        $actions['create-user-mark'] = $actions['create'];

        $actions['on-moderation'] = $actions['index'];
        $actions['on-moderation']['prepareSearchQuery'] = [$this, 'onModerationSearchQuery'];

        return $actions;
    }

    public function actionUserMarks()
    {
        $get = Yii::$app->request->queryParams;

        if(empty(Yii::$app->user->id) || !array_key_exists('type', $get)) {
            throw new BadRequestHttpException();
        }

        [$type, $relation] = $get['type'] ? ['user_to_id', 'user_from_id'] : ['user_from_id', 'user_to_id'];

        return $this->modelClass::userMarks($type, $relation, $get['id']);
    }

    public function onModerationSearchQuery()
    {
        $query = parent::prepareSearchQuery();

        return $query->andWhere('status = :s', [':s' => Mark::AWAITING]);
    }

    public function actionGetMark()
    {
        $get = \Yii::$app->request->queryParams;
        $mark = Mark::find()->select('AVG(mark)')->where(['user_to_id' => $get['user_id']])->scalar();

        return ['mark' => $mark];
    }

    public function actionAll()
    {
        $query = $this->prepareSearchQuery();
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (!empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }
        if ($requestParams['pagination'] != 0) {
            $pagination = $requestParams['pagination'];
        } else {
            $pagination = false;
        }

        $provider = Yii::createObject([
            'class' => ActiveDataProvider::class,
            'query' => $query,
            'pagination' => $pagination,
        ]);

        return $provider;
    }
}
