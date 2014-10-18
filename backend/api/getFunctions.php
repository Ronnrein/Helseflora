<?php

namespace Api\CallableGetFunctions;

use Classes\Models\Category;
use Classes\Models\Plant;
use Classes\Models\Sale;
use Classes\Models\User;

// Here goes get functions that can get called by api

/**
 * Outputs all plants
 */
function getAllPlants(){
    $result = [];
    foreach(Plant::getAll("ORDER BY id ASC") as $plant){
        $info = $plant->getInfo();
        $info['imageUrlS'] = $plant->getImageSmallURL();
        $info['imageUrlL'] = $plant->getImageLargeURL();
        $result[] = $info;
    }
    return $result;
}

/**
 * Outputs all categories
 */
function getAllCategories(){
    $result = [];
    foreach(Category::getAll() as $category){
        $result[] = $category->getInfo();
    }
    return $result;
}

/**
 * Outputs all categories and plants
 */
function getCategoriesPlants(){
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
 * Outputs all sales
 */
function getAllSales(){
    $result = [];
    foreach(Sale::getAll() as $sale){
        $result[] = $sale->getInfo();
    }
    return $result;
}

/**
 * Outputs all sales and plants
 */
function getSalesPlants(){
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

function testArgs($args){
    echo "arg 'test' is: ".$args['test'];
}