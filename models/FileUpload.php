<?php

namespace kriss\models;

use yii\base\Model;
use yii\web\UploadedFile;
use Yii;

class FileUpload extends Model
{
    /**
     * @var UploadedFile
     */
    public $file;
    /**
     * @var string
     */
    public $fileLabel = '上传文件';
    /**
     * 最大大小
     * @var string
     */
    public $maxSize;
    /**
     * 扩展名，逗号隔开
     * @var string
     */
    public $extensions;
    /**
     * 保存路径
     * @var string
     */
    public $savePath = 'uploads/';
    /**
     * 是否使用 http url
     * @var bool
     */
    public $useHttpUrl = true;
    /**
     * 是否是多个文件
     * @var bool
     */
    public $multi = false;

    public function rules()
    {
        if (!$this->multi) {
            $fileRule = [
                ['file'], 'file', 'skipOnEmpty' => false
            ];
            if ($this->maxSize) {
                $fileRule += ['maxSize' => $this->maxSize];
            }
            if ($this->extensions) {
                $fileRule += ['extensions' => $this->extensions];
            }
        } else {
            $fileRule = [
                ['file'], 'each', 'rule' => ['file', 'skipOnEmpty' => false]
            ];
            if ($this->maxSize) {
                $fileRule['rule'] += ['maxSize' => $this->maxSize];
            }
            if ($this->extensions) {
                $fileRule['rule'] += ['extensions' => $this->extensions];
            }
        }

        return [
            $fileRule
        ];
    }

    public function attributes()
    {
        return [
            'file' => $this->fileLabel
        ];
    }

    /**
     * 上传的入口
     * @param $requestName
     * @param $prefix
     * @return array|bool|string
     */
    public function upload($requestName, $prefix)
    {
        if (!$this->multi) {
            return $this->uploadOneFile($requestName, $prefix);
        } else {
            return $this->uploadMultiFiles($requestName, $prefix);
        }
    }

    /**
     * 上传单个文件
     * @return bool|string
     */
    protected function uploadOneFile($requestName, $prefix)
    {
        $this->file = UploadedFile::getInstanceByName($requestName);
        if ($this->validate()) {
            return $this->runUploadOne($this->file, $prefix);
        }
        return false;
    }

    /**
     * 上传多个文件
     * @param $requestName
     * @return array|bool
     */
    protected function uploadMultiFiles($requestName, $prefix)
    {
        $this->file = UploadedFile::getInstancesByName($requestName);
        $files = $this->file;
        if ($this->validate()) {
            $fileNames = [];
            foreach ($files as $file) {
                $fileNames[] = $this->runUploadOne($file, $prefix);
            }
            return $fileNames;
        }
        return false;
    }

    /**
     * 上传一个
     * @param $file UploadedFile
     * @param $prefix
     * @return bool|string
     */
    protected function runUploadOne($file, $prefix)
    {
        $fileName = uniqid($prefix) . '.' . $file->extension;
        $savePath = $this->savePath . $fileName;
        $file->saveAs($savePath);
        if ($this->useHttpUrl) {
            return Yii::$app->request->hostInfo . '/' . $savePath;
        }
        return $savePath;
    }
}