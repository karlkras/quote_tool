<?php

require_once (__DIR__ . '/LinguistQuoteLineItem.php');
require_once (__DIR__ . '/ContainedLinguistQuoteLineItem.php');
require_once (__DIR__ . '/LinguistQuoteItemHelper.php');
require_once (__DIR__ . '/LinguistTRandCETask.php');
require_once (__DIR__ . '/BaseQuoteLineItemGenerator.php');

/**
 * Description of LinguisticQuoteLineItemGenerator
 *
 * @author Axian Developer
 */

class LinguisticQuoteLineItemGenerator extends BaseQuoteLineItemGenerator{
    protected $wordItemArray = array();
    protected $projectManager;
    
    
    public function __construct($theTask,ProjectManager $projMan, $projectObj, $pricing, PricingMySql $myDBConn) {
        parent::__construct($theTask, $projMan->getTaskCatalog(), $projectObj, $pricing, $myDBConn);
        
        $this->projectManager = $projMan;
        
        if($theTask->ltask->type === 'TR+CE' || $theTask->ltask->type === 'TR/CE'|| $theTask->ltask->type === 'TR'
                || $theTask->ltask->type === 'BT' || $theTask->ltask->type === 'TCP') {
            $this->BuildTranslateCopyEditTask($theTask->ltask->type === 'TR' ? 'TR' : 'TR+CE');
        } else {
            $this->BuildLinguisLineItem();
        }
    }
    
    protected function BuildLinguisLineItem () {
        $theType = $this->theRawTask->ltask->type;
        
        $theEnum = QuoteLineItemEnum::enum()->$theType;
        
        $aHelper = new LinguistQuoteItemHelper($this->theRawTask, $this->pricingDbTable);
        
        $newTask = $this->createHourlyTask($theEnum, $this->theRawTask->ltask->workRequired, $aHelper);
        // set the base rate...
        $rateKey = $newTask->getStandardRateKey();
        $rate = $this->theRawTask->wordRateDetails->$rateKey;
        $newTask->setBaseRatePerUnit($rate);
        
        $this->setCustomSellPriceAndRushFee($newTask);
        
        if($newTask->getType() === "PR") {
            if(!$this->projectManager->chargeForProofreading() && is_null($this->pricingDbTable)) {
                $newTask->setCustomPrice(0);
            }
        }
        if($this->projectObj->pricingScheme == "LLS Pricing") {
            $newTask->setCustomRatePerUnit(QuoteToolUtils::getStandardBlockPricingOnHourly(true, $this->theRawTask));
            if($newTask->getType() === "PR") {
                if($this->applyRushFee) {
                    // so standard hourly for a linguist task is 65. If rush is requested
                    // on a proofreading task it should be 95. 1.4615 is about as close
                    // as one can get to hitting that amount.
                    $newTask->setCustomRushFeePercentage(0.4615);
                }
            }
        }
        $this->catalogService->addItem($newTask);
    }
    
