<?php

include_once (__DIR__ . "/LinguistQuoteItemContainer.php");
require_once (__DIR__ . "/QuoteConstants.php");
/**
 * Description of LinguistTRandCETask
 *
 * @author Axian Developer
 */
class LinguistTRandCETask extends LinguistQuoteItemContainer {
    public function __construct($theTask, $dbName) {
        parent::__construct($theTask, $dbName);
    }

    public function getMinimumPricingDBColumnKeys() {
        return LinguistTRandCETask::buildCustomMinimumPricingLookup($this);
    }
    
    private static function buildCustomMinimumPricingLookup(LinguistQuoteItemContainer $theItem) {
        $retArray = array();
        $langSpecific = QuoteConstants::getMinimumPricingDBLookupRef("TR+CE") . "=" . str_replace(" ", "_", $theItem->getSourceLang()) . "=" . str_replace(" ", "_", $theItem->getTargetLang());
        $default = QuoteConstants::getMinimumPricingDBLookupRef("TR+CE");
        array_push($retArray, $langSpecific);
        array_push($retArray, $default);
        
        return $retArray;
    }
    
    public function getWordItemTask($itemName) {
        $obj = $this->getLineItems();
        foreach($obj as $name => $item) {
            if($name === $itemName) {
                return $item;
            }
        }
    }
    
    public function renderHtml($parentId) {
        $this->adjustForMinimum();  
        parent::renderHtml($parentId);
    }
    
    public function getHtml($parentId) {
        $this->adjustForMinimum();
        return parent::getHtml($parentId);
    } 
    
    public function getItemHtmlArray() {
        $this->adjustForMinimum();
        $ret = parent::getItemHtmlArray();
        
        return $ret;
        
    } 

    public function renderXml() {
        
    }
    
    public function getRushFee() {
        $this->adjustForMinimum();
        return parent::getRushFee();
    }
    
    public function getActualSellPriceTotal() {
        $this->adjustForMinimum();        
        return parent::getActualSellPriceTotal();
    }
    
    private function getChildBaseSell() {
        $total = 0.0;
        foreach($this->itemArray as $item){
            $total += $item->getActualSellPriceTotal();
        }
        return $total;
    }
    
    private function adjustForMinimum() {
        // let's handle basecost first...
        $this->adjustMinimumForCost();
        // and now price minimums.
        return $this->adjustMinimumForSellPrice();
    }
    
    public function getBaseCostTotal() {
        $this->adjustForMinimum();
        return parent::getBaseCostTotal();
    }
    
    private function adjustMinimumForCost() {
        $baseCost = parent::getBaseCostTotal();
        $minimum = $this->getCostMinimum();
        if($baseCost > $minimum) { 
            return;
        } 
        $obj = $this->getLineItems();
        $count = 0;
        foreach($obj as $item) {
            if($count == 0) {
                $item->setCustomCost($minimum);
            } else {
                $item->setCustomCost(0);
            }
            $count++;
        }
        return;
    }
    
    private function adjustMinimumForSellPrice() {
        $minimum = $this->getSellPriceMinimum();
        if($minimum > -1) {
            $baseSellPrice = $this->getChildBaseSell();
            if($minimum < $baseSellPrice) {
                return;
            }
            $obj = $this->getLineItems();
            $count = 0;
            foreach($obj as $item) {
                if($count == 0) {
                    $item->setCustomPrice($minimum);
                } else {
                    $item->setCustomPrice(0);
                }
                $count++;
            }
        }
    }

}
