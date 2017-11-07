<?php

require_once(__DIR__ . '/QuoteItemHelper.php');
require_once(__DIR__ . '/../interfaces/IBillableQuoteLineItem.php');
require_once(__DIR__ . '/BillableTaskData.php');


/**
 * Description of BillingQuoteItemHelper
 *
 * @author Axian Developer
 */
class BillableQuoteLineItemHelper extends QuoteItemHelper implements IBillableQuoteLineItem{
    
    protected $distributedType;
    protected $targetLangCount = 0;
    protected $categoryParent;
    
    public function __construct(BillableTaskData $theTaskData, $targetLangCount, $clientDb = null) {
        $this->distributedType = $theTaskData->getDistributionStrategy();
        parent::__construct(QuoteConstants::getTaskCategory($theTaskData->getType(), 
                !$this->isDistributed()), $theTaskData->getId(), 
                $theTaskData->getName(), $theTaskData->getType(),
                $clientDb);
        
        $this->targetLangCount = $targetLangCount;
    }

    public function isDistributed() {
        return $this->distributedType != DistributedTypeEnum::enum()->not;
    }

    public function getTargetLangCount() {
        return $this->targetLangCount;
    }

    public function getDistributedType() {
        return $this->distributedType;
    }

    public function renderHtml() {
    }

    public function setTargetLangCount($count) {
        $this->targetLangCount = $count;
    }

}