    protected function BuildTranslateCopyEditTask($type = 'TR+CE') { 
        //build linguistTask Object
        $linguistTaskContainer = new LinguistTRandCETask($this->theRawTask, $this->pricingDbTable);
        if($type == 'TR+CE') {
            $this->initializeTRandCEWordItems($linguistTaskContainer, $this->theRawTask);
        } else {
            $this->initializeTRWordItems($linguistTaskContainer, $this->theRawTask);
        }
        $theWordItems = $this->getWordQuoteItems();
        
        $totalWordCount = 0;
        foreach($theWordItems as $wordItem) {
            $totalWordCount += $wordItem->getWorkUnitCount();
        }
        
        $loopCount = 0;
        foreach($theWordItems as $wordItem) {
            // set the base rate...
            $rateKey = $wordItem->getStandardRateKey();
            $rate = $this->theRawTask->wordRateDetails->$rateKey;
            $wordItem->setBaseRatePerUnit($rate);
            $this->setCustomSellPriceAndRushFee($wordItem);
            if($this->projectObj->pricingScheme == "LLS Pricing") {
                if($loopCount == 0) {
                    $sourceLang = $wordItem->getSourceLang();
                    $targetLang = $wordItem->getTargetLang();
                    $foo = strpos($sourceLang, 'English');

                    $blockLang = is_bool($foo) ? $sourceLang : $targetLang;
                    $standardMinimum = -1;
                    $rushMinimum = -1;
                    $sellPrice = QuoteToolUtils::getStandardBlockPricingForTRCE($blockLang, $totalWordCount, $standardMinimum, $rushMinimum);
                    $wordItem->setBlockPrice($sellPrice);
                    $linguistTaskContainer->setLanguageFrameMinimumRushFeeTotal($rushMinimum);
                    $linguistTaskContainer->setLanguageFrameMinimumTotal($standardMinimum);
                } else {
                    $wordItem->setBlockPrice(0);
                }
            }
            $linguistTaskContainer->putLineItem($wordItem, $wordItem->getName());
            $loopCount++;
        }
        // now set the custom sell price if available...
        QuoteToolUtils::applyMinimumSellPrice($linguistTaskContainer, $this->pricingDbTable, $this->pricingDbConnection);
        
        $this->catalogService->addItem($linguistTaskContainer);
        // now, if this is block pricing we may need to manufacture a format task...
        if($this->theRawTask->categoryName === "Non-Trados TR/TR+CE Task") {
            $dtpPrice = 0.0;
            $dtpRate = 0.0;
            $dtpWorkRequired = 0;
            
            QuoteToolUtils::getFormatTaskDataFromDoctansTRCE($this->theRawTask, $dtpPrice, $dtpRate, $dtpWorkRequired);
            if($dtpPrice > 0) {
                $formatTask = $this->projectManager->getFormatTaskForNonTratos();
                if(is_null($formatTask)) {
                    $theHelper = $this->buildFormat1Helper();
                    $billableItem = new BillableQuoteLineItem(new WorkUnit(WorkUnitType::enum()->hours, $dtpWorkRequired), $theHelper);
                    //$billableItem->setCustomPrice($dtpPrice);
                    $billableItem->setBaseRatePerUnit($dtpRate);
                    $billableItem->setUnitsEditable();
                    if($this->projectObj->pricingScheme == "LLS Pricing") {
                        $billableItem->setCustomRatePerUnit(60);
                    }
                    $this->setCustomSellPriceAndRushFee($billableItem);
                    $this->projectManager->setFormatTaskForNonTratos($billableItem);
                } else {
                    $dtpWorkRequired += $formatTask->getWorkUnitCount();
                    $formatTask->setWorkUnitCount($dtpWorkRequired);
                }
            }
        }
    }
    
    private function setCustomSellPriceAndRushFee(QuoteLineItem $item) {
        // set the base sell rush rate if applicable...
        if($this->applyRushFee) {
            QuoteToolUtils::applyRushFee($item, $this->defaultRushFeePercentage, $this->applyCustomRushFees, $this->pricingDbTable , $this->pricingDbConnection);
        }
        
        // now set the custom sell price if available...
        QuoteToolUtils::applyCustomSellRate($item, $this->pricingDbTable, $this->pricingDbConnection);
    }    
    
    protected function putWordQuoteItem(QuoteLineItem $theItem) {
        array_push($this->wordItemArray, $theItem);
    }
    
    protected function getWordQuoteItems(){
        return $this->wordItemArray;
    }

