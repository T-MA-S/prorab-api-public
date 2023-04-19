<?php

namespace app\controllers;

use Yii;
use app\components\SxGeo;
use app\components\QueryActiveController;
use yii\web\NotFoundHttpException;

class CityController extends QueryActiveController
{
    public $modelClass = 'app\models\City';
    public $searchModel = 'app\models\CitySearch';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ['options', 'index', 'view', 'geo'];
        $behaviors['access']['rules'] = array_merge($behaviors['access']['rules'], [
            [
                'allow' => true,
                'actions' => ['index', 'view', 'geo'],
            ]
        ]);
        return $behaviors;
    }

    public function actionGeo()
    {
        $geo = new SxGeo($_SERVER['DOCUMENT_ROOT'] . '/SxGeoCity.dat');
        $geoCity = $geo->getCity(Yii::$app->request->userIP);
        $model = $this->modelClass::find()->where(['geo_id'=>$geoCity['city']['id']])->one();
        if (!empty($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException('Город не определён');
        }
    }
}
