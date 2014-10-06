<?php
/**
 * Created by PhpStorm.
 * User: Ronnrein
 * Date: 03.10.2014
 * Time: 16:40
 */
require_once("autoload.php");

use Classes\Models\Plant;

echo "Testing stuff<br />";

$plant = new Plant(3);
echo $plant->getName()."<br />";
echo $plant->getInfo()["navn"]."<br /><br />";

foreach(Plant::getAll("ORDER BY id ASC") as $plant){
    echo $plant->getId()." => ".$plant->getName()."<br />";
}