    protected function initializeTRandCEWordItems(IQuoteItem $quoteItemInfo, $theTask){
        if($theTask->categoryName === "Non-Trados TR/TR+CE Task") {
            $this->putWordQuoteItem($this->createWordTask(QuoteLineItemEnum::enum()->tr_ce_words, $theTask->wordCounts->wordCount, $quoteItemInfo));
            return;
        }
        if (($theTask->wordCounts->fuzzyWords == 0) && ($theTask->wordCounts->matchRepsWords == 0)) {
            $this->putWordQuoteItem($this->createWordTask(QuoteLineItemEnum::enum()->tr_ce_new_text, $theTask->wordCounts->newWords, $quoteItemInfo));
        } else {
            if($theTask->wordCounts->newWords > 0) {
                $this->putWordQuoteItem($this->createWordTask(QuoteLineItemEnum::enum()->tr_ce_new_text,  $theTask->wordCounts->newWords, $quoteItemInfo));
            }
            if($theTask->wordCounts->fuzzyWords > 0) {
                $this->putWordQuoteItem($this->createWordTask(QuoteLineItemEnum::enum()->tr_ce_fuzzy_text,  $theTask->wordCounts->fuzzyWords, $quoteItemInfo));
            }
            if($theTask->wordCounts->matchRepsWords > 0) {
                $this->putWordQuoteItem($this->createWordTask(QuoteLineItemEnum::enum()->tr_ce_matchrep_text,  $theTask->wordCounts->matchRepsWords, $quoteItemInfo));
            }
        }
    }
    
    protected function initializeTRWordItems(IQuoteItem $quoteItemInfo, $theTask){
        if($theTask->categoryName === "Non-Trados TR/TR+CE Task") {
            $this->putWordQuoteItem($this->createWordTask(QuoteLineItemEnum::enum()->tr_words, $theTask->wordCounts->wordCount, $quoteItemInfo));
            return;
        }
        if (($theTask->wordCounts->fuzzyWords == 0) && ($theTask->wordCounts->matchRepsWords == 0)) {
            $this->putWordQuoteItem($this->createWordTask(QuoteLineItemEnum::enum()->tr_new_text, $theTask->wordCounts->newWords, $quoteItemInfo));
        } else {
            if($theTask->wordCounts->newWords > 0) {
                $this->putWordQuoteItem($this->createWordTask(QuoteLineItemEnum::enum()->tr_new_text,  $theTask->wordCounts->newWords, $quoteItemInfo));
            }
            if($theTask->wordCounts->fuzzyWords > 0) {
                $this->putWordQuoteItem($this->createWordTask(QuoteLineItemEnum::enum()->tr_fuzzy_text,  $theTask->wordCounts->fuzzyWords, $quoteItemInfo));
            }
            if($theTask->wordCounts->matchRepsWords > 0) {
                $this->putWordQuoteItem($this->createWordTask(QuoteLineItemEnum::enum()->tr_matchrep_text,  $theTask->wordCounts->matchRepsWords, $quoteItemInfo));
            }
        }
    }    
    
    protected function createWordTask(QuoteLineItemEnum $theEnum, $count, IQuoteItem $quoteItemInfo) {
        $orgName = $quoteItemInfo->getName();
        $quoteItemInfo->setName($theEnum->getName());
        $newTask = new ContainedLinguistQuoteLineItem(new WorkUnit(WorkUnitType::enum()->words, $count),$quoteItemInfo, $quoteItemInfo);
        $quoteItemInfo->setName($orgName);
        return $this->populateTask($theEnum, $newTask);
    }
    
    protected function createHourlyTask(QuoteLineItemEnum $theEnum, $count, $quoteItemInfo) {
        
        $newTask = new LinguistQuoteLineItem(new WorkUnit(WorkUnitType::enum()->hours, $count),$quoteItemInfo);
        return $this->populateTask($theEnum, $newTask);
    }
    
    protected function populateTask(QuoteLineItemEnum $theEnum, IQuoteItem $item) {
        //$item->set(QuoteConstants::getRatePricingDBLookupRef($theEnum->getName()));
        $item->setDescription(QuoteConstants::getDescription($theEnum->getName()));
        $item->setStandardRateKey(QuoteConstants::getStandardRateKey($theEnum->getName()));
        $item->setDisplayName(QuoteConstants::getDisplayName($theEnum->getName()));
        return $item;
    }
    
    private function buildFormat1Helper() {
        $id = mt_rand(1000000,9999999);
        $taskData = new BillableTaskData("evenly", "Format 1", $id, "Formatting Specialist" );
        return new BillableQuoteLineItemHelper($taskData, $this->catalogService->getNumberOfTargetLanguages($this->theRawTask->sourceLang), $this->pricingDbTable);
    }
}

