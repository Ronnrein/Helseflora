<?php
/**
 * Created by PhpStorm.
 * User: Ronnrein
 * Date: 03.10.2014
 * Time: 16:17
 */
namespace Functions;

/**
 * Checks if the file exists, case insensitive on file only
 * @param type $path The path
 * @param type $caseSensitive If the search should be case insensitive
 * @return boolean If the file exists
 */
function fileExistsSingle($path, $caseSensitive = false) {
    if (file_exists($path)) {
        return true;
    } else if ($caseSensitive) {
        return false;
    }

    $lowerPath = strtolower($path);

    foreach (glob(dirname($path) . "/*") as $file) {
        if (strtolower($file) === $lowerPath) {
            return true;
        }
    }
    return false;
}

/**
 * Checks if the file exists, case insensitive on full path
 * @param type $path The path
 * @param type $caseSensitive If the search should be case insensitive
 * @return boolean If the file exists
 */
function fileExists($path, $caseSensitive = false) {
    if (file_exists($path)) {
        return true;
    } else if ($caseSensitive) {
        return false;
    }

    $dirs = explode("/", $path);
    $len = count($dirs);
    $dir = "/";
    foreach ($dirs as $i => $part) {
        $dirPath = fileExistsSingle($dir . $part);
        if (!$dirPath) {
            return false;
        }

        $dir = $dirPath;
        $dir .= (($i > 0) && ($i < $len - 1)) ? "/" : "";
    }
}