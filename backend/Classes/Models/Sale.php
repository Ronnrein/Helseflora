<?php

namespace Classes\Models;

use Classes\Models\AbstractModels\DbModel;

/**
 * Represents a sale in the database
 * Class Sale
 * @package Classes\Models
 */
class Sale extends DbModel{

    /**
     * Returns the percentage of this sale
     * @return float
     */
    public function getPercentage(){
        return (float)$this->getInfo()['percent'];
    }

    /**
     * Returns the new price of the plant after sale percentage
     * @return float
     */
    public function getNewPrice(){
        $price = $this->getPlant()->getPrice();
        return (float)$price-(($this->getPercentage()/100)*$price);
    }

    /**
     * Returns the plant of this sale
     * @return Plant
     */
    public function getPlant(){
        return new Plant($this->info['plant_id']);
    }

} 