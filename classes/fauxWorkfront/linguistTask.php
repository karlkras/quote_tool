<?php
namespace llts\fauxWorkfront;

include_once (__DIR__ . "/task.php");
include_once (__DIR__ . "/wordCounts.php");

use llts\fauxWorkfront\task;
use llts\fauxWorkfront\wordCounts;

/**
 * Description of linguistTask
 *
 * @author Axian Developer
 */
class linguistTask {
    const CATEGORY_NAMES = array("Trados TR/TR+CE Task", "Hourly Miscellaneous Task");
    const TRCE_TASK_TYPE = 0;
    const HOURLY_TASK_TYPE = 1;
    
    public $ltask; // task
    public $sourceLang = ""; // string
    public $targLang = ""; // string
    public $categoryName; // string
    public $wordCounts = null; // wordCounts
    // this will come out of the @task api call.
    public $wordRateDetails = null; // linguistRateDetails
    
    public function __construct($taskName, $taskType, $projectId, $sourceLang, $targLang, wordCounts $wordCounts, $wordRateDetails, $categoryTypeInt) {
        $this->sourceLang = $sourceLang;
        $this->targLang = $targLang;
        $this->wordCounts = $wordCounts;
        $this->wordRateDetails = $wordRateDetails;
        $catTypeArray = linguistTask::CATEGORY_NAMES;
        $this->categoryName = $catTypeArray[$categoryTypeInt];
        //if($categoryTypeInt == self::TRCE_TASK_TYPE);
        $this->ltask = new task($projectId, $sourceLang . " to " . $targLang . " " . $taskType, $taskType);
    }
    
    public function getLtask() {
        return $this->ltask;
    }

    public function getSourceLang() {
        return $this->sourceLang;
    }

    public function getTargLang() {
        return $this->targLang;
    }

    public function getCategoryName() {
        return $this->categoryName;
    }

    public function getWordRateDetails() {
        return $this->wordRateDetails;
    }

    public function getWordCounts() {
        return $this->wordCounts;
    }

    public function setLtask($ltask) {
        $this->ltask = $ltask;
    }

    public function setSourceLang($sourceLang) {
        $this->sourceLang = $sourceLang;
    }

    public function setTargLang($targLang) {
        $this->targLang = $targLang;
    }

    public function setCategoryName($categoryName) {
        $this->categoryName = $categoryName;
    }

    public function setWordRateDetails($wordRateDetails) {
        $this->wordRateDetails = $wordRateDetails;
    }

    public function setWordCounts($new, $match, $fuzzy, $words = 0, $formattingHOurs = 0) {
        $this->wordCounts = new wordCounts($new, $match, $fuzzy, $words, $formattingHOurs);
    }

}



