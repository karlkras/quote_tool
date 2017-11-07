<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PricingMySql
 *
 * @author Axian Developer
 */
require_once(__DIR__ . "/../../definitions.php");

class PricingMySql extends mysqli{

    public function __construct() {
        parent::__construct(DBServerName, UserName, Password, DBName);
        if($this->connect_errno){
            throw new Exception("Failed to connect to MySQL: (". $this->connect_errno. ") ". $this->connect_error, $this->connect_errno);
        }
    }
    
    public function query($query) {
        if(!$result  = parent::query($query)) {
            echo "Query Execution failed: (", $this->connect_errno, ") ", $query;
        }
        return $result;
    }

    function __destruct() {
        //$this->close();
    }

}
