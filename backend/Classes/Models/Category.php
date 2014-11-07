<?php

namespace Classes\Models;

use Classes\Models\AbstractModels\DbModel;

class Category extends DbModel{

    // GETTERS

    public function getPlants($reserved = false){
        $appendix = $reserved ? "" : " AND reserved=0";
        return Plant::getAll("WHERE category_id = ".$this->getId().$appendix);
    }

} 