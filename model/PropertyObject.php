<?php

/**
 * @abstract A generic class for table classes to inherit from
 * @author Leonx
 * @copyright 2014 NetApp, Inc.
 * @version 1.0.0
 * 
 */

namespace Model;

abstract class PropertyObject
{

    protected $data; //Actual data from
    //the database
    protected $errors = array(); //Any validation errors

    //that might have occurred

    /**
     * <constructor>
     *
     * [@param  [array] <$arData> <key value pair of a row in database table>]
     */
    public function __construct($arData)
    {
        if (!$arData)
            $arData = array();
        $this->data = $arData;
    }


    /**
     * <Magic function get, called when property not found>
     *
     * [@param  [string] <$propertyName> ]
     * [@return <string> <property value]
     */

    public function __get($propertyName)
    {
        if (method_exists($this, 'get' . $propertyName)) {
            return call_user_func(
                    array($this, 'get' . $propertyName));
        } else {
            return $this->data[$propertyName];
        }
    }


    /**
     * <Magic function set, called when property not found>
     *
     * [@param  [string] <$propertyName> ]
     */

    public function __set($propertyName, $value)
    {
        if (method_exists($this, 'set' . $propertyName)) {
            return call_user_func(
                    array($this, 'set' . $propertyName), $value);
        } else {
            //Now set the new value
            $this->data[$propertyName] = $value;
        }
    }

    /**
     * <pasre query string like ((cfw_version = 88.20.32.97)AND(test_type = runcheck)) >
     *
     * [@param  [string] <$queryStr> <query string from $_GET>]
     * [@return <array> <key value pairs>]
     */

    public static function parseQueryParam($queryStr)
    {
        $query = array();
        $queryStr = str_replace(array("(", ")"), " ", $queryStr); //replace all ( and )
        $ands = preg_split("/and/i", $queryStr);
        foreach ($ands as $and) {
            $array=preg_split("/\s/", trim($and));
            $strValue="";
            for($i=2;$i<count($array);$i++){
                $strValue.=' '.$array[$i];
            }
            list($key, $ope, $value) = array($array[0],$array[1],$strValue);
            $query[$key] = array("key" => $key, "operator" => $ope, "value" => $value);
        }
        return $query;
    }

}
