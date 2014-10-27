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
        $session = $user->createToken();
        return array("username" => $user->getUsername(), "token" => $session->getToken(), "access" => $user->getAccessId());
    } else{
        return Config::STATUS_ERROR;
    }
}

function logOut($data){
    Session::getByToken($data['sessionToken'])->delete();
}

function checkToken($data){
    try{
        $session = Session::getByToken($data['sessionToken']);
        if($session->isValid()){
            return Config::STATUS_SUCCESS;
        } else{
            $session->delete();
            return Config::STATUS_ERROR;
        }
    } catch(\Exception $e){
        return Config::STATUS_ERROR;
    }

}