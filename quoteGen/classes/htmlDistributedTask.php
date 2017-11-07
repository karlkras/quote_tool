<?php

include_once (__DIR__ . "/htmlTask.php");
include_once (__DIR__ . "/QuoteToolUtils.php");

/**
 * Description of htmlDistributedTask
 *
 * @author Axian Developer
 */
class htmlDistributedTask extends htmlTask {

    protected $targLang;

    public function __construct($targLang, $taskId) {
        parent::__construct($taskId);
        $this->targLang = $targLang;
    }

    public function getTaskKey() {
        return parent::getTaskKey() . "-" . $this->getTargLang();
    }

    public function getTargLang() {
        return $this->targLang;
    }

    public function getLookupTargetLang() {
        return $this->convertLangId($this->targLang);
    }

    private function convertLangId($id) {
        return QuoteToolUtils::convertLanguageId($id);
    }

}
