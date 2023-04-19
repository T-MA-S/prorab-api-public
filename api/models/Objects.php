<?php

namespace app\models;

use app\components\behaviors\NotificationBehavior;
use app\components\behaviors\UpdateTimeBehavior;
use app\components\behaviors\FilesBehavior;
use app\components\interfaces\BusyInterface;
use app\components\interfaces\StatusInterface;
use app\components\traits\Moderadable;
use app\models\ScheduleIsBusy;
use app\models\Image;
use Yii;

/**
 * This is the model class for table "object".
 *
 * @property int $id
 * @property string|null $image
 * @property string|null $name
 * @property string|null $model
 * @property string|null $about
 * @property int|null $price_1
 * @property int|null $price_2
 * @property int|null $quantity
 * @property int $status_busy
 * @property int $status
 * @property int $active
 * @property int|null $user_id
 * @property int|null $city_id
 * @property int $type
 * @property int|null $category_id
 * @property string|null $created
 * @property string|null $updated
 * @property array|string $schedule
 * @property int|null $on_moderation
 * @property int $schedule_type
 * @property int $work_on_weekend
 * @property int $moderator_id
 *
 * @property AdditionalCategory[] $additionalCategories
 * @property Category $category
 * @property City $city
 * @property Complaint[] $complaints
 * @property Favourites[] $favourites
 * @property ScheduleIsBusy[] $scheduleIsBusies
 * @property User $user
 */

class Objects extends \yii\db\ActiveRecord implements StatusInterface, BusyInterface
{
    use Moderadable;

    const SCENARIO_UPDATE = 'update';

    const FREE = 0;
    const BUSY = 1;
    const SOON = 2;

    public $schedule;
    public $price_1_name;
    public $price_2_name;
    public $amountInFavourites;
    public $amountOfConfirmedOrders;
    public $amountOfOrders;
    public $userFavourite;

    protected $oldStatus;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['files'] = [
            'class' => FilesBehavior::class,
            'attribute' => 'image'
        ];
        $behaviors['updateTime'] = [
            'class' => UpdateTimeBehavior::class
        ];
        $behaviors['notification'] = [
            'class' => NotificationBehavior::class,
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'object';
    }

    public function fields()
    {
        $fields = parent::fields();

        if (Yii::$app->controller->action->id == 'search') return $fields;

        unset($fields['image']);

        $fields['image'] = function ($model) {
            return Image::find()->where(['model' => 'object', 'model_id' => $this->id])->one();
        };

        $fields['price_1_name'] = function ($model) {
            return Category::getPriceName($this->category_id, 1);
        };

        $fields['price_2_name'] = function ($model) {
            return Category::getPriceName($this->category_id, 2);
        };

        if (isset(Yii::$app->user->id)) {
            $fields['userFavourite'] = function ($model) {
                return Favourites::find()->where(['object_id' =>$this->id, 'user_id' =>Yii::$app->user->id])->all();
            };
        }

        if (in_array(Yii::$app->controller->action->id, ['user-objects'])) {
            $fields['amountInFavourites'] = function ($model) {
                return Favourites::find()->where(['object_id' =>$this->id])->count();
            };
            $fields['amountOfConfirmedOrders'] = function ($model) {
                return Order::find()->where([
                    'object_id' =>$this->id,
                    'confirmed' => 1
                ])
                    ->andWhere(['or',['<>', 'order.rejected', 1], ['order.rejected' => null]])
                    ->andWhere(['order.archive' => 0, 'order.deleted' => 0])
                    ->count();
            };
            $fields['amountOfNewOrders'] = function ($model) {
                return Order::find()->where([
                    'object_id' =>$this->id,
                ])
                    ->andWhere(['or',['<>', 'order.confirmed', 1], ['order.confirmed' => null]])
                    ->andWhere(['or',['<>', 'order.rejected', 1], ['order.rejected' => null]])
                    ->andWhere(['order.archive' => 0, 'order.deleted' => 0, 'order.status' => 1])
                    ->count();
            };
        }

        return $fields;
    }

