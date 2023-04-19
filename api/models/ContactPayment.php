<?php

namespace app\models;

use Yii;
use yii\web\HttpException;

/**
 * This is the model class for table "contact_payment".
 *
 * @property int $id
 * @property string $entity
 * @property int $user_id
 * @property int $entity_id
 *
 * @property User $user
 */
class ContactPayment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contact_payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'entity', 'entity_id'], 'required'],
            [['user_id', 'entity', 'entity_id'], 'safe'],
            ['user_id', 'paymentExist'],
            [['user_id', 'entity_id'], 'integer']
        ];
    }

    public function paymentExist($attribute, $params, $validator)
    {
        if ($this->isNewRecord) {
            $payment = static::find()->where([
                'user_id' => Yii::$app->user->id,
                'entity' => $this->entity,
                'entity_id' => $this->entity_id
            ]);
            if ($payment->one()) {
                $this->addError($attribute, 'Оплата контактов уже существует');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'entity' => 'Название оплаченного обьекта',
            'user_id' => 'User ID',
            'entity_id' => 'ID оплаченного обьекта',
        ];
    }

    public function payContact($payData)
    {
        $transaction = $this::getDb()->beginTransaction();
        
        try {
            $id = defined('YII_DEBUG') ? \Yii::$app->params['debugUserId'] : \Yii::$app->user->id;

            $user = User::findOrFail($id);

            $payData['user_id'] = $id;
            $payData['amount'] = Yii::$app->params['contactPayment'];
            $payData['appointment'] = 'Оплата контактных данных для обьекта ' . $payData['entity_id'];
           
            try {
                $user->pay($payData);
            } catch (\Throwable $e) {
                throw new HttpException(402, $e->getMessage(), 402);
            }

            $this->setAttributes($payData);

            if(!$this->validate()){
                throw new HttpException(402, json_encode($this->getErrors()), 402);
            }

            $this->save();

            $transaction->commit();

            return true;

        } catch (HttpException $e) {
            $transaction->rollback();
            throw new HttpException(402, $e->getMessage(), 402);
        } catch (\Throwable $e) {
            $transaction->rollback();
            throw new HttpException(402, $e->getMessage(), 402);
        }

        return false;
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
}
