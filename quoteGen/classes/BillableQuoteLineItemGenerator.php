<?php

require_once(__DIR__ . '/BaseQuoteLineItemGenerator.php');
require_once(__DIR__ . '/BillableQuoteLineItem.php');
require_once(__DIR__ . '/BillableQuoteLineItemHelper.php');
require_once(__DIR__ . '/ProjectManager.php');
require_once(__DIR__ . '/ProjectManagerQuoteLineItem.php');
require_once(__DIR__ . '/ProjectManagementTaskInfo.php');
require_once(__DIR__ . '/BillableTaskData.php');
require_once(__DIR__ . '/QuoteToolUtils.php');

/**
 * Description of BillableQuoteLineItemGenerator
 *
 * @author Axian Developer
 */
class BillableQuoteLineItemGenerator extends BaseQuoteLineItemGenerator{
    protected $sourceLang;
    protected $helper;
    protected $projectManager;
    
    const SPECIAL_CATEGORIES = ["Document QA Specialist","Project Management", "Project Manager"];
    const PROJECT_MANAGEMENT_CATEGORY = ["Project Management", "Project Manager"];
    
    public function __construct($theTask, $sourceLang, ProjectManager $projMan, $projectObj, $pricing, PricingMySql $myDBConn) {
        parent::__construct($theTask, $projMan->getTaskCatalog(), $projectObj, $pricing, $myDBConn);
        $this->sourceLang = $sourceLang;
        $this->projectManager = $projMan;
        
        $this->BuildBillableLineItem();
    }
    
    protected function BuildBillableLineItem() {
        $theHelper = $this->buildHelper();
        
        if($this->theRawTask->btask->workRequired === 0.0 && !in_array($theHelper->getType(), BillableQuoteLineItemGenerator::SPECIAL_CATEGORIES)){
            return;
        }
        QuoteToolUtils::applyMinimumSellPrice($theHelper, $this->pricingDbTable, $this->pricingDbConnection );
        
        if(in_array($theHelper->getType(), BillableQuoteLineItemGenerator::PROJECT_MANAGEMENT_CATEGORY)) {
            if($theHelper->isDistributed() == true) {
                $test = new ProjectManagementTaskInfo($theHelper, $this->theRawTask->hourlyRate);
                $this->projectManager->setProjectMagementTaskInfo($test);
                return;
            }
            $billableItem = new ProjectManagerQuoteLineItem($theHelper, $this->projectManager);
            $this->projectManager->addProjectManagerTask($billableItem);
        } else {
            $billableItem = new BillableQuoteLineItem(new WorkUnit(WorkUnitType::enum()->hours, $this->theRawTask->btask->workRequired), $theHelper);
        }
        
        $billableItem->setBaseRatePerUnit($this->theRawTask->hourlyRate);        
        
        if(!in_array($theHelper->getType(), BillableQuoteLineItemGenerator::PROJECT_MANAGEMENT_CATEGORY)){
            $this->setCustomSellPriceAndRushRate($billableItem);
            if($this->projectObj->pricingScheme == "LLS Pricing") {
                $billableItem->setCustomRatePerUnit(60);
                $billableItem->setIsBlockASPPU();
            }
        }
        
        $this->catalogService->addItem($billableItem );
    }
    
    private function setCustomSellPriceAndRushRate(QuoteLineItem $item) {
        // set the base and custom rush fee if applicable...
        if($this->applyRushFee) {
            QuoteToolUtils::applyRushFee($item, $this->defaultRushFeePercentage, $this->applyCustomRushFees, $this->pricingDbTable , $this->pricingDbConnection);
        }
        
        // now set the custom sell price if available...
        QuoteToolUtils::applyCustomSellRate($item, $this->pricingDbTable, $this->pricingDbConnection);

    }     
    
    private function buildHelper() {
        $foo = new BillableTaskData($this->theRawTask->distributionStrategy, $this->theRawTask->btask->name, $this->theRawTask->btask->id, $this->theRawTask->btask->type );
        return new BillableQuoteLineItemHelper($foo, $this->catalogService->getNumberOfTargetLanguages($this->sourceLang), $this->pricingDbTable);
    }  
    
}
