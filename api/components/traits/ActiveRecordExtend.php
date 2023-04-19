<?php

namespace app\components\traits;

use yii\web\NotFoundHttpException;

trait ActiveRecordExtend 
{
    /**
     * Get record or throw 404 exception
     * 
     * @param int $id
     * 
     * @return self
     * 
     * @throw NotFoundHttpException
     */
    public static function findOrFail($id)
    {
        $model = static::findOne($id);

        if($model !== null){
            return $model;
        }

        throw new NotFoundHttpException();
    }

    /**
     * Get record by condition or throw 404 exception
     * 
     * @param array $condition
     * 
     * @return self
     * 
     * @throw NotFoundHttpException
     */
    public static function findByConditionOrFail($condition)
    {
        $model = static::find()->where($condition)->one();

        if($model !== null){
            return $model;
        }

        throw new NotFoundHttpException();
    }
}

