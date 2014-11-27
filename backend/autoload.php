<?php

require_once("functions.php");

use Functions as F;

/**
 * Autoloads classes when called
 * @param $class
 * @throws Exception
 */
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