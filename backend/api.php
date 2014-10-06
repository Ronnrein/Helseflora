<?php
/**
 * Created by PhpStorm.
 * User: Ronnrein
 * Date: 06.10.2014
 * Time: 16:24
 */
namespace Api;

require_once("autoload.php");

// Array containing legal formats (format => page content type)
$formats = array(
    "json"        => "json",
    "javascript"  => "json",
    "xml"         => "xml"
);

// Default format will be json
$format = "json";

// Check for callback get variable
if(isset($_GET['callback'])){

    // If callback variable is set, this should be javascript
    $format = "javascript";

} else if(isset($_GET['format']) && isset($formats[$_GET['format']])){

    // If the get variable is set and valid, assign it
    $format = $_GET['format'];

}

// Define format constants for easy access
define("FORMAT", $format);
define("CONTENT", $formats[FORMAT]);

// Define headers, based on content type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/".CONTENT);

// Handle action
if(isset($_GET['a'])){

    // Assign action to variable, later to be called
    $action = "Api\\CallableFunctions\\".$_GET['a'];

    // If variable name is also function name, call it
    if(function_exists($action)){
        $action();
    }

}

// Output functions

function output($data){
    $outputFunc = "Api\\output".ucfirst(CONTENT);
    $outputFunc($data);
}

/**
 * Outputs json based on supplied array
 * @param array $data
 */
function outputJson($data){
    $data = json_encode($data, JSON_NUMERIC_CHECK);
    $callback = isset($_GET['callback']) ? $_GET['callback'] : false;
    print $callback ? "$callback($data)" : $data;
}

/**
 * Outputs xml based on supplied array
 * @param array $data
 */
function outputXml($data){
    $xml = new \SimpleXMLElement("<?xml version=\"1.0\"?><data></data>");
    $array = array();
    foreach($data as $item){
        $array[] = array("row" => $item);
    }

    arrayToXml($array, $xml);

    print $xml->asXML();
}

// Other functions

/**
 * Takes array and xml element and builds up the xml element with the array info
 * @param array $array
 * @param SimpleXmlElement $xml
 */
function arrayToXml($array, &$xml){
    foreach($array as $key => $value){
        if(is_array($value)){
            if(!is_numeric($key)){
                $subnode = $xml->addChild("$key");
                arrayToXml($value, $subnode);
            } else {
                arrayToXml($value, $xml);
            }
        } else {
            $xml->addChild("$key", "$value");
        }
    }
}