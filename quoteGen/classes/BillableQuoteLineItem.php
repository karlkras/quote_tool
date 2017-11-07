<?php

require_once (__DIR__ . "/../interfaces/IBillableQuoteLineItem.php");
require_once (__DIR__ . "/QuoteLineItem.php");
require_once (__DIR__ . "/QuoteToolUtils.php");

/**
 * Description of BillableQuoteLineItem
 *
 * @author Axian Developer
 */


class BillableQuoteLineItem  extends QuoteLineItem implements IBillableQuoteLineItem {
    protected $marginPercentage = 50; //default
    protected $distributedType;
    protected $targetLangCount = 0;
    protected $rateLookupKeys = array();
    protected $minimumLookupKeys = array();
    
    public function __construct(WorkUnit $workUnit, IBillableQuoteLineItem $quoteItemInfo) {
        parent::__construct($workUnit, $quoteItemInfo);
        $this->distributedType = $quoteItemInfo->getDistributedType();
        $this->targetLangCount = $quoteItemInfo->getTargetLangCount();
        $this->rateLookupKeys = [QuoteConstants::getRatePricingDBLookupRef($this->getName())];
        $this->minimumLookupKeys = [QuoteConstants::getMinimumPricingDBLookupRef($this->getName())];
    }
    
    public function getRatePricingDBColumnKeys() {
        return $this->rateLookupKeys;
    }
    
    public function getMinimumPricingDBColumnKeys() {
        return $this->minimumLookupKeys;
    }
    
    public function setRatePricingDBColumnKeys(array $keys){
        $this->rateLookupKeys = $keys;
    }
    
    public function setMinimumPricingLookupKeys(array $keys) {
        $this->minimumLookupKeys = $keys;
    }

    public function getDistributedType() {
        return $this->distributedType;
    }

    public function getTargetLangCount() {
        return $this->targetLangCount;
    }
    
    public function isDistributed() {
        return $this->distributedType != DistributedTypeEnum::enum()->not;
    }

    public function renderXml() {
        
    }

    public function getGroupName() {
        return "";
    }

    public function setTargetLangCount($count) {
        $this->targetLangCount = $count;
    }

}

