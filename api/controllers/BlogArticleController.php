<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\components\QueryActiveController;
use yii\helpers\StringHelper;


class BlogArticleController extends QueryActiveController
{
    public $modelClass = 'app\models\BlogArticle';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ['index', 'view'];
        $behaviors['access']['rules'] = array_merge($behaviors['access']['rules'], [
            [
                'allow' => true,
                'actions' => ['index', 'view'],
            ],
        ]);
        return $behaviors;
    }

    public function beforeAction($action)
    {
        $today = new \DateTime('now');
        if (in_array($action->id, ['view'])) {
            $get = Yii::$app->request->queryParams;
            $article = $this->modelClass::findOne($get['id']);
            $article->scenario = 'not_update';
            $article->view_count++;
            $article->save();
        }
        return parent::beforeAction($action);
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        if (in_array($action->id, ['index'])) {
            foreach ($result as $key => $item) {
                $result[$key]['preview'] = StringHelper::truncate(strip_tags($item['text']),150,'...');
            }
        }
        return $result;
    }

}
