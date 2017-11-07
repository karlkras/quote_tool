<?php

namespace llts\fauxWorkfront;

include_once (__DIR__ . "/task.php");

/**
 * Description of billableTask
 *
 * @author Axian Developer
 */
class billableTask {
    public $btask; // task
    public $hourlyRate = 0.0; // float
    public $distributionStrategy = "evenly"; // string
    
    public function __construct($taskName, $taskType, $projectId, $hourlyRate, $workRequired) {
        $this->hourlyRate = $hourlyRate;
        
        $this->btask = new task($projectId, $taskName, $taskType,$workRequired );
    }
    
    public function getBtask() {
        return $this->btask;
    }

    public function getHourlyRate() {
        return $this->hourlyRate;
    }

    public function getDistributionStrategy() {
        return $this->distributionStrategy;
    }

    public function setBtask($btask) {
        $this->btask = $btask;
    }

    public function setHourlyRate($hourlyRate) {
        $this->hourlyRate = $hourlyRate;
    }

    public function setDistributionStrategy($distributionStrategy) {
        $this->distributionStrategy = $distributionStrategy;
    }


    
}
