<?php

/**
 * @author Leonx
 * @copyright 2014 NetApp, Inc.
 * @version 1.0.0
 */
use \Model\UserStory;

class UserStoryTest extends PHPUnit_Framework_TestCase {

    public function testFindWithPartialName() {
        $userStories = UserStory::findWithParams("(Name contains Spark)", "", "");
        $this->assertTrue(count($userStories) > 0);
    }

    public function testUserStoryData() {
        $us = UserStory::findWithParams("(Name contains \"Spark\")", "", "true");
        $userStory = $us[0];
//        print_r($userStory);
        $this->assertTrue($userStory->PlanEstimate > 0);
        $this->assertTrue(strpos(strtolower($userStory->_refObjectName), 'spark') !== FALSE);
    }

    public function testPlanAndAcceptedPointEst() {
        $us = UserStory::findWithParams("(Name contains \"Spark\")", "", "true");
        $values = \Model\UserStory::getAcceptedPointEst($us[0], "");
        $this->assertTrue($values>=0);
    }
}
