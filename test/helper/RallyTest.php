<?php

/**
 * @author Leonx
 * @copyright 2015 NetApp, Inc.
 * @version 1.0.0
 */
use \Helper\Rally;

class RallyTest extends PHPUnit_Framework_TestCase {

    public function testGetTasks() {
        $tasks = Rally::getInstance()->find("task", "(Owner.Name = \"xxxu3@wichita.edu\")", "", "true");
//        print_r($tasks);
        $this->assertTrue(count($tasks) > 0);
    }

    public function testGetWorkSpace() {
        $workSpace = Rally::getInstance()->find("workspace", "", "", "true");
//        print_r($workSpace);
        $this->assertTrue(count($workSpace) > 0);
    }

}
