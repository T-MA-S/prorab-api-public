<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "chat_message_file".
 *
 * @property int $id
 * @property int|null $chat_message_id
 * @property string|null $file
 * @property string|null $filename
 * @property int|null $type
 *
 * @property ChatMessage $chatMessage
 */
class ChatMessageFile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'chat_message_file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['chat_message_id', 'type'], 'integer'],
            [['filename', 'file'], 'string', 'max' => 255],
            [['chat_message_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChatMessage::class, 'targetAttribute' => ['chat_message_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chat_message_id' => 'Chat Message ID',
            'filename' => 'File',
            'filename' => 'Filename',
            'type' => 'Type',
        ];
    }

    /**
     * Gets query for [[ChatMessage]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChatMessage()
    {
        return $this->hasOne(ChatMessage::class, ['id' => 'chat_message_id']);
    }
}
