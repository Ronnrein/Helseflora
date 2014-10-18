<?php

namespace Api\CallablePostFunctions;

use Classes\Models\Category;
use Classes\Models\Plant;
use Classes\Models\Sale;
use Classes\Models\User;
use Classes\Models\Session;
use Classes\Config;

// Here goes post functions that can get called by api

function setField($data){
    $class = "Classes\\Models\\".ucfirst(strtolower($data['table']));
    $inst = new $class($data['id']);
    $func = "set".ucfirst(strtolower($data['field']));
    $inst->$func($data['value']);
    return Config::STATUS_SUCCESS;
}

function logIn($data){
    $user = User::checkLogin($data['username'], $data['password']);
    if($user) {
        $user->createToken();
        return Config::STATUS_SUCCESS;
    } else{
        return Config::STATUS_ERROR;
    }
}