<?php

namespace Classes\Models;

use Classes\Models\AbstractModels\DbModel;
use Classes\Config;

/**
 * Represents a plant in the database
 * Class Plant
 * @package Classes\Models
 */
class Plant extends DbModel{

    /**
     * Returns the name of this plant
     * @return string
     */
    public function getName(){
        return $this->info['name'];
    }

    /**
     * Returns the price of this plant
     * @return float
     */
    public function getPrice(){
        return (float)$this->getInfo()['price'];
    }

    /**
     * Returns the amount in stock of this plant
     * @return int
     */
    public function getStock(){
        return (int)$this->getInfo()['stock'];
    }

    /**
     * Sets the amount in stock of this plant
     * @param int $amount
     */
    public function setStock($amount){
        $this->setField("stock", $amount);
    }

    /**
     * Returns the image filename of this plant
     * @return string
     */
    public function getImageFile(){
        return $this->info['image'];
    }

    /**
     * Returns the full url to this plants small image
     * @return string
     */
    public function getImageSmallURL(){
        return substr(str_replace(":size:", Config::IMG_FOLDER_S, $this->getImageURL()), 0, -3).Config::IMG_EXT_S;
    }

    /**
     * Returns the full url to this plants large image
     * @return string
     */
    public function getImageLargeURL(){
        return substr(str_replace(":size:", Config::IMG_FOLDER_L, $this->getImageURL()), 0, -3).Config::IMG_EXT_L;
    }

    /**
     * Private method providing url methods with a string to run str_replace on
     * @return string
     */
    private function getImageURL(){
        return Config::getUrl().Config::IMG_FOLDER."/:size:/".$this->getImageFile();
    }

} 