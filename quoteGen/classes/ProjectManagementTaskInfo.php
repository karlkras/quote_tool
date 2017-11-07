<?php
require_once( __DIR__ . '/BillableTaskData.php');
require_once (__DIR__ . '/BillableQuoteLineItemHelper.php');
require_once (__DIR__ . "/LinguistTaskCategoryFrame.php");
require_once (__DIR__ . "/../interfaces/IQuoteItem.php");

/**
 * Description of ProjectManagementTaskInfo
 *
 * @author Axian Developer
 */
class ProjectManagementTaskInfo {
    protected $id;
    protected $baseHourlyRate;
    protected $type;
    protected $name;
    protected $sellMinimum = -1;
    
    public function __construct(IQuoteItem $quoteData, $baseHourlyRate) {
        $this->id = $quoteData->getId();
        $this->baseHourlyRate = $baseHourlyRate;
        $this->type = $quoteData->getType();
        $this->name = $quoteData->getName();
        $this->setSellPriceMinimum($quoteData->getSellPriceMinimum());
    }
    
    public function getBaseHourlyRate() {
        return $this->baseHourlyRate;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getType() {
        return $this->type;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setSellPriceMinimum($min) {
        $this->sellMinimum = $min;
    }
    
    public function getSellPriceMinimum() {
        return $this->sellMinimum;
    }
    
    public function buildHelper($targLangKey) {
        $foo = new BillableTaskData(true, $this->getName(), $this->getId() . "-" . $targLangKey, $this->getType() );
        return new BillableQuoteLineItemHelper($foo, 0);
    }
    
    public function buildPMTask(LinguistTaskCategoryFrame $frame) {
        $theHelper = $this->buildHelper($frame->getId());
        $newPMItem = new ProjectManagerQuoteLineItem($theHelper, $frame);
        $newPMItem->setBaseRatePerUnit($this->getBaseHourlyRate());
        $newPMItem->setSellPriceMinimum($this->getSellPriceMinimum());
        return $newPMItem;
    }
}
