<?php

require_once (__DIR__ . "/QuoteItemHelper.php");
require_once (__DIR__ . "/QuoteConstants.php");
require_once (__DIR__ . "/../interfaces/ILinguistQuoteItem.php");

/**
 * Description of LinguistItemHelper
 *
 * @author Axian Developer
 */
class LinguistQuoteItemHelper extends QuoteItemHelper implements ILinguistQuoteItem{
    protected $theTask;
    protected $categoryParent;
    
    public function __construct($theTask, $clientDb = null) {
        parent::__construct(QuoteConstants::getTaskCategory($theTask->ltask->type), $theTask->ltask->id, $theTask->ltask->name, $theTask->ltask->type, $clientDb);
        $this->theTask = $theTask;
        $this->setCostMinimum($theTask->wordRateDetails->minimum);
    }
    
    public function getSourceLang() {
        return $this->theTask->sourceLang;
    }

    public function getTargetLang() {
        return $this->theTask->targLang;
    }

    public function setCategoryParent(\QuoteItemCategory $category) {
        $this->categoryParent = $category;
    }

    public function isAlwaysRollUp() {
        if(!is_null($this->categoryParent)){
            return $this->categoryParent->alwaysRollUp();
        }
        return false;
    }

    public function getCostMinimum() {
        return $this->minimum;
    }

    public function setCostMinimum($min) {
        $this->minimum = $min;
    }

}
