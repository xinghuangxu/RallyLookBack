<?php

/**
 * @author Leonx
 * @copyright 2015 NetApp, Inc.
 * @version 1.0.0
 */
use  \Helper\RallyLookBack;
use  \Helper\Rally;

class RallyLookBackTest extends PHPUnit_Framework_TestCase {

    private $rallyLookBack;
    
    protected function setUp() {
        $username = "cxrachina@wichita.edu";
        $password = "Sweety@09";
        \Helper\Rally::getInstance($username, $password);
        $this->rallyLookBack = \Helper\RallyLookBack::getInstance($username, $password);
    }

    public function testFindWithObjectId() {
        $objectId="28089192645";
        $result=$this->rallyLookBack->findWithObjectId($objectId);
    }

}
