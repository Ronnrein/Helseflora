<?php
/**
 * Created by PhpStorm.
 * User: Ronnrein
 * Date: 03.10.2014
 * Time: 16:28
 */

namespace Classes;

use PDO;

/**
 * Stores the database instance and allows classes and methods to get instance
 */
class DB {

    private static $instance;

    public function __construct() {
        if (self::$instance) {
            exit("Instance of DB already exists");
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new PDO("mysql:host=".Config::DB_HOST.";dbname=".Config::DB_NAME, Config::DB_USER, Config::DB_PASS);

            //Comment out following line to avoid exceptions in production
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$instance;
    }

} 