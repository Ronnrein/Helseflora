<?php
/**
 * Created by PhpStorm.
 * User: Ronnrein
 * Date: 18.10.2014
 * Time: 01:41
 */

namespace Classes\Models;

use Classes\Models\AbstractModels\DbModel;
use Classes\Config;
use Classes\DB;


class Session extends DbModel{

    private static $getStmt = "SELECT id FROM session WHERE token = :token LIMIT 1";
    private static $newStmt = "INSERT INTO session (user_id, timeout, token) VALUES (:user_id, :timeout, :token)";
    private static $delStmt = "DELETE FROM session WHERE user_id = :user_id";

    public function getToken(){
        return $this->info['token'];
    }

    public function getUser(){
        return new User($this->getId());
    }

    public function isValid(){
        $timeout = new \DateTime($this->info['timeout']);
        $diff = $timeout->diff(new \DateTime());
        return $diff->h + ($diff->days*24) < Config::SESSION_TIMEOUT_HOURS;
    }

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
     * @param $user User User to create session for
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
     * @param $user User User to delete all sessions for
     */
    public static function removeSession($user){
        $db = DB::getInstance();
        $stmt = $db->prepare(self::$delStmt);
        $stmt->execute(array(":user_id" => $user->getId()));
    }

    /**
     * Generate a session token for user
     * @param $user User User to create session token for
     * @return string Token hashed in md5
     */
    private static function generateToken($user){
        return md5(rand(0, 100000).$user->getId());
    }

} 