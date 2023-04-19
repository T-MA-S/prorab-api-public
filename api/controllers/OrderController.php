<?php

namespace app\controllers;

use Yii;
use app\components\QueryActiveController;
use app\models\Objects;
use app\models\User;
use app\components\helpers\PaymentHelper;
use app\components\interfaces\StatusInterface;
use app\components\traits\ModeratableActions;
use app\models\Order;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class OrderController extends QueryActiveController
{
    use ModeratableActions;

    public $modelClass = Order::class;

    public $elementName = 'заявок';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'] = array_merge($behaviors['access']['rules'], [
            [
                'allow' => true,
                'actions' => ['create', 'index', 'archive', 'view', 'get-confirmed', 'amount-by-user', 'amount-confirmed-by-user'],
                'roles' => ['user']
            ],
            [
                'allow' => true,
                'actions' => ['update'],
                'roles' => ['user'],
                'matchCallback' => function ($rule) {
                    $id = Yii::$app->request->get('id');
                    $model = $this->modelClass::findOne($id);

                    if ($model->user_id == Yii::$app->user->id) {
                        return true;
                    }
                    return false;
                },
            ],
            [
                'allow' => true,
                'actions' => ['confirm', 'order-contacts', 'cancel-confirmation', 'reject', 'add-deleted'],
                'roles' => ['user'],
                'matchCallback' => function ($rule) {
                    $id = Yii::$app->request->get('id');
                    $model = $this->modelClass::findOne($id);
                    $object = Objects::findOne($model->object_id);

                    if ($object->user_id == Yii::$app->user->id) {
                        return true;
                    }
                    return false;
                },
            ],
            [
                'allow' => true,
                'actions' => ['for-moderation', 'approve', 'reject'],
                'roles' => ['moderator']
            ]
        ]);
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        $actions['index']['prepareSearchQuery'] = [$this, 'prepareSearchQuery'];
        $actions['archive'] = $actions['index'];
        $actions['archive']['prepareSearchQuery'] = [$this, 'prepareArchiveSearchQuery'];
        return $actions;
    }

    protected function verbs()
    {
        $verbs = parent::verbs();
        $verbs['reject'] = ['PUT', 'GET', 'PATCH'];
        $verbs['approve'] = ['PUT', 'GET', 'PATCH'];
        $verbs['for-moderation'] = ['GET'];
        return $verbs;
    }

    public function prepareSearchQuery()
    {
        $query = parent::prepareSearchQuery();
        if (Yii::$app->user->identity->role == User::ROLE_USER) {
            $query->andWhere(['or',['<>', 'order.rejected', 1], ['order.rejected' => null]]);
            $query->andWhere(['order.archive' => 0, 'order.deleted' => 0, 'order.status' => StatusInterface::APPROVED]);
        }
        return $query;
    }

    public function prepareArchiveSearchQuery()
    {
        $query = parent::prepareSearchQuery();
        if (Yii::$app->user->identity->role == User::ROLE_USER) {
            $query->andWhere(['or',['<>', 'order.rejected', 1], ['order.rejected' => null]]);
            $query->andWhere(['order.archive' => 1, 'order.deleted' => 0, 'order.status' => StatusInterface::APPROVED]);
        }
        return $query;
    }

    public function actionConfirm($id)
    {
        $model = $this->modelClass::findOne($id);
        if (isset($model)) {
            $model->invoice_id = 'order_' . $id;
            $model->confirmed = 1;
            if(PaymentHelper::confirmPayment($model->invoice_id)) {
                $model->save();
                return $model;
            } else {
                throw new ServerErrorHttpException('Возникла ошибка при оплате');
            }

        } else {
            throw new NotFoundHttpException('Заказ не найден');
        }
    }

    public function actionCancelConfirmation($id)
    {
        $model = $this->modelClass::findOne($id);
        if (isset($model)) {
            $model->confirmed = 0;
            $model->save();
            return $model;
        } else {
            throw new NotFoundHttpException('Заказ не найден');
        }
    }

    public function actionAddDeleted($id)
    {
        $model = $this->modelClass::findOne($id);
        if (isset($model)) {
            $model->deleted = 1;
            $model->save();
            return $model;
        } else {
            throw new NotFoundHttpException('Заказ не найден');
        }
    }

    public function actionOrderContacts($id)
    {
        $model = $this->modelClass::findOne($id);
        $user = User::find()->where(['id' => $model->user_id])->one();
        return $user;
    }

    public function actionGetConfirmed()
    {
        return $this->modelClass::find()
            ->joinWith('object')
            ->where(['confirmed' => 1])
            ->andWhere([
                'OR',
                ['order.user_id' => Yii::$app->user->id],
                ['object.user_id' => Yii::$app->user->id]
            ])
            ->andWhere(['archive' => 0, 'deleted' => 0, 'object.status' => StatusInterface::APPROVED])
            ->all();
    }

    public function actionAmountConfirmedByUser()
    {
        return $this->modelClass::find()
            ->joinWith('object')
            ->where(['confirmed' => 1])
            ->andWhere([
                'OR',
                ['order.user_id' => Yii::$app->user->id],
                ['object.user_id' => Yii::$app->user->id]
            ])
            ->andWhere(['archive' => 0, 'deleted' => 0, 'object.status' => StatusInterface::APPROVED])
            ->count();
    }

    public function actionAmountByUser()
    {
        return $this->modelClass::find()
            ->joinWith('object')
            ->where(['object.user_id' => Yii::$app->user->id])
            ->andWhere(['or',['<>', 'order.confirmed', 1], ['order.confirmed' => null]])
            ->andWhere(['or',['<>', 'order.rejected', 1], ['order.rejected' => null]])
            ->andWhere(['order.archive' => 0, 'order.deleted' => 0, 'order.status' => 1])
            ->andWhere(['<>','object.status', StatusInterface::DELETED])
            ->count();

    }
}
