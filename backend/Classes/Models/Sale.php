<?php

namespace Classes\Models;

use Classes\Models\AbstractModels\DbModel;

class Sale extends DbModel{

    // GETTERS

    /**
     * Gets the percentage of this sale
     * @return float
     */
    public function getPercentage(){
        return (float)$this->getInfo()['percent'];
    }

    /**
     * Gets the new price of the plant
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