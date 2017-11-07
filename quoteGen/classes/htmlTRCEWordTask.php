<?php

include_once (__DIR__ . "/htmlDistributedTask.php");

/**
 * Description of htmlTRCEWordTask
 *
 * @author Axian Developer
 */
class htmlTRCEWordTask extends htmlDistributedTask{
    protected $wordType;
    
    public function __construct($targLang, $taskId, $wordType) {
        parent::__construct($targLang, $taskId);
        $this->wordType = $wordType;
    }
    
    public function getTaskKey() {
        return htmlTask::getTaskKey() . "-" . $this->getWordType();
    }
    
    public function getWordType() {
        return $this->wordType;
    }
}
