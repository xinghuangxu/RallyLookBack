<?php

/**
 * @author Leonx
 * @copyright 2015 NetApp, Inc.
 * @version 1.0.0
 */
use \Helper\RallyLookBack;
use \Helper\Rally;

class RallyLookBackTest extends PHPUnit_Framework_TestCase {

//    private $rallyLookBack;
    private $_curl;
    private $_headers_request = array('Content-Type: text/java_script');
    private $_debug = false;
    private $_agent = 'PHP - Rally Api - 1.4';

    public function testFindWithObjectId() {
        $url = "https://rally1.rallydev.com/analytics/v2.0/service/rally/workspace/2930602434/artifact/snapshot/query.js?find={%22ObjectID%22:28089192645}&fields=true&start=0&pagesize=100&limit=10";
        $this->_curl = curl_init();
        set_time_limit(0);
        $this->_setopt(CURLOPT_RETURNTRANSFER, true);
        $this->_setopt(CURLOPT_HTTPHEADER, $this->_headers_request);
        $this->_setopt(CURLOPT_VERBOSE, $this->_debug);
        $this->_setopt(CURLOPT_USERAGENT, $this->_agent);
        $this->_setopt(CURLOPT_HEADER, 0);
        $this->_setopt(CURLOPT_SSL_VERIFYHOST, 0);
        $this->_setopt(CURLOPT_SSL_VERIFYPEER, 0);
        $this->_setopt(CURLOPT_COOKIEJAR, dirname(__file__) . '/cookie.txt');
        // Authentication
        $this->_setopt(CURLOPT_USERPWD, RallyUserName . ":" . RallyPassword);
        $this->_setopt(CURLOPT_HTTPAUTH, CURLAUTH_ANY);

        $this->_setopt(CURLOPT_CUSTOMREQUEST, 'GET');
        $this->_setopt(CURLOPT_POSTFIELDS, '');

        $this->_setopt(CURLOPT_URL, $url);
        $response = curl_exec($this->_curl);
        $array = (json_decode($response, true));
        $this->assertTrue(count($array['Results']) > 0);
    }

    public function testGetLookBackObject() {
        $lookbackApi = RallyLookBack::getInstance();
        $params = array(
            "find" => array(
                "ObjectID" => 28089192645
            ),
            "fields" => "true",
            "pagesize" => 100,
            "limit" => 2,
            "start" => 0
        );
        $lookbackObject = $lookbackApi->query($params);
        $this->assertTrue(get_class($lookbackObject)=="Helper\LookBackObject");
    }

    public function testGetUrlParam() {
        $params = array(
            "find" => array(
                "ObjectID" => 28089192645
            ),
            "fields" => "true",
            "start" => 0,
            "pagesize" => 100,
            "limit" => 10,
        );
        $ansewr = "find={ObjectID:28351577495}&fields=true&start=0&pagesize=100&limit=10";
        $parsedResult = RallyLookBack::getInstance()->parseArrayToUrlParam($params);
        $this->assertFalse(strcmp($parsedResult, $ansewr) == 0);
    }

    public function testGetFromATime() {
        $time="2015-01-09T21:23:47.796Z";
        $lookbackObject = RallyLookBack::getInstance()->query(array(
            "find" => array(
                "ObjectID" => 28089192645,
                "_ValidFrom" => "{\"\$lte\":\"$time\"}",
                "_ValidTo" => "{\"\$gt\":\"$time\"}"
            ),
            "fields" => "true",
            "pagesize" => 100,
            "limit" => 2,
            "start" => 0
        ));
        $this->assertTrue(get_class($lookbackObject)=="Helper\LookBackObject");
    }

    protected function _setopt($option, $value) {
        curl_setopt($this->_curl, $option, $value);
    }

}
