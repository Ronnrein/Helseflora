<?php

namespace Classes\Models;

use Classes\Models\AbstractModels\DbModel;
use Classes\Config;
use Classes\DB;

/**
 * Represents a session in the database
 * Class Session
 * @package Classes\Models
 */
class Session extends DbModel{

    /**
     * Prepared string for getting a session by token
     * @var string
     */
    private static $getStmt = "SELECT id FROM session WHERE token = :token LIMIT 1";

    /**
     * Prepared string for creating a new session for a user
     * @var string
     */
    private static $newStmt = "INSERT INTO session (user_id, timeout, token) VALUES (:user_id, :timeout, :token)";

    /**
     * Prepared string for deleting a session for user
     * @var string
     */
    private static $delStmt = "DELETE FROM session WHERE user_id = :user_id";

    /**
     * Returns token of this session
     * @return string
     */
    public function getToken(){
        return $this->info['token'];
    }

    /**
     * Returns user id of this session
     * @return int
     */
    public function getUserId(){
        return (int)$this->info['user_id'];
    }

    /**
     * Returns user object of this session
     * @return User
     */
    public function getUser(){
        return new User($this->getUserId());
    }

    /**
     * Returns whether or not the token is still valid or if it has expired
     * @return bool
     */
    public function isValid(){
        $timeout = new \DateTime($this->info['timeout']);
        $diff = $timeout->diff(new \DateTime());
        return $diff->h + ($diff->days*24) < Config::SESSION_TIMEOUT_HOURS;
    }

    /**
     * Returns session object from supplied token
     * @param $token
     * @return Session
     * @throws \Exception
     */
    public static function getByToken($token){
        $db = DB::getInstance();
        $stmt = $db->prepare(self::$getStmt);
        $stmt->execute(array(":token" => $token));
        if($stmt->rowCount() == 0){
            throw new \Exception("Session with token \"".$token."\" does not exist");
        }
        return new Session($stmt->fetchColumn());
    }

    /**
     * Create a new session for a user
     * @param $user User
     */
    public static function create($user){
        self::removeSession($user);
        $db = DB::getInstance();
        $stmt = $db->prepare(self::$newStmt);
        $date = new \DateTime();
        $date->add(new \DateInterval("PT".Config::SESSION_TIMEOUT_HOURS."H"));
        $stmt->execute(array(":user_id" => $user->getId(), "timeout" => $date->format("Y-m-d H:i:s"), ":token" => self::generateToken($user)));
        return new Session((int)$db->lastInsertId());
    }

    /**
     * Delete session(s) for user
     * @param $user User
     */
    public static function removeSession($user){
        $db = DB::getInstance();
        $stmt = $db->prepare(self::$delStmt);
        $stmt->execute(array(":user_id" => $user->getId()));
    }

    /**
     * Generate a session token for user
     * @param $user User
     * @return string Token hashed in md5
     */
    private static function generateToken($user){
        return md5(rand(0, 100000).$user->getId());
    }

} 