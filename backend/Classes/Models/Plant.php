<?php
/**
 * Created by PhpStorm.
 * User: Ronnrein
 * Date: 03.10.2014
 * Time: 16:44
 */

namespace Classes\Models;

use Classes\Models\AbstractModels\DbModel;
use Classes\Config;

class Plant extends DbModel{

    // GETTERS

    public function getImageFile(){
        return $this->info['bildefil'];
    }

    public function getImageSmallURL(){
        return substr(str_replace(":size:", Config::IMG_FOLDER_S, $this->getImageURL()), 0, -3).Config::IMG_EXT_S;
    }

    public function getImageLargeURL(){
        return substr(str_replace(":size:", Config::IMG_FOLDER_L, $this->getImageURL()), 0, -3).Config::IMG_EXT_L;
    }

    private function getImageURL(){
        return "http://".$_SERVER['HTTP_HOST'].strtok($_SERVER["REQUEST_URI"],'?').Config::IMG_FOLDER."/:size:/".$this->getImageFile();
    }

} 