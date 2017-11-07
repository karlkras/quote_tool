<?php

include_once (__DIR__ . "/htmlDistributedTask.php");

/**
 * Description of htmlLinquistTask
 *
 * @author Axian Developer
 */
class htmlLinquistTask extends htmlDistributedTask{
    public function getTaskKey() {
        return htmlTask::getTaskKey();
    }
}
