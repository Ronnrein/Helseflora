<?php

namespace Classes\Models\AbstractModels;

use Classes\DB;
use PDO;

/**
 * Abstract model class other classes will extend
 */
abstract class DbModel {

    /**
     * Name of view, if this is one
     * @var string
     */
    protected static $view;

    /**
     * Stores all instances of current class
     * @var array
     */
    protected static $instances = array();

    /**
     * Stores info from database
     * @var array
     */
    protected $info;

    /**
     * Database instance
     * @var PDO
     */
    protected $DB;

    public function __construct($in) {
        $this->DB = DB::getInstance();
        if (is_int($in) || (is_string($in) && is_int((int) $in))) {
            $in = (int) $in;
            $exp = explode('\\', get_called_class());
            $table = (isset(static::$view)) ? static::$view : strtolower(end($exp));
            $stmt = $this->DB->prepare("SELECT * FROM {$table} WHERE id = :id");
            $stmt->bindParam(":id", $in, PDO::PARAM_INT);
            $stmt->execute();
            $this->info = $stmt->fetch(PDO::FETCH_ASSOC);
            static::$instances[] = $this;
        } else if (is_array($in)) {
            $this->info = $in;
        }
    }

    /**
     * Get id of current tablerow
     * @return int
     */
    public function getId(){
        return isset($this->info['id']) ? (int)$this->info['id'] : null;
    }

    /**
     * Returns the info array from db
     * @return array
     */
    public function getInfo(){
        return $this->info;
    }

    /**
     * Delete this row of the class
     */
    public function delete(){
        echo "DELETING TOKEN";
        $exp = explode('\\', get_called_class());
        $stmt = $this->DB->prepare("DELETE FROM ".strtolower(end($exp))." WHERE id= :id");
        $stmt->execute(array(":id" => $this->getId()));
    }

    /**
     * Set field of object and database to value
     * @param string $field
     * @param mixed $value
     */
    protected function setField($field, $value) {
        $exp = explode('\\', get_called_class());
        $stmt = $this->DB->prepare("UPDATE `".strtolower(end($exp))."` SET `{$field}` = :value WHERE id = :id");
        $stmt->execute(array(":value" => $value, ":id" => $this->getId()));
        foreach(static::getInstances() as $instance){
            if($instance->getId() === $this->getId()){
                $instance->info[$field] = $value;
            }
        }
    }

    /**
     * Get all instances of current class
     * @return $this[]
     */
    protected static function getInstances(){
        $result = array();
        foreach(static::$instances as $instance){
            if(get_class($instance) === get_called_class()){
                $result[] = $instance;
            }
        }
        return $result;
    }

    /**$appendix
     * Get all the rows of this model as objects
     * @return $this[]
     */
    public static function getAll($appendix = ""){
        $appendix = $appendix != "" ? " ".$appendix : $appendix;
        $db = DB::getInstance();
        $result = array();
        $stmt = $db->prepare("SELECT * FROM `".static::getView()."`".$appendix);
        $stmt->execute();
        foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
            $result[] = new static($row);
        }
        static::getView();
        return $result;
    }

    /**
     * Gets name of table/view to get data from
     * @return string
     */
    public static function getView(){
        $exp = explode('\\', get_called_class());
        return isset(static::$view) ? static::$view : strtolower(end($exp));
    }

}