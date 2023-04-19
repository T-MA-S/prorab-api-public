<?php

namespace app\components;

// use Yii;
// use yii\db\ActiveRecord;
// use common\components\AppActiveQuery as ActiveQuery;
use yii\web\ServerErrorHttpException;

class UploadedFile extends \yii\web\UploadedFile
{

    public function saveAs($file, $deleteTempFile = true)
    {
        $postdata = fopen($this->tempName, "r");
        $fp = fopen($file, "w");

        while ($data = fread($postdata, $this->size))
            if (fwrite($fp, $data)) {
                fclose($fp);
                fclose($postdata);
                return true;
            }
        throw new ServerErrorHttpException('Ошибка загрузки файла');
    }
}
