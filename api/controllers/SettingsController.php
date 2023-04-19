<?php

namespace app\controllers;

use Yii;
use app\components\QueryActiveController;
use app\models\Settings;

class SettingsController extends QueryActiveController
{
    public $modelClass = 'app\models\Settings';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'] = array_merge($behaviors['access']['rules'], [
            [
                'allow' => true,
                'actions' => ['index', 'view'],
                'roles' => ['user']
            ]
        ]);
        return $behaviors;
    }

    public function actionFormData()
    {
        $query = $this->prepareSearchQuery();
        $settings = $query->select(['name', 'text'])->all();
        $result = [];
        foreach ($settings as $item) {
            $result[$item->name] = $item->text;
        }
        return $result;
    }

    public function actionUpdateAll()
    {
        $request = Yii::$app->getRequest()->getBodyParams();
        $result = [];
        if (is_array($request)) {
            foreach ($request as $key => $item) {
                $settings = Settings::find()->where(['name' => $key])->one();
                if (!empty($settings)) {
                    $settings->text = $item;
                    $settings->save();
                    $result[] = $settings;
                }
            }
        }
        return $result;
    }

}
