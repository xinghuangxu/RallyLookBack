<?php

/**
 * @abstract A representation of Test Run Data Row in Test Run Table
 * @author Leonx
 * @copyright 2014 NetApp, Inc.
 * @version 1.0.0
 * 
 */

namespace Model;

use \Helper\Rally as Rally;

class Task extends PropertyObject {


    /**
     * <constructor>
     *
     * [@param  [array] <$arData> <data row data>]

     */
    public function __construct($arData = null) {
        parent::__construct($arData);
    }

    public function getName() {
        return $this->data['_refObjectName'];
    }

    public function getId() {
        return $this->data['_refObjectUUID'];
    }

    public static function findWithOwnerName($name) {
        $result = array();
        $tasks = Rally::getInstance()->find("task", "(Owner.Name = $name)", '', "true");
        foreach ($tasks as $task) {
            $result[] = new Task($task);
        }
        return $result;
    }
    
    public function toHtmlRow(){
        return "<tr class=\"ts\"> ".$this->toTd(array(
            $this->FormattedID,$this->Name,$this->State
        ))."</tr>";
    }
    
    public function toTd($fields){
        $tds="";
        foreach($fields as $f){
            $tds.="<td style='padding-right: 10px;'>".$f."</td>";
        }
        return $tds;
    }

    /**
     * <output to json format>
     *
     * [@return <array> <array representation of TestRun Object>]
     */
    public function toArray() {
        return $this->data;
    }

}
