<?php
/**
 * Created by PhpStorm.
 * User: Ronnrein
 * Date: 06.10.2014
 * Time: 17:58
 */

namespace Classes\Models;

use Classes\Models\AbstractModels\DbModel;
use Classes\Config;

class User extends DbModel{

    private static $newUserStmt = "INSERT INTO user (name, username, password, email) VALUES (:name, :username, :password, :email)";
    private static $checkUserStmt = "SELECT id FROM user WHERE username = :username AND password = :password";

    public static $current = null;

    public function getUsername(){
        return $this->info['username'];
    }

    public function getAccessId(){
        return (int)$this->info['accessid'];
    }

    public function setUsername($username){
        $this->setField("username", $username);
    }

    public function createToken(){
        return Session::create($this);
    }

    /**
     * Checks the login and returns if it is a valid login or not
     * @param $user
     * @param $pass
     * @return User|bool
     */
    public static function checkLogin($user, $pass){
        $db = \Classes\DB::getInstance();
        $stmt = $db->prepare(self::$checkUserStmt);
        $stmt->execute(array(":username" => $user, ":password" => self::hashPassword($pass)));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? new User($row['id']) : false;
    }

    /**
     * Create a new user
     * @param $name
     * @param $username
     * @param $password
     * @param $email
     */
    public static function create($name, $username, $password, $email){
        $db = \Classes\DB::getInstance();
        $stmt = $db->prepare(self::$newUserStmt);
        $stmt->execute(array(":name" => $name, ":username" => $username, ":password" => self::hashPassword($password), ":email" => $email));
    }

    /**
     * Hash the given string with md5, using the salt set in Config
     * @param $pass
     * @return string
     */
    private static function hashPassword($pass){
        return md5($pass.Config::PASS_SALT);
    }

} 