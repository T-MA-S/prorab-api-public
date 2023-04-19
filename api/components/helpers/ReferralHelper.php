<?php

namespace app\components\helpers;

class ReferralHelper
{
    const KEY_REFERRER_ID = 'ref_id';

    public static function setReferrerId(string $refId)
    {
        $ref = static::getReferrerId();

        if($ref === null && $ref !== $refId) {
            \Yii::$app->session->set(self::KEY_REFERRER_ID, $refId);
        }
    }

    public static function getReferrerId()
    {
        return \Yii::$app->session->get(self::KEY_REFERRER_ID, null);
    }

    public static function removeReferrer()
    {
        return \Yii::$app->session->remove(self::KEY_REFERRER_ID);
    }
}