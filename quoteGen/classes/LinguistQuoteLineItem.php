<?php

require_once(__DIR__ . "/../interfaces/ILinguistQuoteItem.php");
require_once(__DIR__ . "/QuoteLineItem.php");

/**
 * Description of LinguistQuoteItem
 *
 * @author Axian Developer
 */
class LinguistQuoteLineItem extends QuoteLineItem implements ILinguistQuoteItem{
    
    protected $sourceLang;
    protected $targetLang;
    protected $categoryParent;
    protected $costMinimum = -1;
    
    public function __construct(\WorkUnit $workUnit, \ILinguistQuoteItem $quoteItemInfo) {
        parent::__construct($workUnit, $quoteItemInfo);
        $this->sourceLang = $quoteItemInfo->getSourceLang();
        $this->targetLang = $quoteItemInfo->getTargetLang();
        $this->setCostMinimum($quoteItemInfo->getCostMinimum());
    }

    public function getSourceLang() {
        return $this->sourceLang;
    }

    public function getTargetLang() {
        return $this->targetLang;
    }
    
    private static function buildCustomRatePricingLookup(LinguistQuoteLineItem $theItem) {
        $retArray = array();
        $langSpecific = $theItem->getRateDBReferenceName() . "=" . str_replace(" ", "_", $theItem->getSourceLang()) . "=" . str_replace(" ", "_", $theItem->getTargetLang());
        $default = $theItem->getRateDBReferenceName();
        array_push($retArray, $langSpecific);
        array_push($retArray, $default);
        
        return $retArray;
    }
    
    private static function buildCustomMinimumPricingLookup(LinguistQuoteLineItem $theItem) {
        $retArray = array();
        $langSpecific = $theItem->getMinimumDBReferenceName() . "=" . str_replace(" ", "_", $theItem->getSourceLang()) . "=" . str_replace(" ", "_", $theItem->getTargetLang());
        $default = $theItem->getMinimumDBReferenceName();
        array_push($retArray, $langSpecific);
        array_push($retArray, $default);
        
        return $retArray;
    }    

    public function getMinimumPricingDBColumnKeys() {
        return LinguistQuoteLineItem::buildCustomMinimumPricingLookup($this);
    }

    public function getRatePricingDBColumnKeys() {
        return LinguistQuoteLineItem::buildCustomRatePricingLookup($this);
    }

    public function renderXml() {
        
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

    public function getGroupName() {
        return "";
    }
    
    public function setCostMinimum($min) {
        $this->costMinimum = $min;
    }
    
    public function getCostMinimum () {
        return $this->costMinimum;
    }
}

