<?php

/**
 * Rally Lookback API Return Object
 *
 * Class to represent the object return by rally
 *
 * @version 1.0
 * @author  Leon Xu <xinghuangxu@gmail.com>
 *
 */

namespace Helper;

class LookBackObject{
    
    private $data;
    private $info;
    
    public function __construct($responseArray,$info) {
        $this->data=$responseArray;
        $this->info=$info;
    }
    
    public function getObjectData($index){
        return $this->data['Results'][$index];
    }
    
}
