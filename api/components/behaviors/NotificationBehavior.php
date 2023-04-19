<?php

namespace app\components\behaviors;

use Yii;
use app\models\Notification;
use yii\base\Behavior;

class NotificationBehavior extends Behavior
{
    public function sendNotification($type, $message, $user_id)
    {
        $notification = new Notification();
        $notification->user_id = $user_id;
        $notification->user_from_id = Yii::$app->user->id;
        $notification->model = $this->owner->tableName();
        $notification->model_id = $this->owner->id;
        $notification->type = $type;
        $notification->text = $message;

        $notification->save();
    }
}
