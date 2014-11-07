<?php
/**
 * Created by PhpStorm.
 * User: Ronnrein
 * Date: 03.10.2014
 * Time: 16:37
 */

namespace Classes;

class Config {

    // Constants
    const DB_HOST = "localhost";
    const DB_NAME = "helseflora";
    const DB_USER = "helseflora";
    const DB_PASS = "florahelse";
    const IMG_FOLDER = "img";
    const IMG_FOLDER_S = "s";
    const IMG_FOLDER_L = "l";
    const IMG_EXT_S = "png";
    const IMG_EXT_L = "jpg";
    const PASS_SALT = "0aa420224bb91061ac8aace2500b6991";
    const SESSION_TIMEOUT_HOURS = "24";
    const STATUS_ERROR = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_NO_PERMISSION = 2;
    const ACCESS_LEVEL_USER = 0;
    const ACCESS_LEVEL_ADMIN = 1;
}