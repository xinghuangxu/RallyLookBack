<?php

/**
 * @abstract Single entry to all requests. Bootstrap and dispatch Requests
 * @author Leonx
 * @copyright 2014 NetApp, Inc.
 * @version 1.0.0
 */


require_once "bootstrap.php";

try {
    $router = new Router();
    echo $router->route();
} catch (Exception $ex) {
    echo json_encode(array("status" => 0, "message" => 'Bootstrap fail! ' . $ex->getMessage()));
}

class Router
{

    private $controllerName;
    private $method;
    private $action;
    private $args;

    /**
     * <Method Description>
     *
     * [@param  [param type] <param name> <param description>]
     * [@return <return type> <return description]
     * [@throws <exception type> <Exception reason>]
     */
    static function dispatch($args)
    {
        $router = new Router($args);
        return $router->route();
    }

    /**
     * <__construct>
     *
     * [@param  [array] <$args> <for testing or use to reroute old routers>]
     */
    public function __construct($args = null)
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($args) {
            $this->controllerName = $args['controller'];
            $this->action = $args['action'];
        } else {
            $parameters = (explode('/', $_SERVER['PATH_INFO']));
            $this->controllerName = $parameters[1];
            $this->_setRestfulAction(isset($parameters[2]) ? $parameters[2] : null, isset($parameters[3]) ? $parameters[3] : null);
        }
        $this->_parseParams();
    }

    /**
     * <Retrieve paramters from the Request>
     * use to set the router's private args and method fileds
     */
    private function _parseParams()
    {
        $method = $this->method;
        if ($method == "PUT" || $method == "DELETE") {
            parse_str(file_get_contents('php://input'), $this->args); //might need to decode this too
            $this->args = json_decode($this->args); //assume is json encoded
        } else if ($method == "GET") {
            $this->args = $_GET;
        } else if ($method == "POST") {
            $this->args = json_decode($_POST['data'], true);
        }
    }

    /**
     * <_setRestfulAction>
     * Verb	Path	    Action
      GET	/resource   index
      GET	/resource/create  create
      POST	/resource	store
      GET	/resource/{resource}	show
      PUT/PATCH	/resource/{resource}  update
      DELETE	  /resource/{resource}   destroy
     * [@param  [string] <$arg1> <action>]
     * [@param  [string] <$arg2> <could be null or {resource} id>]
     * [@return <return type> <return description]
     * [@throws <exception type> <Exception reason>]
     */
    private function _setRestfulAction($arg1, $arg2)
    {
        if (!$arg1) {
            if ($this->method == "GET") { //get
                $this->action = "index"; //default 
            } else { //post
                $this->action = "store"; //default 
            }
        } else if (is_numeric($arg1)) {
            if ($this->method === "GET") { //get
                if ($arg2) {
                    $this->action = $arg2; //edit
                } else {
                    $this->action = "show";
                }
            } else if ($this->method === "DELETE") { //put
                $this->action = "destroy"; //default for delete 
            } else {
                $this->action = "update"; //default for update
            }
            $_GET['id'] = $arg1;
        } else {
            $this->action = $arg1;
        }
    }
    
    /**
     * <Dispatch Request to Controller Class -> Action method>
     *

     * [@return <depends> <will return what ever the controller function returns]
     * [@throws <Exception> <Exception controller function exception>]
     */
    public function route()
    {
        try {
            $this->validateRoute();
            $controllerName = "\\Controller\\" . $this->controllerName . "Controller";
            $controller = new $controllerName;
            return $controller->{$this->action}($this->args);
        } catch (Exception $ex) {
//            var_dump($ex->getTrace());
            echo json_encode(array("status" => 0, 'action' => $this->action, "message" => $ex->getMessage()));
        }
    }

    /**
     * Validate if the Reqeust Route is valid
     * 
     * @throw Exception when the route is not valid
     */

    /**
     * <validateRoute>
     *
     * [@throws <Exception> <controller name not valid>]
     * [@throws <Exception> <action name not valid>]
     */
    public function validateRoute()
    {
        if (!$this->controllerName) {
            throw new Exception("Controller parameter required!");
        }
        if (!$this->action) {
            throw new Exception("Action parameter required!");
        }
    }

}
