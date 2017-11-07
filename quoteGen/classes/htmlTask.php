<?php


/**
 * Description of htmlTask
 *
 * @author Axian Developer
 */
abstract class htmlTask {
    protected $taskId;
    public function __construct($taskId) {
        $this->taskId = $taskId;
    }
    
    public function getTaskId() {
        return $this->taskId;
    }
    
    public function getTaskKey(){
        return "task-" . $this->getTaskId();
    }
}
