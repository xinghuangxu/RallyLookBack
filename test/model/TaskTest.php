<?php

/**
 * @author Leonx
 * @copyright 2014 NetApp, Inc.
 * @version 1.0.0
 */
use  \Model\Task;

class TaskTest extends PHPUnit_Framework_TestCase {

    private $rally;

    public function testFindWithOwnerName() {
        $tasks = Task::findWithOwnerName(RallyUserName);
        print_r($tasks);
        $this->assertTrue(count($tasks) > 0);
    }
    
//    public function testUserStoryData(){
//        $us=UserStory::findWithPartialName("\"EQITesting LSIP1234567892\"");
//        $userStory = $us[0];
//        $this->assertTrue( $userStory->PlanEstimate > 0);
//        $this->assertTrue( $userStory->_refObjectUUID != "");
//    }
//
//    public function testPlanAndAcceptedPointEst() {
//        $us=UserStory::findWithPartialName("\"EQITesting LSIP1234567892\"");
//        $values = \Model\UserStory::getPlanAndAcceptedPointEst($us[0], "");
//        $this->assertTrue($values['Planned']>0);
//        $this->assertTrue($values['Accepted']>0);
//    }

}
