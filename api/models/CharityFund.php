<?php

namespace app\models;

use app\components\behaviors\FileBehavior;
use Yii;
// use app\components\UploadFileFromApi as FileAPi;

/**
 * This is the model class for table "charity_fund".
 *
 * @property int $id
 * @property int|null $active
 * @property string|null $title
 * @property string|null $description
 * @property string|null $link
 * @property string|null $logo
 * @property string|null $image
 */
class CharityFund extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['logo'] = [
            'class' => FileBehavior::class,
            'attribute' => 'logo'
        ];
        $behaviors['image'] = [
            'class' => FileBehavior::class,
            'attribute' => 'image'
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'charity_fund';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['active'], 'integer'],
            [['description'], 'string'],
            [['title', 'link', 'image', 'logo'], 'string', 'max' => 255],
        ];
    }

    // public function beforeSave($insert)
    // {
    //     $this->uploadFiles = new FileAPi(Yii::getAlias('@webroot') . '/uploads/charityFund/');
    //     if($this->checkFiles()){
    //         $this->uploadFiles->runUpload();
    //     }

    //     parent::beforeSave($insert);

    //     return true;
    // }

    // public function afterDelete()
    // {
    //     $path = Yii::getAlias('@webroot') . '/uploads/charityFund/';
    //     unlink($path . $this->image);
    //     unlink($path . $this->logo);
    // }

    // protected function checkFiles()
    // {
    //     if(!$this->isNewRecord) {
    //         if($this->getOldAttribute('image') !== $this->image['name']){
    //             $this->image = $this->uploadFiles->setFile($this->image['name'], $this->image['body']);
    //         }
    //         if($this->getOldAttribute('logo') !== $this->logo['name']){
    //             $this->logo = $this->uploadFiles->setFile($this->logo['name'], $this->logo['body']);
    //         }
    //         return true;
    //     }

    //     $this->image = $this->uploadFiles->setFile($this->image['name'], $this->image['body']);
    //     $this->logo = $this->uploadFiles->setFile($this->logo['name'], $this->logo['body']);

    //     return true;
    // }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'active' => 'Active',
            'title' => 'Title',
            'description' => 'Description',
            'link' => 'Link',
            'logo' => 'Logo',
            'image' => 'Image',
        ];
    }
}
