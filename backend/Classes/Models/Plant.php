<?php

namespace Classes\Models;

use Classes\Models\AbstractModels\DbModel;
use Classes\Config;

class Plant extends DbModel{

    // GETTERS

    /**
     * Returns the price of this plant
     * @return float
     */
    public function getName(){
        return $this->info['name'];
    }

    public function getPrice(){
        return (float)$this->getInfo()['price'];
    }

    public function getImageFile(){
        return $this->info['image'];
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