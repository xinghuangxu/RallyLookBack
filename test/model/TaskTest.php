<?php

/**
 * @author Leonx
 * @copyright 2014 NetApp, Inc.
 * @version 1.0.0
 */
use  \Model\Task;

class TaskTest extends PHPUnit_Framework_TestCase {

    public function testFindWithOwnerName() {
        $tasks = Task::findWithOwnerName(RallyUserName);
        $this->assertTrue(count($tasks) > 0);
    }

}
