<?php

namespace Classes\Models;

use Classes\Models\AbstractModels\DbModel;

class Category extends DbModel{

    // GETTERS

    public function getPlants(){
        return Plant::getAll("WHERE category_id = ".$this->getId());
    }

} 