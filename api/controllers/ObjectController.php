<?php

namespace app\controllers;

use app\components\interfaces\StatusInterface as S;
use app\components\QueryActiveController;
use app\components\traits\ModeratableActions;
use yii\web\NotFoundHttpException;
use app\models\{Objects, ObjectsSearch, Order, User};
use Yii;

class ObjectController extends QueryActiveController
{
    use ModeratableActions;

    public $elementName = 'обьявлений';

    public $modelClass = Objects::class;
    public $searchModel = ObjectsSearch::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ['options', 'index', 'view', 'get-model-list', 'get-price-ranges', 'search'];
        $behaviors['access']['rules'] = array_merge($behaviors['access']['rules'], [
            [
                'allow' => true,
                'actions' => ['index', 'view', 'get-model-list', 'get-price-ranges', 'search'],
            ],
            [
                'allow' => true,
                'actions' => ['create', 'all', 'user-view', 'user-objects', 'confirmed-order-implementer-status-list'],
                'roles' => ['user']
            ],
            [
                'allow' => true,
                'actions' => ['update', 'for-moderation', 'approve', 'reject'],
                'roles' => ['moderator']
            ]
        ]);
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['update']['scenario'] = 'update';
        $actions['index']['prepareSearchQuery'] = [$this, 'prepareSearchQuery'];
        $actions['all'] = $actions['index'];
        $actions['user-objects'] = $actions['index'];
        $actions['user-objects']['prepareSearchQuery'] = [$this, 'prepareUserObjectsSearchQuery'];
        $actions['search'] = $actions['index'];
        $actions['search']['prepareSearchQuery'] = [$this, 'prepareSearchForSearchQuery'];
        $get = Yii::$app->request->queryParams;
        if (array_key_exists('pagination', $get)) {
            $pagination = $get['pagination'] ?: false;
            $actions['all']['pagination'] = $pagination;
            $actions['user-objects']['pagination'] = $pagination;
        }
        return $actions;
    }

    public function prepareSearchQuery()
    {
        $query = parent::prepareSearchQuery();
        if (empty(Yii::$app->user->id) || Yii::$app->user->identity->role == User::ROLE_USER) {
            $query->andWhere(['in','object.status', [S::APPROVED, S::DEACTIVATED]]);
        }
        return $query;
    }
    public function prepareUserObjectsSearchQuery()
    {
        $query = parent::prepareSearchQuery();
        $query->andWhere(['object.user_id' => Yii::$app->user->id]);
        $query->andWhere('object.status != :status', ['status'=> S::DELETED]);
        return $query;
    }

    public function prepareSearchForSearchQuery()
    {
        $query = $this->prepareSearchQuery();
        $query->select(['name'])->distinct()->orderBy('CHAR_LENGTH(name)');
        return $query;
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        if (in_array($action->id, ['index', 'all', 'user-objects'])) {
            foreach ($result as $key => $item) {
                $result[$key]['status_busy'] = Objects::getStatusBusy($item['id']);
            }
        }
        if (in_array($action->id, ['view', 'user-view'])) {
            $result['status_busy'] = Objects::getStatusBusy($result['id']);
        }
        return $result;
    }

    public function actionGetModelList()
    {
        $query = $this->prepareSearchQuery();
        $models = $query->select(['model'])->groupBy(['model'])->asArray();
        $list = [];
        foreach ($models->all() as $model) {
            if (!empty($model['model'])) {
                $list[] = $model['model'];
            }
        }

        return $list;
    }

    public function actionGetPriceRanges()
    {
        $query = $this->prepareSearchQuery();
        $ranges = [];
        $ranges['price_1']['max'] = $query->select(['price_1'])->max('price_1');
        $ranges['price_1']['min'] = $query->select(['price_1'])->min('price_1');
        $ranges['price_2']['max'] = $query->select(['price_2'])->max('price_2');
        $ranges['price_2']['min'] = $query->select(['price_2'])->min('price_2');

        return $ranges;
    }

    public function actionUserView($id)
    {
        $model = $this->modelClass::findOne($id);
        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException('Объявление не найдено');
        }
    }

    public function actionConfirmedOrderImplementerStatusList($id)
    {
        $result['amountOfActive'] = Order::find()
            ->where([
                'confirmed' => 1,
                'object_id' => $id,
            ])
            ->andWhere(['or',['<>', 'rejected', 1], ['rejected' => null]])
            ->andWhere(['archive' => 0, 'deleted' => 0])
            ->count();

        $result['amountOfArchive'] = Order::find()
            ->where([
                'confirmed' => 1,
                'object_id' => $id,
            ])
            ->andWhere(['or',['<>', 'rejected', 1], ['rejected' => null]])
            ->andWhere(['archive' => 1, 'deleted' => 0])
            ->count();

        return $result;
    }
}