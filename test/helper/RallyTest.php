<?php

/**
 * @author Leonx
 * @copyright 2015 NetApp, Inc.
 * @version 1.0.0
 */
use  \Helper\Rally;

class RallyLookBackTest extends PHPUnit_Framework_TestCase {

    private $rally;
    
    protected function setUp() {
        $username = RallyUserName;
        $password = RallyPassword;
        $this->rally=\Helper\Rally::getInstance($username, $password);
    }
    
    public function testGetTasks(){
        $tasks=$this->rally->find("task","(Owner.Name = \"xxxu3@wichita.edu\")","","true");
        print_r($tasks);
    }

//    public function testGetWorkSpace() {
//        $workSpace=$this->rally->get("workspace","","true");
//        print_r($workSpace);
//    }

}
