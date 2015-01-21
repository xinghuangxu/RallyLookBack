<?php

/**
 * @author Leonx
 * @copyright 2015 NetApp, Inc.
 * @version 1.0.0
 */
use Controller\UserStoryController;

class UserStoryControllerTest extends PHPUnit_Framework_TestCase {

    public function testTimeParameter() {
        $userStoryController = new UserStoryController();
        //get old object
        $param = array(
            "query" => "(Name contains  \"New UI - Front End\")",
            'time' => "2013-06-14T15:59:20.717Z",
            'fetch' => 'true'
        );
        $json_encoded_result = $userStoryController->index($param);
        $decoded_result = json_decode($json_encoded_result, "true");
        $oldObject = $decoded_result['Result'][0];
        $this->assertTrue($oldObject['AcceptedPoints'] == 0);

        //get current object
        $param = array(
            "query" => "(Name contains  \"New UI - Front End\")",
            'time' => "2014-06-14T15:59:20.717Z",
            'fetch' => 'true'
        );
        $json_encoded_result = $userStoryController->index($param);
        $decoded_result = json_decode($json_encoded_result, "true");
        $newObject = $decoded_result['Result'][0];
        $this->assertTrue(count($newObject) > 0);
        
        //accepted points should be different for both object
        $this->assertTrue($oldObject['AcceptedPoints'] != $newObject['AcceptedPoints']);
    }

}
