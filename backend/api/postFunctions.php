<?php

namespace Api\CallablePostFunctions;

use Classes\Models\Category;
use Classes\Models\Plant;
use Classes\Models\Order;
use Classes\Models\User;
use Classes\Models\Session;
use Classes\Config;

// Here goes post functions that can get called by api

function setField($data){
    $className = ucfirst(strtolower($data['table']));
    if(!(
        ($className == "User" && User::$current->getId() == (int)$data['id'])
        || Config::ACCESS_LEVEL_ADMIN)){
        return Config::STATUS_ERROR;
    }
    $class = "Classes\\Models\\".$className;
    $inst = new $class($data['id']);
    $func = "set".ucfirst(strtolower($data['field']));
    $inst->$func($data['value']);
    return Config::STATUS_SUCCESS;
}

function logIn($data){
    $user = User::checkLogin($data['username'], $data['password']);
    if($user) {
        $session = $user->createToken();
        return array("id" => $user->getId(), "username" => $user->getUsername(), "name" => $user->getName(), "email" => $user->getEmail(), "token" => $session->getToken(), "access" => $user->getAccessId());
    } else{
        return Config::STATUS_ERROR;
    }
}

function logOut($data){
    Session::getByToken($data['sessionToken'])->delete();
}

function editUser($data){
    $user = User::$current;
    if(User::checkAccess(Config::ACCESS_LEVEL_ADMIN) && isset($data['userid'])){
        $user = new User((int)$data['userid']);
    }
    if(isset($data['password']) && $data['password'] != ""){
        $user->setPassword($data['password']);
    }
    $user->setName($data['name']);
    $user->setUsername($data['username']);
    $user->setEmail($data['email']);
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

function purchase($data){
    if(User::checkAccess(Config::ACCESS_LEVEL_USER)){
        Order::create(User::$current, json_decode($data['items']));
    }
}

function newUser($data){
    User::create($data['name'], $data['username'], $data['password'], $data['email']);
}