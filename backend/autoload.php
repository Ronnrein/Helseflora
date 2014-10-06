<?php
/**
 * Created by PhpStorm.
 * User: Ronnrein
 * Date: 03.10.2014
 * Time: 16:15
 */
require_once("functions.php");

use Functions as F;

function __autoload($class) {
    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        $class = str_replace("\\", "/", $class);
    }
    $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . $class . ".php";
    if(F\fileExists($file)){
        require_once($file);
    } else{
        throw new Exception("Unable to load class: \"{$class}\". (File: {$file})");
    }
}