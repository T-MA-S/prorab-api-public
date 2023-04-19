<?php

namespace app\models;

use app\components\interfaces\StatusInterface;
use app\components\traits\Moderadable;
use Yii;
use app\components\validators\MarkExistValidator;
use app\components\validators\StopWordValidator;
use app\components\validators\SelfMarkValidator;
use yii\db\Query;


/**
 * This is the model class for table "mark".
 *
 * @property int $id
 * @property int|null $user_from_id
 * @property int|null $user_to_id
 * @property float|null $mark
 * @property string|null $date
 * @property string|null $comment
 *
 * @property User $userFrom
 * @property User $userTo
 */
class Mark extends \yii\db\ActiveRecord implements StatusInterface
{
    use Moderadable;

    public static function tableName()
    {
        return 'mark';
    }

    public function extraFields()
    {
        return ['userFrom', 'userTo'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_from_id', 'user_to_id', 'mark'], 'required'],
            [['user_from_id', 'user_to_id', 'mark', 'status'], 'integer'],
            [['date', 'comment'], 'safe'],

            ['comment', StopWordValidator::class],
            ['user_from_id', MarkExistValidator::class],
            ['user_from_id', SelfMarkValidator::class],

            [['user_from_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_from_id' => 'id']],
            [['user_to_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_to_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_from_id' => 'ID от кого оценка',
            'user_to_id' => 'ID кому оценка',
            'mark' => 'Оценка',
            'date' => 'Дата',
            'comment' => 'Комментарий',
            'status' => 'Статус'
        ];
    }

    /**
     * Gets query for [[UserFrom]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserFrom()
    {
        return $this->hasOne(User::class, ['id' => 'user_from_id']);
    }

    /**
     * Gets query for [[UserTo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserTo()
    {
        return $this->hasOne(User::class, ['id' => 'user_to_id']);
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $date = new \DateTime();
            $this->status = self::AWAITING;
            $this->date = $date->format('Y-m-d H:i:s');
        }

        $this->moderate();

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $user = $this->userTo;
        $user->setMark();
        $user->save();
    }

    public static function userMarks($type, $relation)
    {
        $t = self::tableName();

        $query = (new Query())
            ->select("{$t}.id, user_from_id, user_to_id, {$t}.mark, date, comment, user.name as name, user.avatar as avatar")
            ->from($t)
            ->leftJoin('user', "user.id = {$t}.{$relation}")
            ->where("{$type} = :id", [':id' => Yii::$app->user->id])
            ->andWhere('mark.status = :s', [':s'=>self::APPROVED]);

        return $query->all();
    }
}
