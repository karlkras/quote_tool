<?php
namespace llts\fauxWorkfront;

include_once (__DIR__ . "/wordCounts.php");
include_once (__DIR__ . "/linguistTask.php");
include_once (__DIR__ . "/billableTask.php");


/**
 * Description of taskService
 *
 * @author Axian Developer
 */
class taskService {

    public $lingTasks = array(); // linguistTask
    public $billableTasks = array(); // billableTask
    public $projID; // int
    public $rushFee = 0.0; // float
    public $discount = 0.0; // float
    public $minimum = 0.0; // float

    public function __construct($projectId = 0) {
        $this->projID = $projectId;
    }

    public function getLingTasks() {
        return $this->lingTasks;
    }

    public function getBillableTasks() {
        return $this->billableTasks;
    }

    public function getProjID() {
        return $this->projID;
    }

    public function getRushFee() {
        return $this->rushFee;
    }

    public function getDiscount() {
        return $this->discount;
    }

    public function getMinimum() {
        return $this->minimum;
    }

    public function addLingTask($lingTask) {
        array_push($this->lingTasks, $lingTask);
    }

    public function addBillableTask($billableTask) {
        array_push($this->billableTasks, $billableTask);
    }

    public function setProjID($projID) {
        $this->projID = $projID;
    }

    public function setRushFee($rushFee) {
        $this->rushFee = $rushFee;
    }

    public function setDiscount($discount) {
        $this->discount = $discount;
    }

    public function setMinimum($minimum) {
        $this->minimum = $minimum;
    }


}
