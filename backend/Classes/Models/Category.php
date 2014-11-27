<?php

namespace Classes\Models;

use Classes\Models\AbstractModels\DbModel;

/**
 * Represents a category in the database
 * Class Category
 * @package Classes\Models
 */
class Category extends DbModel{

    /**
     * Get Plants of this category
     * @param bool $reserved Should reserved plants be included or not
     * @return $this[]
     */
    public function getPlants($reserved = false){
        $appendix = $reserved ? "" : " AND reserved=0";
        return Plant::getAll("WHERE category_id = ".$this->getId().$appendix);
    }

} 