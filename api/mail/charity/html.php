<?php
/** @var app\models\Charity $model */
?>

<h1><?= $model->fio ?> заполнил форму на сайте <?= Yii::$app->params['siteUrl'] ?></h1>
<div>
    <h4>Данные из формы:</h4>
    <?php foreach ($model->attributes as $name => $attribute) {
        if ($name == 'id') continue;
        echo '<p>' . $model->getAttributeLabel($name) . ': ' . $attribute . '</p>';
    }?>
</div>
