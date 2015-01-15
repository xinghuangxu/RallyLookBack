<?php

/**
 * @author Leonx
 * @copyright 2015 NetApp, Inc.
 * @version 1.0.0
 */
use Controller\UserStoryController;

class UserStoryControllerTest extends PHPUnit_Framework_TestCase {


    public function testFindWithOwnerName() {
        $tasks = Task::findWithOwnerName(RallyUserName);
        print_r($tasks);
        $this->assertTrue(count($tasks) > 0);
    }
    

}