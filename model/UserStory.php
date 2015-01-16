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
use Helper\RallyLookBack;

class UserStory extends PropertyObject {

    const API_NAME = "HierarchicalRequirement";

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

    public function getChildren() {
        return $this->children;
    }

    public function getParent() {
        if ($this->data['HasParent'] == 1) {
            if (!$this->parent) {
                $this->parent = UserStory::findWithId($this->data['Parent']['_refObjectUUID']);
            }
            return $this->parent;
        }
        return null;
    }

    public static function findWithParams($query, $order, $fetch) {
        $result = array();
        $stories = Rally::getInstance()->find('userstory', $query, $order, $fetch);
        foreach ($stories as $story) {
            $result[] = new UserStory($story);
        }
        return $result;
    }

    public static function findWithReleaseName($releaseName) {
        $result = array();
        $stories = Rally::getInstance()->find('userstory', "(Release.Name contains {$releaseName})", '', 'ScheduleState,Iteration,HasParent,Parent,Release,c_ArchitecturalTopicID');
        foreach ($stories as $story) {
            $result[] = UserStory::findWithId($story['_refObjectUUID']);
        }
        return $result;
    }

    public static function findWithId($id) {
        if (array_key_exists($id, self::$map)) {
            return self::$map[$id];
        }
        $story = Rally::getInstance()->get(UserStory::API_NAME, $id);
        self::$map[$id] = new UserStory($story[UserStory::API_NAME]);
        return self::$map[$id];
    }

    public function addChild(UserStory $child) {
        $parentId = $child->getParent()->getId();
        while ($parentId !== $this->getId()) {
            $parent = UserStory::findWithId($parentId);
            $parent->addChild($child);
            $child = $parent;
            $parentId = $child->getParent() ? $child->getParent()->getId() : null;
        }
        if (!array_key_exists($child->getId(), $this->children)) {
            $this->children[$child->getId()] = $child;
        }
    }

    public function toString($indent) {
        $str = '<pre>' . str_repeat(' ', $indent * 3) . $indent . '.' . $this->getName() . "</pre>";
        $indent++;
        foreach ($this->children as $child) {
            $str.=$child->toString($indent);
        }
        return $str;
    }

    /**
     * <find tests meet the query requirements>
     *
     * [@param  [array] <$arg> < values such as count, query>]
     * [@return <array> <an array of TestRun objects]
     */
    public static function find($arg) {
        $time = isset($arg['time']) ? $arg['time'] : "";
        $query = isset($arg['query']) ? $arg['query'] : "";
        $fetch = isset($arg['fetch']) ? $arg['fetch'] : "";
        $order = isset($arg['order']) ? $arg['order'] : "";
        $results = UserStory::findWithParams($query, $order,$fetch);
        foreach ($results as $us) {
            if(strpos($fetch,'AcceptedPoints')!==FALSE){
                $acceptedPoints=UserStory::getAcceptedPointEst($us, $time);
            }
            $us->returnToState($time);
            if(isset($acceptedPoints)){
                $us->AcceptedPoints = $acceptedPoints;
            }
        }
        return $results;
    }

    public function returnToState($time) {
        if (!$time)
            return;
        $time = trim($time);
        $timeStamp = strtotime($time);
        $lastUpdatedDateTimeStamp = strtotime($this->LastUpdateDate);
        $creationTimeStamp = strtotime($this->CreationDate);
        if ($timeStamp < $creationTimeStamp) {
            throw new \Exception("Object has not been created yet at time $time. Object Created At: " . $this->CreationDate);
        }
        if ($timeStamp < $lastUpdatedDateTimeStamp && $timeStamp > $creationTimeStamp) {
            $data = array(
                "find" => array(
                    "ObjectID" => $this->ObjectID,
                    "_ValidFrom" => "{\"\$lte\":\"$time\"}",
                    "_ValidTo" => "{\"\$gt\":\"$time\"}"
                ),
                "fields" => "true",
                "pagesize" => 100,
                "limit" => 2,
                "start" => 0
            );
            $lookbackObject = RallyLookBack::getInstance()->query($data);
            $this->data = $lookbackObject->getObjectData(0);
        } else {
            $this->_ValidTo = "9999-01-01T00:00:00.000Z";
            $this->_ValidFrom = $this->LastUpdateDate;
        }
    }

    public static function getAcceptedPointEst($userStory, $time) {
        $child = array();
        $c = 0;
//        $Epic_userstories = Rally::getInstance()->find('userstory', "(Name contains   \"$name\")  ", '', 'ScheduleState,Iteration,Children,DirectChildrenCount,Release,PlanEstimate');
//        $EpicUS_PlndPts = $userStory->PlanEstimate; //US plan estimate
        $ID = $userStory->_refObjectUUID;
        $result = Rally::getInstance()->getChildren('HierarchicalRequirement', "$ID"); //get the childre and store in $Glob_owner
        $b = count($result);
        for ($x = 0; $x < $b; $x++) {
            $child[$c] = $result[$x];
            $c++;
        }
        $CompleteArray = $child;
        $Accepted_Pts = 0;
        $Counter = count($result);
        for ($y = 0; $y < $Counter; $y++) {

            if ($CompleteArray[$y]['DirectChildrenCount'] != 0) {
                $I = $CompleteArray[$y]['_refObjectUUID'];
                $result = Rally::getInstance()->getChildren('HierarchicalRequirement', "/$I");

                global $Glob_owner;
                for ($i = 0; $i < count($result); $i++) {
                    $CompleteArray[] = $result[$i];
                }

                $Counter = 0;
                $Counter = count($CompleteArray);
            }
        }
        for ($x = 0; $x < count($CompleteArray); $x++) {
            if ($CompleteArray[$x]['DirectChildrenCount'] == 0 && $CompleteArray[$x]['ScheduleState'] ==
                    'Accepted') {
                if ($time) {
                    if (!UserStory::shouldFilterByTime($CompleteArray[$x]['AcceptedDate'], $time)) {
                        $Accepted_Pts = $Accepted_Pts + $CompleteArray[$x]['PlanEstimate'];
                    }
                } else {
//                    UserStory::printStory($CompleteArray[$x]);
                    $Accepted_Pts = $Accepted_Pts + $CompleteArray[$x]['PlanEstimate'];
                }
                //filter based on accepted date
            }
        }
        return $Accepted_Pts;
    }
    
//    public static function printStory($us){
//        print $us['_refObjectName']." ".$us['ScheduleState']." ".$us['PlanEstimate']."\n";
//    }
    

    /**
     *
     * [@return true/flase true should be filterd, false otherwise
     */
    public static function shouldFilterByTime($usTime, $filterTime) {
        $usTimeStam = strtotime($usTime);
        $filterTimeStam = strtotime($filterTime);
        $result = "";
        eval(" \$result= ($filterTimeStam < $usTimeStam); ");
        if ($result) {
            return true;
        }
        return false;
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
