<?php

namespace app\components\validators;

use Yii;
use \yii\db\Query;
use app\models\Dictionary;
use yii\caching\DbDependency;
use yii\validators\Validator;

/**
 * @property array $punctuations
 * @property string $text
 */

class StopWordValidator extends Validator
{
    protected array $chars = ['.', ',', '...', '!', '|', '?', ':', ';', ' ', '<', '>'];

    protected string $text;

    protected function prepareText($text)
    {
        $this->text = str_replace($this->chars, '', $text);
    }

    public function validateAttribute($model, $attribute)
    {
        $table = Dictionary::tableName();

        if(!$stopWordsPattern = Yii::$app->cache->get('stop_words')){

            $table = Dictionary::tableName();

            $queries = (new Query())->select('word')->from($table)->all();

            if($queries){
                $words = [];
                foreach($queries as $q){
                    $words[] = $q['word'];
                }

                $stopWordsPattern = "/(" . implode('|', $words) . ")/";

                $dependency = new DbDependency([
                    'sql' => 'SELECT COUNT(id) FROM ' . $table
                ]);

                Yii::$app->cache->set('stop_words', $stopWordsPattern, 3600*24, $dependency);
            }
        }

        $this->prepareText($model->$attribute);

//        while(empty($mathces)){
//            preg_match($stopWordsPattern, $this->text, $mathces);
//        }
        if ($stopWordsPattern) {
            preg_match($stopWordsPattern, $this->text, $mathces);
        }

        if(!empty($mathces)){
            $this->addError($model, $attribute, "Поле содержит недопустимые слова");
        }
    }
}