    public function extraFields()
    {
        return ['category', 'user', 'city', 'scheduleIsBusies', 'images', 'favourites'];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['update'] = array_merge(array_keys($this->attributes), ['schedule']) ;
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'city_id'], 'required'],
            [['about'], 'string'],
            [['price_1', 'price_2', 'quantity', 'status_busy', 'status', 'active', 'user_id', 'city_id'], 'integer'],
            [['type', 'category_id', 'on_moderation', 'moderator_id', 'work_on_weekend'], 'integer'],
            [['created', 'updated', 'schedule'], 'safe'],
            [['image', 'name', 'model'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'image' => 'Изображение',
            'name' => 'Название',
            'model' => 'Модель',
            'about' => 'Описание',
            'price_1' => 'Цена 1',
            'price_2' => 'Цена 2',
            'quantity' => 'Количество',
            'status_busy' => 'Статус занятости',
            'status' => 'Статус',
            'active' => 'Статус активности',
            'user_id' => 'ID пользователя',
            'city_id' => 'ID города',
            'type' => 'Тип',
            'category_id' => 'ID категории',
            'mark' => 'Оценка',
            'created' => 'Дата создания',
            'updated' => 'Дата обновления',
        ];
    }

    /**
     * Gets query for [[AdditionalCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdditionalCategories()
    {
        return $this->hasMany(AdditionalCategory::class, ['object_id' => 'id']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * Gets query for [[Complaints]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComplaints()
    {
        return $this->hasMany(Complaint::class, ['object_id' => 'id']);
    }

    /**
     * Gets query for [[Favourites]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFavourites()
    {
        return $this->hasMany(Favourites::class, ['object_id' => 'id']);
//            ->where(['or',['favourites.user_id' => Yii::$app->user->id],['favourites.user_id' => null]]);
    }

    /**
     * Gets query for [[ScheduleIsBusies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScheduleIsBusies()
    {
        return $this->hasMany(ScheduleIsBusy::class, ['object_id' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        if (!(Yii::$app->user->id)) {
            return [];
        }

        return $this->hasMany(Order::class, ['object_id' => 'id']);
    }

    /**
     * Gets query for [[Bookings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getConfirmedOrders()
    {
        return $this->hasMany(Order::class, ['object_id' => 'id'])->where('confirmed = 1');
    }

    public function getImages()
    {
        return $this->hasMany(Image::class, ['model_id' => 'id'])->where(['OR', ['image.model' => 'object'], ['image.model' => null]]);
    }

    public static function getStatusBusy($id)
    {
        $object = static::findOne($id);
        if ($object->status == StatusInterface::DEACTIVATED || $object->user->busy) {
            return self::BUSY;
        }
        $today = new \DateTime('now');
        if ($today->format('H:i') < $object->user->working_hours_start || $today->format('H:i') > $object->user->working_hours_end) {
            return self::BUSY;
        }
        $result = self::FREE;
        $bookings = [];
        foreach ($object->confirmedOrders as $order) {
            $bookings = array_merge($bookings, $order->bookings);
        }
        $busyDates = array_merge($bookings, $object->scheduleIsBusies);
        foreach ($busyDates as $date) {
            $begin = new \DateTime($date['date_from']);
            $end = new \DateTime($date['date_from']);
            $end = $end->modify('+' . ($date['duration'] - 1) . ' day');
            if ($end->format('Y-m-d') > $today->format('Y-m-d')) {
                $interval = new \DateInterval('P1D');
                $dateRange = new \DatePeriod($begin, $interval, $end);
                foreach ($dateRange as $dataItem) {
                    if ($today->format('Y-m-d') == $dataItem->format('Y-m-d')) {
                        $result = self::BUSY;
                    }
                }
            } elseif ($end->format('Y-m-d') == $today->format('Y-m-d')) {
                $result = self::SOON;
            }
        }
        return $result;
    }

    public function setMark()
    {
        $this->mark = round($this->user->getAvgMark(), 1);
    }

    public function afterDelete()
    {
        ContactPayment::deleteAll(['entity_id' => $this->id, 'entity' => 'object']);

        return parent::afterDelete();
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created = date('Y-m-d H:i:s', time());
        }

        if (empty($this->user_id)) {
            $this->user_id = \Yii::$app->user->id;
        }

        $this->updated = date('Y-m-d H:i:s', time());

        $this->moderate();

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->schedule = json_decode($this->schedule);

        if (is_array($this->schedule)) {
            ScheduleIsBusy::saveFromArray($this->id, $this->schedule);
        }

        if (
            array_key_exists('status', $changedAttributes)
            && $changedAttributes['status'] != $this->status
            && in_array($this->status, [static::APPROVED, static::REJECTED])
        ) {
            if ($this->status == 1) {
                if ($changedAttributes['status'] == 3) {
                    $notificationMessage = "Объявление " . $this->name . " свободно для заявок.";
                    foreach ($this->favourites as $favourite) {
                        $this->sendNotification(Notification::TYPE_OBJECT_ACTIVATED, $notificationMessage, $favourite->user_id);
                    }
                } else {
                    $notificationMessage = "Ваше объявление прошло модерацию и отображается в каталоге сервиса Прораб";
                    $this->sendNotification(Notification::TYPE_OBJECT_APPROVED, $notificationMessage, $this->user_id);
                }
            } else {
                $notificationMessage = "Ваше объявление " . $this->name . " не соответствует правилам сервиса, исправьте объявление";
                $this->sendNotification(Notification::TYPE_OBJECT_REJECTED, $notificationMessage, $this->user_id);
            }
            if (Yii::$app->user->identity->role == 'moderator') {
                $statistics = new ModeratorStatistics();
                $statistics->moderator_id = Yii::$app->user->id;
                $statistics->model = 'object';
                $statistics->model_id = $this->id;
                $statistics->save();
            }
        }
    }
}
