<?php

require_once (__DIR__ . "/QuoteConstants.php");
require_once (__DIR__ . "/BaseQuoteItemContainer.php");
require_once (__DIR__ . "/../interfaces/ILinguistQuoteItemContainer.php");
require_once (__DIR__ . "/../interfaces/IHasWords.php");

/**
 * Description of LinguistQuoteItemContainer
 *
 * @author Axian Developer
 */
 abstract class LinguistQuoteItemContainer extends BaseQuoteItemContainer implements ILinguistQuoteItemContainer, IHasWords{
    protected $sourceLang;
    protected $targetLang;
    protected $type;
    protected $category;
    protected $id;
    protected $name;
    protected $categoryParent;
    protected $sellPriceMinimum = -1;
    protected $costMinimum = -1;
    // these next two added for block pricing use and need to be escalated to the
    // the language frame as these relate to language frame values, not task...
    protected $languageFrameMinimumTotal = -1;
    protected $languageFrameMinimumRushFeeTotal = -1;
    
    public function __construct($theTask, $clientDb = null) {
        parent::__construct($theTask->ltask);
        $this->sourceLang = $theTask->sourceLang;
        $this->targetLang = $theTask->targLang;
        $this->setCostMinimum($theTask->wordRateDetails->minimum);
        $this->setClientDatabase($clientDb);
    }
    
    public function setSourceLang($sourceLang) {
        $this->sourceLang = $sourceLang;
    }

    public function setTargetLang($targetLang) {
        $this->targetLang = $targetLang;
    }
    
    public function getSourceLang() {
        return $this->sourceLang;
    }

    public function getTargetLang() {
        return $this->targetLang;
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
    
    public function getWordCount() {
        return $this->getUnitCount();
    }
    
    public function setLanguageFrameMinimumRushFeeTotal($amount) {
        $this->languageFrameMinimumRushFeeTotal = $amount;
    }
    
    public function getLanguageFrameMinimumRushFeeTotal() {
        return $this->languageFrameMinimumRushFeeTotal;
    }    
    
    public function setLanguageFrameMinimumTotal($amount) {
        $this->languageFrameMinimumTotal = $amount;
    }
    
    public function getLanguageFrameMinimumTotal() {
        return $this->languageFrameMinimumTotal;
    }        
    
    public function getDisplayName() {
        return $this->type;
    }
    
    public function getCostMinimum() {
        return $this->costMinimum;
    }
    
    public function setCostMinimum($min) {
        $this->costMinimum = $min;
    }
    
    public function getClientDatabase() {
        return $this->clientDb;
    }

    public function setClientDatabase($dbName) {
        $this->clientDb = $dbName;
    }
}
