<?php

require_once (__DIR__ . '/LinguistQuoteLineItem.php');
require_once (__DIR__ . '/LinguistQuoteItemContainer.php');

/**
 * Description of ContainedLinguistQuoteLineItem
 *
 * @author Axian Developer
 */
class ContainedLinguistQuoteLineItem extends LinguistQuoteLineItem {

    protected $containerParent;

    public function __construct(\WorkUnit $workUnit, \ILinguistQuoteItem $quoteItemInfo, LinguistQuoteItemContainer $container) {
        parent::__construct($workUnit, $quoteItemInfo);
        $this->containerParent = $container;
        $this->id = $this->id . "-" . $this->name;
    }

    public function getGroupName() {
        return "group_" . $this->containerParent->getId();
    }

//    public function getBaseCostTotal() {
//        $retVal = parent::getBaseCostTotal();
//        return $retVal <  $this->costMinimum ?  $this->costMinimum : $retVal;
//    }

}
