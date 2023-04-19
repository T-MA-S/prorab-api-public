<?php

namespace app\models;

use app\components\behaviors\NotificationBehavior;
use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "chat_message".
 *
 * @property int $id
 * @property int|null $from_user_id
 * @property int|null $to_user_id
 * @property string|null $text
 * @property int $viewed
 * @property string|null $created
 * @property string|null $view_time
 *
 * @property User $fromUser
 * @property User $toUser
 */
class ChatMessage extends \yii\db\ActiveRecord
{
    public $file;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
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
        return 'chat_message';
    }

    public function extraFields()
    {
        return ['files', 'fromUser' , 'toUser'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from_user_id', 'to_user_id', 'viewed'], 'integer'],
            [['text'], 'string'],
            [['created', 'view_time', 'file', 'uploadFile'], 'safe'],
            [['from_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['from_user_id' => 'id']],
            [['to_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['to_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from_user_id' => 'ID отправителя',
            'to_user_id' => 'ID получателя',
            'text' => 'Текст сообщения',
            'viewed' => 'Просмотрено',
            'created' => 'Дата создания',
            'view_time' => 'Время просмотра',
        ];
    }

    /**
     * Gets query for [[FromUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFromUser()
    {
        return $this->hasOne(User::class, ['id' => 'from_user_id']);
    }

    /**
     * Gets query for [[ToUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getToUser()
    {
        return $this->hasOne(User::class, ['id' => 'to_user_id']);
    }

    /**
     * Gets query for [[ChatMessageFile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(ChatMessageFile::className(), ['chat_message_id' => 'id']);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->from_user_id = Yii::$app->user->id;
        }
        $oldViewed = $this->getOldAttribute('viewed');
        if (!$oldViewed && $this->viewed) {
            $this->view_time = date('Y-m-d H:i:s', time());
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->file = UploadedFile::getInstanceByName('file');

        if ($this->file instanceof UploadedFile) {
            $this->saveFile($this->file);
        } else {
            $this->file = UploadedFile::getInstancesByName('file');
            foreach ($this->file as $file) {
                if ($file instanceof UploadedFile) {
                    $this->saveFile($file);
                }
            }
        }

        if (isset($this->to_user_id)) {
            $notificationMessage = "Вам пришло сообщение от техподдержки.";
            $this->sendNotification(Notification::TYPE_CHAT_MESSAGE, $notificationMessage, $this->to_user_id);
        }
    }

    public static function getLastMessage(?int $user_id = null)
    {
        if (empty($user_id)) {
            $user_id = Yii::$app->user->id;
        }
        return ChatMessage::find()
            ->where('from_user_id = :from_user_id OR to_user_id = :to_user_id', [
                ':from_user_id' => $user_id,
                ':to_user_id' => $user_id
            ])->orderBy(['id' => SORT_DESC])->one();
    }

    protected function saveFile($file){
        $chatFile = new ChatMessageFile();
        $chatFile->chat_message_id = $this->id;
        $chatFile->filename = $file->name;
        $chatFile->file = uniqid() . '.' . $file->extension;
        if (in_array($file->extension, ['jpeg', 'jpg', 'png', 'bmp'])) {
            $chatFile->type = 1;
        } else {
            $chatFile->type = 0;
        }

        if ($chatFile->save()) {
            $file->saveAs(\Yii::getAlias('@webroot') . '/uploads/' . $chatFile->file);
        }
    }
}
