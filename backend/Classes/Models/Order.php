<?php

namespace Classes\Models;

use Classes\Models\AbstractModels\DbModel;
use Classes\Config;

class Order extends DbModel{

    private static $newOrderStmt = "INSERT INTO `order` (user_id) VALUES (:user_id)";
    private static $newItemStmt = "INSERT INTO orderItem (order_id, plant_id, amount) VALUES (:order_id, :plant_id, :amount)";

    /**
     * @param User $user
     * @param array $plants
     */
    public static function create($user, $plants){
        $db = \Classes\DB::getInstance();
        $stmt = $db->prepare(self::$newOrderStmt);
        $stmt->execute(array(":user_id" => $user->getId()));
        $orderId = $db->lastInsertId();
        foreach($plants as $plantId => $amount){
            if($amount > 0){
                $stmt = $db->prepare(self::$newItemStmt);
                $stmt->execute(array(":order_id" => $orderId, ":plant_id" => $plantId, ":amount" => $amount));
            }
        }
    }

} 