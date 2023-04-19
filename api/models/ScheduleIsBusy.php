<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "schedule_is_busy".
 *
 * @property int $id
 * @property int|null $object_id
 * @property string|null $date_from
 * @property int|null $duration
 * @property int|null $status
 *
 * @property Objects $object
 */
class ScheduleIsBusy extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'schedule_is_busy';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['object_id', 'duration', 'status'], 'integer'],
            [['date_from'], 'safe'],
            [['object_id'], 'exist', 'skipOnError' => true, 'targetClass' => Objects::className(), 'targetAttribute' => ['object_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'object_id' => 'ID объявления',
            'date_from' => 'Дата начала',
            'duration' => 'Продолжительность',
            'status' => 'Статус',
        ];
    }

    /**
     * Gets query for [[Object]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObject()
    {
        return $this->hasOne(Objects::className(), ['id' => 'object_id']);
    }

    public static function saveFromArray($object_id, $array)
    {
        $scheduleIds = static::find()->select('id')->where(['object_id' => $object_id])->all();
        $scheduleIds = ArrayHelper::index($scheduleIds, 'id');

        foreach ($array as $schedule) {
            $scheduleIsBusy = static::find()
                ->where(['object_id' => $object_id])
                ->andFilterWhere(['like', 'date_from', $schedule->date])->one();
            if(empty($scheduleIsBusy)) {
                $scheduleIsBusy = new ScheduleIsBusy();
                $scheduleIsBusy->object_id = $object_id;
                $scheduleIsBusy->date_from = $schedule->date;
            } else {
                ArrayHelper::remove($scheduleIds, $scheduleIsBusy->id);
            }
            $scheduleIsBusy->duration = $schedule->duration;
            $scheduleIsBusy->save();
        }

        foreach ($scheduleIds as $scheduleForDelete) {
            $scheduleForDelete->delete();
        }
    }
}
