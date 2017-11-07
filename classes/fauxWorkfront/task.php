<?php

namespace llts\fauxWorkfront;

include_once (__DIR__ . '/baseFauxObject.php');

/**
 * Description of task
 *
 * @author Axian Developer
 */
class task extends baseFauxObject {
    public $price = 0.0; // float
    public $projectID = 0; // int
    public $type = ""; // string
    public $workRequired = 0.0; // float

    public function __construct($projectId = 0, $name = "", $type = "", $workRequired = 0.0) {
        parent::__construct();
        $this->projectID = $projectId;
        $this->setName($name);
        $this->type = $type;
        $this->workRequired = $workRequired;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getProjectID() {
        return $this->projectID;
    }

    public function getType() {
        return $this->type;
    }

    public function getWorkRequired() {
        return $this->workRequired;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    public function setProjectID($projectID) {
        $this->projectID = $projectID;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function setWorkRequired($workRequired) {
        $this->workRequired = $workRequired;
    }

}
