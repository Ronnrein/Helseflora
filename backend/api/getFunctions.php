<?php

namespace Api\CallableGetFunctions;

use Classes\Models\Category;
use Classes\Models\Plant;
use Classes\Models\Sale;
use Classes\Models\Session;
use Classes\Models\User;
use Classes\Config;

// Here goes get functions that can get called by api

/**
 * Returns all plants (Override to add image urls)
 * @return array
 */
function getAllPlants($data){
    $result = [];
    $array = null;
    if(isset($data['category'])){
        try{
            $cat = new Category($data['category']);
            $array = $cat->getPlants();
        } catch(\PDOException $e){
            return Config::STATUS_ERROR;
        }
    } else{
        $array = Plant::getAll("ORDER BY id ASC");
    }
    foreach($array as $plant){
        if(isset($data['simple']) && $data['simple'] == "true"){
            $result[] = array("id" => $plant->getId(), "name" => $plant->getName());
        }
        else{
            $info = $plant->getInfo();
            $info['imageUrlS'] = $plant->getImageSmallURL();
            $info['imageUrlL'] = $plant->getImageLargeURL();
            $result[] = $info;
        }
    }
    return $result;
}

function getPlant($data){
    $plant = new Plant((int)$data['id']);
    $info = $plant->getInfo();
    $info['imageUrlS'] = $plant->getImageSmallURL();
    $info['imageUrlL'] = $plant->getImageLargeURL();
    return $info;
}

/**
 * Used to get all of a table/class
 * @param $data GET data
 * @return array|null
 */
function getAll($data){
    $className = ucfirst(strtolower($data['what']));
    $funcName = rtrim("Api\\CallableGetFunctions\\getAll".$className, "s")."s";
    $classNameFull = rtrim("Classes\\Models\\".$className, "s");

    // If a function already exists for requested table/class, call it, else if requested class exists, return its data
    if(function_exists($funcName)){
        return $funcName($data);
    } else if(class_exists($classNameFull)){
        $result = [];
        foreach($classNameFull::getAll() as $obj){
            $result[] = $obj->getInfo();
        }
        return $result;
    }
    return Config::STATUS_ERROR;
}

function get($data){
    $className = ucfirst(strtolower($data['what']));
    $funcName = "Api\\CallableGetFunctions\\get".$className;
    $classNameFull = "Classes\\Models\\".$className;

    // If a function already exists for requested table/class, call it, else if requested class exists, return its data
    if(function_exists($funcName)){
        return $funcName($data);
    } else if(class_exists($classNameFull)){
        $obj = new $classNameFull((int)$data['id']);
        return $obj->getInfo();
    }
    return Config::STATUS_ERROR;
}

/**
 * Get all categories, and plants in those categories
 * @return array
 */
function getAllCategoriesplants($data){
    $result = [];
    foreach(Category::getAll() as $category){
        $subResult = $category->getInfo();
        foreach($category->getPlants() as $plant){
            $subResult['plants'][] = $plant->getInfo();
        }
        $result[] = $subResult;
    }
    return $result;
}

/**
 * Get all sales and the plant which the sale is for
 * @return array
 */
function getAllSalesplants($data){
    $result = [];
    foreach(Sale::getAll() as $sale){
        $subResult = $sale->getInfo();
        $plant = $sale->getPlant();
        $subResult['plant'] = $plant->getInfo();
        $subResult['plant']['newPrice'] = $sale->getNewPrice();
        $result[] = $subResult;
    }
    return $result;
}

function test($data){
    $session = Session::getByToken($data['token']);
    $user = $session->getUser();
    return $session->isValid();
}