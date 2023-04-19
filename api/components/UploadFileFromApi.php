<?php

namespace app\components;

use yii\web\ServerErrorHttpException;

class UploadFileFromApi
{
    protected $files = [];
    protected $pathTo;

    /**
     * Set path to save file from api service
     * 
     * @param string $path
     * 
     */
    public function __construct($path)
    {
        if(!is_dir($path)){
            mkdir($path);
        }
        $this->pathTo = $path;
    }
    

    /**
     * Set filename and body of the file to be uploaded
     * 
     * @param string $fileName
     * @param string $body gzencoded file's body
     * 
     * @return string name of file
     */
    public function setFile($fileName, $body)
    {
        $this->files[] = [
            'fileName' => $fileName,
            'body' => $body
        ];

        return $fileName;
    }

    /**
     * Run writing files
     * 
     * @return bool
     */
    public function runUpload(): bool
    {
        foreach($this->files as $file){
            $this->save($file['fileName'], $file['body']);
        }

        return true;
    }

    protected function save($fileName, $body): bool
    {
        if(file_put_contents($this->pathTo . $fileName, gzdecode($body))){
            return true;
        };

        return false;
    }
}
