<?php
/**
 * Created by PhpStorm.
 * User: Ronnrein
 * Date: 03.10.2014
 * Time: 16:40
 */
namespace Api\CallableFunctions;

require_once("api.php");

use Classes\Models\Plant;
use Classes\DB;

// Here goes functions that can get called by api

function getAllPlants(){
    $result = [];
    foreach(Plant::getAll("ORDER BY id ASC") as $plant){
        $info = $plant->getInfo();
        $info['imageUrlS'] = $plant->getImageSmallURL();
        $info['imageUrlL'] = $plant->getImageLargeURL();
        $result[] = $info;
    }
    \Api\output($result);
}

function getCategories(){
    $db = DB::getInstance();
    $stmt = $db->prepare("SELECT DISTINCT kategori FROM plant");
}