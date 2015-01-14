<?php

/**
 * @author Leonx
 * @copyright 2015 NetApp, Inc.
 * @version 1.0.0
 */
use Helper\StatusUpdate;

class StatusUpdateTest extends PHPUnit_Framework_TestCase {

    public function testHtml() {
        $myStatus=new StatusUpdate(RallyUserName);
        print $myStatus->getHtmlReport();
    }
}