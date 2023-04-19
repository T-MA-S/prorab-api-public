<?php 

namespace app\components\traits;

use Yii;
use app\components\interfaces\StatusInterface as S;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\db\Exception;

/**
 * Trait with moderation functionality
 * 
 * @param array $allowedStatuses statuses what user can set without moderation
 * @param array $allowedAttributes attributes what user can set without moderation
 * @param array $moderadableAttributes attributes what user can`t set without moderation
 */
trait Moderadable
{
    protected $params;

    protected $allowedStatuses = [
        S::APPROVED, S::DEACTIVATED, S::DELETED
    ];

    protected static $moderadableTables = [
        'user', 'order', 'object', 'mark'
    ];

    protected $allowedAttributes = [
        'status', 'referal_status', 'referal_user_id', 'busy', 'working_hours_start', 'working_hours_end', 'status_busy', 'work_on_weekend'
    ];

    protected $moderadableAttributes = [
        'image', 'name', 'category_id', 'comment', 'about', 'address', 'avatar'
    ];

    /**
     * Check who is updating entity and set attributes
     * 
     * @return void
     */
    protected function moderate(): void
    {
        $this->params = \Yii::$app->request->getBodyParams();

        $user = true;
        if (isset(\Yii::$app->user->identity)) {
            if (\Yii::$app->user->identity->role !== 'user') {
                $user = false;
            }
        }

        if ($user || $this->isNewRecord) {
            if (!empty($this->params) || !empty($_FILES)) {
                $this->byUser();
            }
        } else {
            $this->byAdmin();
        }
    }

    /**
     * Set attributes to moderate entity
     * 
     * @return void
     */
    protected function byUser(): void
    {
        if($this->canChange()) {
            $this->status = array_key_exists('status', $this->params) ?  $this->params['status'] : $this->status;
        } else {
            $this->status = S::AWAITING;
        }
    }

    /**
     * Check is object can change without moderation
     * 
     * @return true if can
     * @return false if cant
     */
    private function canChange(): bool
    {
        if(
            $this->checkStatus() &&
            $this->checkAttributes()
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check is user can set status value
     * 
     * @return true if can
     * @return false if cant
     */
    private function checkStatus(): bool
    {
        if(!array_key_exists('status', $this->params)){
            return true;
        }

        if(!in_array($this->params['status'], $this->allowedStatuses)) {
            return false;
        }

        return true;
    }

    /**
     * Check is user can set attribute without moderation
     * 
     * Always to moderation if new record
     * 
     * @return true if can
     * @return false if cant
     */
    private function checkAttributes(): bool
    {
        if($this->isNewRecord){
            return false;
        }

        $oldAttributes = $this->getOldAttributes();

        foreach(array_keys($this->params) as $attr){
            if(
                !array_key_exists($attr, $oldAttributes) ||
                !in_array($attr, $this->moderadableAttributes)
            ) {
                continue;
            }

            if($oldAttributes[$attr] != $this->params[$attr]){
                return false;
            }
        }

        return true;
    }

    /**
     * Set status attributes as admin 
     * 
     * @return void
     */
    protected function byAdmin(): void
    {
        if(array_key_exists('status', $this->params)){
            if(!in_array(
                $this->params['status'],
                [S::AWAITING, S::APPROVED, S::REJECTED, S::DEACTIVATED, S::DELETED]
            )) {
                throw new BadRequestHttpException('Указан несуществующий статус');
            }
        }

        $this->status = array_key_exists('status', $this->params)? $this->params['status']: $this->status;
        $this->on_moderation = S::AWAITING;
        $this->moderator_id = Yii::$app->user->id;
    }

    /**
     * Check amounts of objects to moderation
     * 
     * @return array
     */
    public static function forModeration($entity = '')
    {
        $mId = Yii::$app->user->id;
        
        $oldObject = static::find()
            ->where([
                'status' => S::AWAITING,
                'on_moderation' => S::AWAITING,
                'moderator_id' => $mId
            ])
            ->one();

        $query = static::find()
            ->where(['or',
                ['status' => S::AWAITING],
                ['status' => null]
            ])
            ->andWhere(['or', 
                ['on_moderation' => S::AWAITING],
                ['on_moderation' => null]
            ]);
        
        $countQuery = 'SELECT SUM(t.cnt) as objectsAmount FROM (';

        $queries = [];
        foreach(self::$moderadableTables as $t) {
            $queries[] = "SELECT COUNT(`id`) as cnt FROM `{$t}` WHERE (`status` IS NULL OR `status` = 0) AND (`on_moderation` IS NULL OR `on_moderation` = 0)";
        }

        $countQuery .= implode(' UNION ', $queries) . ') t';

        $objectsAmount = Yii::$app->db->createCommand($countQuery)->queryScalar();

        if (isset($oldObject)) {
            return [
                'objectsAmount' => $objectsAmount + 1,
                'object' => $oldObject
            ];
        }

        if(!$objectsAmount){
            throw new NotFoundHttpException("Нет {$entity} для модерации");
        }

        $result = [
            'objectsAmount' => $objectsAmount,
            'object' => $query->one()
        ];

        if($result['object']){
            $result['object']->on_moderation = S::MODERATION_PROCESS;
            $result['object']->moderator_id = $mId;
            $result['object']->save(false);
        }

        return $result;
    }

    public static function approve($id)
    {
        try {
            $model = static::findOne($id);
            $model->status = S::APPROVED;
            $model->on_moderation = S::AWAITING;
            $model->save(false);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public static function reject($id)
    {
        try {
            $model = static::findOne($id);
            if(array_key_exists('rejected', $model->attributes)){
                $model->rejected = 1;
            }
            $model->status = S::REJECTED;
            $model->on_moderation = S::AWAITING;
            $model->save(false);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}