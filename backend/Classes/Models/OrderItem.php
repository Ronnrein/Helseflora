<?php

namespace Classes\Models;

use Classes\Models\AbstractModels\DbModel;
use Classes\Config;

/**
 * Represents an order item in the database
 * Class OrderItem
 * @package Classes\Models
 */
class OrderItem extends DbModel{

    /**
     * Returns the plant of this order item
     * @return Plant
     */
    public function getPlant(){
        return new Plant($this->info['plant_id']);
    }

    /**
     * Returns the amount of this order item
     * @return int
     */
    public function getAmount(){
        return (int)$this->info['amount'];
    }

} 