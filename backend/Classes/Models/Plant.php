<?php
/**
 * Created by PhpStorm.
 * User: Ronnrein
 * Date: 03.10.2014
 * Time: 16:44
 */

namespace Classes\Models;

use Classes\Models\AbstractModels\DbModel;

class Plant extends DbModel{

    /**
     * Name of table
     * @var string
     */
    protected static $table = "plant";

    // GETTERS

    /**
     * Get name of plant
     * @return string
     */
    public function getName(){
        return $this->info['navn'];
    }

} 