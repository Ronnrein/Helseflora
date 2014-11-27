<?php

namespace Classes\Models;

use Classes\Models\AbstractModels\DbModel;
use Classes\Config;

/**
 * Represents a user in the database
 * Class User
 * @package Classes\Models
 */
class User extends DbModel{

    /**
     * Prepared statement to create a new user
     * @var string
     */
    private static $newUserStmt = "INSERT INTO user (name, username, password, email, accessid) VALUES (:name, :username, :password, :email, :accessid)";

    /**
     * Prepared statement to check login credentials
     * @var string
     */
    private static $checkUserStmt = "SELECT id FROM user WHERE username = :username AND password = :password";

    /**
     * Holds the current logged in user, if there is one
     * @var User
     */
    public static $current = null;

    /**
     * Returns name of user
     * @return string
     */
    public function getName(){
        return $this->info['name'];
    }

    /**
     * Returns email of user
     * @return string
     */
    public function getEmail(){
        return $this->info['email'];
    }

    /**
     * Returns username of user
     * @return string
     */
    public function getUsername(){
        return $this->info['username'];
    }

    /**
     * Returns access id of user
     * @return int
     */
    public function getAccessId(){
        return (int)$this->info['accessid'];
    }

    /**
     * Sets a new username for this user
     * @param $username
     */
    public function setUsername($username){
        $this->setField("username", $username);
    }

    /**
     * Sets a new name for this user
     * @param $name
     */
    public function setName($name){
        $this->setField("name", $name);
    }

    /**
     * Sets a new email for this user
     * @param $email
     */
    public function setEmail($email){
        $this->setField("email", $email);
    }

    public function setPassword($password){
        $this->setField("password", $this->hashPassword($password));
    }

    /**
     * Creates a new session token for this user and returns it
     * @return Session
     */
    public function createToken(){
        return Session::create($this);
    }

    /**
     * Checks the login and returns if it is a valid login or not
     * @param $user
     * @param $pass
     * @return User|false
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
        $stmt->execute(array(":name" => $name, ":username" => $username, ":password" => self::hashPassword($password), ":email" => $email, ":accessid" => Config::ACCESS_LEVEL_USER));
    }

    /**
     * @param int $access
     * @param User $user
     * @return bool
     */
    public static function checkAccess($access = 0, $user = null){
        if(!isset($user)){
            if(isset(self::$current)){
                $user = self::$current;
            } else{
                return false;
            }
        }
        return $user->getAccessId() >= $access;
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