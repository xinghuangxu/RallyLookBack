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

    public static function findWithPartialName($name) {
        $result = array();
        $stories = Rally::getInstance()->find('userstory', "(Name contains $name)", '', "true");
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
        $count = isset($arg['count']) ? $arg['count'] : null;
        $query = isset($arg['query']) ? UserStory::parseQueryParam($arg['query']) : array();
        $results = array();
        if ($query['Name']) {
            $results = UserStory::findWithPartialName($query['Name']['value']);
            foreach ($results as $us) {
                $us->AcceptedPoints = UserStory::getPlanAndAcceptedPointEst($us, array_key_exists('Time', $query) ? $query['Time'] : "")['Accepted'];
            }
        }
        return $results;
    }

    public static function getPlanAndAcceptedPointEst($userStory, $time) {
//        print_r($time);
        $child = array();
        $c = 0;
//        $Epic_userstories = Rally::getInstance()->find('userstory', "(Name contains   \"$name\")  ", '', 'ScheduleState,Iteration,Children,DirectChildrenCount,Release,PlanEstimate');
        $EpicUS_PlndPts = $userStory->PlanEstimate; //US plan estimate
        $ID = $userStory->_refObjectUUID;
        $result = Rally::getInstance()->get2('HierarchicalRequirement', "$ID"); //get the childre and store in $Glob_owner
        global $Glob_owner;
        $b = count($Glob_owner['Results']);
        for ($x = 0; $x < $b; $x++) {
            $child[$c] = $Glob_owner['Results'][$x];
            $c++;
        }
        $CompleteArray = array();
        $CompleteArray = $child;
        $Accepted_Pts = 0;
        $Counter = count($Glob_owner['Results']);
        for ($y = 0; $y < $Counter; $y++) {

            if ($CompleteArray[$y]['DirectChildrenCount'] != 0) {
                $I = $CompleteArray[$y]['_refObjectUUID'];
                $result = Rally::getInstance()->get2('HierarchicalRequirement', "/$I");

                global $Glob_owner;
                for ($i = 0; $i < count($Glob_owner['Results']); $i++) {
                    $CompleteArray[] = $Glob_owner['Results'][$i];
                }

                $Counter = 0;
                $Counter = count($CompleteArray);
            }
        }
        for ($x = 0; $x < count($CompleteArray); $x++) {
            if ($CompleteArray[$x]['DirectChildrenCount'] == 0 && $CompleteArray[$x]['ScheduleState'] ==
                    'Accepted') {
                if ($time) {
                    if (!UserStory::shouldFilterByTime($CompleteArray[$x]['AcceptedDate'], $time['operator'], $time['value'])) {
                        $Accepted_Pts = $Accepted_Pts + $CompleteArray[$x]['PlanEstimate'];
                    }
                }else{
                    $Accepted_Pts = $Accepted_Pts + $CompleteArray[$x]['PlanEstimate'];
                }
                //filter based on accepted date
            }
        }
        if ($EpicUS_PlndPts == null) {
            $EpicUS_PlndPts = '0';
        }
        return array('Planned' => $EpicUS_PlndPts, 'Accepted' => $Accepted_Pts);
    }

    /**
     *
     * [@return true/flase true should be filterd, false otherwise
     */
    public static function shouldFilterByTime($usTime, $operator, $filterTime) {
        $usTimeStam = strtotime($usTime);
        $filterTimeStam = strtotime($filterTime);
        $result = "";
        eval(" \$result= ($filterTimeStam $operator $usTimeStam); ");
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
