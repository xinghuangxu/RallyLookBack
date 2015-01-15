<?php
/**
 * Test Run Controller
 * @author Leonx
 * @copyright 2014 NetApp, Inc.
 * @version 1.0.0
 * 
 * Example of the TestRun Api:
  Get Tests for a specific product:  http://coveragedev.eng.netapp.com/leonx/app/api.php/TestRun?query=((cfw_version%20=%2088.20.32.88)AND(ctlr%20=%205501))
  Get Tests between two dates:  http://coveragedev.eng.netapp.com/leonx/app/api.php/TestRun?query=((cfw_version%20=%2088.25.34.18)AND(date%20%3C%202014-11-21))&count=10
  Get Only Runcheck Tests: http://coveragedev.eng.netapp.com/leonx/app/api.php/TestRun?query=(test_type%20=%20runcheck)&count=10
 */

namespace Controller;
use \Helper\Rally as Rally;

class UserStoryController
{
    /*
     * <handle show>
     *
     * [@param  [array] <$arg> <key value pair from $_GET>]
     * [@return <string> <return json encoded array]
     */

//    public function show($arg)
//    {
//        $testRun = TestRun::findWithId($arg['id']);
//        return json_encode(array("status" => 1, "data" => $testRun->toJSON()));
//    }
//
    /*
     * <handle show>
     *
     * [@param  [array] <$arg> <key value pair from $_GET>]
     * [@return <string> <return json encoded array]
     */

    public function index($arg)
    {
        $queryResult = \Model\UserStory::find($arg);
        
        $result = array();
        foreach ($queryResult as $testRun) {
            $result[] = $testRun->toArray();
        }
        return json_encode(array(
            'TotalResultCount' => count($queryResult),
            'Result' => $result,
        ));
    }

}
