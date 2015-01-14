<?php

/**
 * @author Leonx
 * @copyright 2014 NetApp, Inc.
 * @version 1.0.0
 */
use  \Model\UserStory;

class UserStoryTest extends PHPUnit_Framework_TestCase {

    private $rally;

    protected function setUp() {
        $username = "cxrachina@wichita.edu";
        $password = "Sweety@09";
        $this->rally = \Helper\Rally::getInstance($username, $password);
    }

    public function testFindWithPartialName() {
        $userStories = \Model\UserStory::findWithPartialName("Spark");
//        print_r($userStories);
        $this->assertTrue(count($userStories) > 0);
    }
    
    public function testUserStoryData(){
        $us=UserStory::findWithPartialName("\"EQITesting LSIP1234567892\"");
        $userStory = $us[0];
        $this->assertTrue( $userStory->PlanEstimate > 0);
        $this->assertTrue( $userStory->_refObjectUUID != "");
    }

    public function testPlanAndAcceptedPointEst() {
        $us=UserStory::findWithPartialName("\"EQITesting LSIP1234567892\"");
        $values = \Model\UserStory::getPlanAndAcceptedPointEst($us[0], "");
        $this->assertTrue($values['Planned']>0);
        $this->assertTrue($values['Accepted']>0);
    }

}
