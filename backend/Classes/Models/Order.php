<?php

namespace Classes\Models;

use Classes\Models\AbstractModels\DbModel;
use Classes\Config;

/**
 * Represents an order in the database
 * Class Order
 * @package Classes\Models
 */
class Order extends DbModel{

    /**
     * @var string Statement to create new order
     */
    private static $newOrderStmt = "INSERT INTO `order` (user_id) VALUES (:user_id)";

    /**
     * @var string Statement to create new order item
     */
    private static $newItemStmt = "INSERT INTO orderItem (order_id, plant_id, amount) VALUES (:order_id, :plant_id, :amount)";

    /**
     * Returns all the items of this order
     * @return OrderItem[]
     */
    public function getItems(){
        return OrderItem::getAll("WHERE order_id = ".$this->getId());
    }

    /**
     * Create new order
     * @param User $user User this order is for
     * @param array $plants Array of plant ids and amounts to order
     */
    public static function create($user, $plants){
        $db = \Classes\DB::getInstance();
        $stmt = $db->prepare(self::$newOrderStmt);
        $stmt->execute(array(":user_id" => $user->getId()));
        $orderId = $db->lastInsertId();
        foreach($plants as $plantId => $amount){
            $plant = new Plant($plantId);
            if($amount > 0 && amount < $plant->getStock()){
                $plant->setStock($plant->getStock() - $amount);
                $stmt = $db->prepare(self::$newItemStmt);
                $stmt->execute(array(":order_id" => $orderId, ":plant_id" => $plantId, ":amount" => $amount));
            }
        }
    }

} 