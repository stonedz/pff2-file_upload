<?php

namespace pff\modules;


use pff\Abs\AModule;
use pff\Exception\PffException;
use pff\Iface\IConfigurableModule;

/**
 * Class Pff2FileUpload
 * @package pff\modules
 */
class Pff2FileUpload extends AModule implements IConfigurableModule {

    private $fileDir;
    private $validMimeTypes;

    public function __construct($confFile = 'pff2-file_upload/module.conf.local.yaml') {
        $this->loadConfig($confFile);

    }

    /**
     * @param array $parsedConfig
     * @return mixed
     */
    public function loadConfig($parsedConfig) {
        $conf = $this->readConfig($parsedConfig);
        $this->fileDir = ROOT. DS . 'app' . DS . 'public'. DS.$conf['moduleConf']['dir'] .DS;
        $this->validMimeTypes = $conf['moduleConf']['validMimeTypes'];
    }


    /**
     * Salva il file
     *
     * @param $fileArray
     * @param $add_uniqueid If true adds an uniqueid as a prefix
     * @throws PffException
     * @return bool|string
     */
    public function saveFile($fileArray, $add_uniqueid = true) {
        $tmp_file      = $fileArray['tmp_name'];
        $name          = $fileArray['name'];
        if($add_uniqueid) {
          $new_name      = uniqid().$name;
        }
        else {
          $new_name      = $name;
        }
        $new_full_name = $this->fileDir.$new_name;

        if(!$this->checkMimeType($fileArray['type'])) {
            return false;
        }

        if(! move_uploaded_file($tmp_file, $new_full_name)) {
            throw new PffException('Error uploading the file', 500);
        }

        return $new_name;
    }

    /**
     *
     * @param string $fileMimeType
     * @return bool
     */
    public function checkMimeType($fileMimeType) {
        if(in_array($fileMimeType, $this->validMimeTypes)) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * @param string $fileName
     * @return bool
     */
    public function deleteFile($fileName) {

        if(file_exists($this->fileDir.$fileName)) {
            unlink($this->fileDir.$fileName);
            return true;
        }
        else {
            return false;
        }

    }
}
