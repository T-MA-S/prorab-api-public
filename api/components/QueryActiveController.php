<?php

namespace app\components;

use Yii;
use yii\filters\AccessControl;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use yii\data\ActiveDataFilter;

class QueryActiveController extends ActiveController
{
    public $modelClass;
    public $searchModel;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                // restrict access to
                'Origin' => [
                    'http://prorab-app.local',
                    'https://prorab-app.local',
                    'http://5.128.156.25',
                    'http://formantestfront.tw1.ru',
                    'http://reverse',
                    'http://tets.foreman-go.ru'
                ],
                // Allow only POST and PUT methods
                'Access-Control-Request-Method' => ['*'], //['POST', 'PUT', 'GET', 'PATCH', 'DELETE', 'OPTIONS'],
                // Allow only headers 'X-Wsse'
                'Access-Control-Request-Headers' => ['*'], //['Authorization', 'Accept', 'ORIGIN'],
                // Allow credentials (cookies, authorization headers, etc.) to be exposed to the browser
                'Access-Control-Allow-Credentials' => false,
                'Access-Control-Expose-Headers' => ['*'],
            ],
        ];

        $behaviors['authenticator'] = [
            'except' => ['options'],
            'class' => HttpBearerAuth::class,
        ];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow'   => true,
                    'actions' => ['options']
                ],
                [
                    'allow' => true,
                    'actions' => ['update', 'delete'],
                    'roles' => ['user'],
                    'matchCallback' => function ($rule) {
                        $id = Yii::$app->request->get('id');
                        $model = $this->modelClass::findOne($id);

                        if ($model->getAttribute('user_id')) {
                            return $model->user_id == Yii::$app->user->id ? true : false;
                        } else {
                            return  true;
                        }
                    },
                ],
                [
                    'allow' => true,
                    'roles' => ['admin']
                ],
            ],
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        $actions['create'] = [
            'class' => 'app\components\actions\CreateAction',
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
            'scenario' => $this->createScenario,
        ];
        $actions['index']['prepareSearchQuery'] = [$this, 'prepareSearchQuery'];
        $get = \Yii::$app->request->queryParams;
        if (array_key_exists('pagination', $get)) {
            $actions['index']['pagination'] = $get['pagination'] ?: false;
        }
        return $actions;
    }

    public function prepareSearchQuery()
    {
        $get = \Yii::$app->request->queryParams;

        if ($this->searchModel) {
            $searchModelName = $this->searchModel;
            $searchModel = new $searchModelName();
            $query = $searchModel->search($get);
        } else {
            $query = $this->modelClass::find();
        }
        $dataFilter = new ActiveDataFilter(['searchModel' => $this->modelClass]);

        if ($dataFilter->load($get)) {
            $filter = $dataFilter->build(false);

            if (!empty($filter)) {
                if (array_key_exists('expand', $get)) {
                    if ($get['expand']) {
                        foreach (explode(', ', $get['expand']) as $expand) {
                            $query->joinWith($expand);
                        }
                    }
                }
                $query->andWhere($filter);
            }
        }
        return $query;
    }
}
