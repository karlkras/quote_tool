<?php

require_once (__DIR__ . '/TaskCatalogService.php');
require_once (__DIR__ . '/QuoteToolUtils.php');
require_once (__DIR__ . '/LinguistTaskCategoryFrame.php');
require_once (__DIR__ . '/../interfaces/IHtmlRenderable.php');
/**
 * Description of SummaryController
 *
 * @author Axian Developer
 */
class SummaryController implements IHtmlRenderable{
    private $taskService;
    private $theTasksByTarget;
    private $theSellTableHead;
    private $theCostTableHead;
    private $theGMTableHead;
    private $sortedTasksByTarget;
    private $numberOfColumnsInSummaryTable;
    private $taskFrameArray;
    private $projectMinimum;
    private $addedFeesAndDiscounts;
    private $projectManager;
    
    public function __construct(TaskCatalogService $taskService, $taskFrameArray, $addedFeesAndDiscounts, $projectMinimum = -1, $projectManager) {
        $this->taskService = $taskService;
        $this->theTasksByTarget = $this->taskService->getFullTaskListPerTarget();
        $this->projectMinimum = $projectMinimum;
        $this->sortedTasksByTarget = $this->theTasksByTarget;
        $this->projectManager = $projectManager;
        ksort($this->sortedTasksByTarget);
        
        $this->taskFrameArray = $taskFrameArray;
        $this->addedFeesAndDiscounts = $addedFeesAndDiscounts;
    }
    
    private function generateRows($lisguisRowArray, $nonDistributedRowArray, $feesAndDiscounts) {
        $rowItems = "";
        
        foreach($lisguisRowArray as $rowItem){
            $rowItems = $rowItems . $rowItem;
        }
        
        foreach($nonDistributedRowArray as $rowItem) {
            $rowItems = $rowItems . $rowItem;
        }
        
        foreach($feesAndDiscounts as $rowItem) {
            $rowItems = $rowItems . $rowItem;
        }        
        
        return $rowItems;
    }
    
    public function renderHtml($parentId) {
        $form = SummaryController::SUMMARY_FIELDSET_FORM;
        $string = str_replace("{COST_SUMMARY_TABLE}", $this->buildCostSummaryTable(), $form);
        $string = str_replace("{SELL_PRICE_SUMMARY_ID}", $this->getSellPriceSummaryDivId(), $string);
        $string = str_replace("{COST_SUMMARY_ID}", $this->getCostSummaryDivId(), $string);
        $string = str_replace("{SELL_PRICE_SUMMARY_TABLE}", $this->buildSellSummaryTable(), $string);
        $string = str_replace("{GROSS_MARGIN_ID}", $this->getGrossMarginSummaryDivId(), $string);
        $string = str_replace("{GM_SUMMARY_TABLE}", $this->buildGMSummaryTable(), $string);
   
        echo $string;
    }
    
    public function getSellPriceSummaryDivId() {
        return SummaryController::SELL_PRICE_DIV_ID;
    }
    
    public function getCostSummaryDivId() {
        return SummaryController::COST_DIV_ID;
    }
    
    public function getGrossMarginSummaryDivId() {
        return SummaryController::GROSS_MARGIN_DIV_ID;
    }
    
    public function buildSellSummaryTable() {
        $this->buildTableHeads();
        $sellItemRows = $this->buildSellRows();
        $sellNonDistributed = $this->buildSellNonDistributedRows();
        $sellFeesAndDiscounts = $this->buildSellAddedFeesAndDiscountRows();
        $sellRowTotal = $this->buildSellTotalRow();
        $sellRowItems = $this->generateRows($sellItemRows, $sellNonDistributed, $sellFeesAndDiscounts);
        
        return $this->buildSummaryTable($this->theSellTableHead, $sellRowItems, $sellRowTotal);
    }
    
    public function buildCostSummaryTable() {
        $this->buildTableHeads();
        $costItemRows = $this->buildCostRows();
        $costNonDistributed = $this->buildCostNonDistributedRows();
        $costFeesAndDiscounts = $this->buildCostAddedFeesAndDiscountRows();
        
        $costRowTotal = $this->buildCostTotalRow();
        $costRowItems = $this->generateRows($costItemRows, $costNonDistributed, $costFeesAndDiscounts);
        
        return $this->buildSummaryTable($this->theCostTableHead, $costRowItems, $costRowTotal);
    }
    
    public function buildGMSummaryTable() {
        $this->buildTableHeads();
        $string = SummaryController::GM_SUMMARY_TABLE;
        $string = str_replace("{GM_TABLE_HEADER}", $this->theGMTableHead, $string);
        $string = str_replace("{GM_TOTALS_ROWS}", $this->buildGMOutputRows(), $string);
        
        return $string;
    }
    
    private function buildSummaryTable($tableHead, $rowItems, $totalRow) {
        $string = SummaryController::SUMMARY_TABLE;
        $string = str_replace("{TABLE_HEADER}", $tableHead, $string);
        $string = str_replace("{TASK_LIST_ROWS}", $rowItems, $string);
        $string = str_replace("{TASK_TOTAL_ROW}", $totalRow, $string);
        
        return $string;
    }
        
    private function buildTableHeads() {
        $this->numberOfColumnsInSummaryTable = 2;
        $targHeads = "";
        foreach($this->sortedTasksByTarget as $targetName => $theTasks) {
            $targHeads = $targHeads . (str_replace("{NAME}", $this->formatLangHead($targetName), SummaryController::TARGET_LANG_COLUMN_HEAD));
            $this->numberOfColumnsInSummaryTable++;
        }
        
        //let's build the sell table head:
        $retString = SummaryController::SELL_TABLE_HEAD_SPEC;
        $retString = str_replace("{SOURCELANG}", $this->taskService->getSourceLang(), $retString);
        if($this->hasNondistributedTasks()) {
            $this->numberOfColumnsInSummaryTable++;
        }
        $retString = str_replace("{OTHERSERVICES}", $this->hasNondistributedTasks() ? SummaryController::OTHER_SERVICES_COLUMN : "", $retString);
        $this->theSellTableHead = str_replace("{TARGLANGCOLUMNS}", $targHeads, $retString);
        
        if(!empty($this->addedFeesAndDiscounts)){
            $this->numberOfColumnsInSummaryTable++;
        }
        $retString = str_replace("{ADDFEESDISCOUNTS}", !empty($this->addedFeesAndDiscounts) ? SummaryController::ADDIONAL_FEES_DISCOUNT_COLUMN : "", $retString);
        $this->theSellTableHead = str_replace("{TARGLANGCOLUMNS}", $targHeads, $retString);
        
        // now the gm and cost heads:
        $retString = SummaryController::COST_GM_TABLE_HEAD_SPEC;
        $retString = str_replace("{OTHERSERVICES}", $this->hasNondistributedTasks() ? SummaryController::OTHER_SERVICES_COLUMN : "", $retString);
        $retString = str_replace("{ADDFEESDISCOUNTS}", !empty($this->addedFeesAndDiscounts) ? SummaryController::ADDIONAL_FEES_DISCOUNT_COLUMN : "", $retString);
        $retString = str_replace("{TARGLANGCOLUMNS}", $targHeads, $retString);
        
        $this->theCostTableHead = $retString;
        $this->theGMTableHead = $retString;
    }
        
    private function buildSellRows(){
        return $this->buildSellOrCostRows(true);
    }
    
    private function buildCostRows() {
        return $this->buildSellOrCostRows(false);
    }
    
    private function buildSellNonDistributedRows() {
        return $this->buildNonDistributedRows(true);
    }
    
    private function buildCostNonDistributedRows() {
        return $this->buildNonDistributedRows(false);
    }
    
    private function buildSellAddedFeesAndDiscountRows() {
        return $this->buildAddedFeesAndDiscountRows(true);
    }
    
    private function buildCostAddedFeesAndDiscountRows() {
        return $this->buildAddedFeesAndDiscountRows(false);
    }
    
    private function buildSellArray(&$sellArray){
        foreach($this->sortedTasksByTarget as $targetName => $languageTaskList){
            $pmDistro = $this->projectManager->getDistroPM(QuoteToolUtils::makeLanguageId($targetName));
            if (!is_null($pmDistro)){
                array_push($languageTaskList, $pmDistro);
            } 
            foreach ($languageTaskList as $task){
                $tempArray[$task->getType()][$targetName][] = $task;
            }
        }
        
        $xCount = 0;
        $yCount = 0;
        $skipCount = 0;
        foreach($tempArray as $taskName=>$languageList){
            $xCount = 0;
            foreach($languageList as $languageName=>$taskList){
                if (count($taskList) > 1){
                    $return = $this->addRows($sellArray, $yCount, $xCount, $taskList);
                    if ($return > $skipCount){
                        $skipCount = $return;
                    }
                }else{
                    if ($this->placeTask($sellArray, $yCount, $xCount, $taskList[0], $languageName)){
                        $xCount--;
                    }
                }
                $xCount++;
            }
            if ($skipCount > 0){
                $yCount += $skipCount;
                $skipCount = 0;
            }else{
                $yCount++;
            }
        }
        $this->fillNulls($sellArray);
        $this->sortSellArray($sellArray, $this->sortedTasksByTarget);
    }
    
    private function sortSellArray(&$sellArray, $sortOrderArray){
        $xMax = count($sellArray);
        $yMax = 0;
        foreach ($sellArray as $column){
            if (count($column)>$yMax){
                $yMax = count($column);
            }
        }
        $tempIndex = 0;
        foreach($sortOrderArray as $targetName => $languageTaskList){
            $yIndex = $this->findLanguageIndex($sellArray, $targetName);
            for($xIndex=0; $xIndex<$xMax; $xIndex++){
                $tempArray[$xIndex][] = $sellArray[$xIndex][$yIndex];
            }
            $tempIndex++;
        }
        $sellArray = $tempArray;
    }
    
    private function placeTask(&$theArray, $firstIndex, $secondIndex, $theTask, $currentLanguage){
        if (!array_key_exists($firstIndex-1, $theArray)){
            $theArray[$firstIndex][$secondIndex] = $theTask;
            return false;
        } else {
            $rowLanguage = $this->findRowLangauge($theArray,$secondIndex);
            if ((!is_null($rowLanguage)) && ($rowLanguage != $currentLanguage)){
                $this->placeTask($theArray, $firstIndex, $secondIndex+1, $theTask, $currentLanguage);
                return true;
            }else{
                $theArray[$firstIndex][$secondIndex] = $theTask;
                return false;
            }
        }
    }
    
    private function findRowLangauge($theArray,$index){
        foreach($theArray as $column){
            if (!is_null($column[$index])){
                return $column[$index]->getTargetLang();
            }
        }
        return null;
    }
    
    private function findLanguageIndex($theArray,$language){
        foreach($theArray as $column){
            foreach($column as $yIndex=>$item){
                if (!is_null($item) && ($item->getTargetLang() == $language)){
                    return $yIndex;
                }
            }
        }
        return null;
    }
    
    private function addRows(&$sellArray, &$y, &$x, $taskArray){
        $tempY = $y;
        $skipCount = 0;
        foreach ($taskArray as $task){
            $sellArray[$tempY][$x] = $task;
            $tempY++;
            $skipCount++;
        }
        return $skipCount;
    }
    
    private function fillNulls(&$theArray){
        $xMax = count ($theArray);
        $yMax = 0;
        foreach ($theArray as $taskList){
            if (count($taskList) > $yMax){
                $yMax = count($taskList);
            }
        }
        
        for($x=0;$x<$xMax;$x++){
            for($y=0;$y<$yMax;$y++){
                if(!key_exists($y, $theArray[$x])){
                    $theArray[$x][$y] = null;
                }
            }
        }
    }
    
    private function buildSellOrCostRows($boolGetSell) {
        $sellArray = array();
        $this->buildSellArray($sellArray);
        
        
        // so now that we've done that let's do us some rows...
        $rowArray = array();
        $columnVals = "";
        
        foreach($sellArray as $itemArray) {
            if (!is_null($itemArray[0])){
              $rowName = $itemArray[0]->getDisplayName();
            } else {
                foreach($itemArray as $item){
                    if (!is_null($item)){
                        $rowName = $item->getDisplayName();
                        break;
                    }
                }
            }
            $columnVals = "";
            $newRow = str_replace("{TASKNAME}", $rowName, SummaryController::TASK_ROW);
            $rowTotal = 0;
            for($index=0; $index < count($itemArray); $index++) {
                if (!is_null($itemArray[$index])){
                    $thisTotal = $boolGetSell ? $itemArray[$index]->getActualSellPriceTotal() : $itemArray[$index]->getBaseCostTotal();
                    $rowTotal += $thisTotal;
                    $columnVals = $columnVals . str_replace("{PRICE}", QuoteToolUtils::getCurrencyFormattedValue($thisTotal), SummaryController::PRICE_COLUMN);
                } else {
                    $columnVals = $columnVals . SummaryController::BLANK_COLUMN;
                }
            }
            if($this->hasNondistributedTasks()) {
                 $columnVals = $columnVals . SummaryController::BLANK_COLUMN;
            }
            if(!empty($this->addedFeesAndDiscounts)) {
                 $columnVals = $columnVals . SummaryController::BLANK_COLUMN;
            }
            $columnVals = $columnVals . str_replace("{PRICE}", QuoteToolUtils::getCurrencyFormattedValue($rowTotal), SummaryController::PRICE_COLUMN);
            array_push($rowArray, str_replace("{PRICECOLUMNS}",$columnVals, $newRow));
        }
        return $rowArray;
    }
    
    private function buildNonDistributedRows($boolGetSell) {
        $nonDist = $this->taskService->getNonDistributedBillableTasks();
        $rowArray = array();
        if(!is_null($nonDist) && ! empty($nonDist)){
            $theTasksByTarget = $this->taskService->getFullTaskListPerTarget();
            $blankColumns = count(array_keys($theTasksByTarget));
            
            foreach($nonDist as $val){
                $newRow = str_replace("{TASKNAME}", $val->getDisplayName(), SummaryController::TASK_ROW);
                $rowItems = "";
                for($i = 0; $i < $blankColumns; $i++){
                    $rowItems = $rowItems . SummaryController::BLANK_COLUMN;
                }
                $outVal = $boolGetSell ? $val->getActualSellPriceTotal() : $val->getBaseCostTotal();
                $rowItems = $rowItems . str_replace("{PRICE}", QuoteToolUtils::getCurrencyFormattedValue($outVal), SummaryController::PRICE_COLUMN);
                if(!empty($this->addedFeesAndDiscounts)) {
                    $rowItems = $rowItems . SummaryController::BLANK_COLUMN;
                }
                $rowItems = $rowItems . str_replace("{PRICE}", QuoteToolUtils::getCurrencyFormattedValue($outVal), SummaryController::PRICE_COLUMN);
                array_push($rowArray, str_replace("{PRICECOLUMNS}", $rowItems, $newRow));
            }
        }
        return  $rowArray;
    }
    
    private function buildAddedFeesAndDiscountRows($boolGetSell) {
        $rowArray = array();
        if(!empty($this->addedFeesAndDiscounts)){
            $theTasksByTarget = $this->taskService->getFullTaskListPerTarget();
            $blankColumns = count(array_keys($theTasksByTarget));
            $blankColumns += $this->hasNondistributedTasks() ? 1 : 0;
            
            foreach($this->addedFeesAndDiscounts as $item) {
                $newRow = str_replace("{TASKNAME}", $item->getDescription(), SummaryController::TASK_ROW);
                $rowItems = "";
                for($i = 0; $i < $blankColumns; $i++){
                    $rowItems = $rowItems . SummaryController::BLANK_COLUMN;
                }
                $outVal = $boolGetSell ? $item->getAmount() : 0;
                if($outVal < 0) {
                    $outVal = $item->getFormattedAmount();
                    
                } else {
                    $outVal = QuoteToolUtils::getCurrencyFormattedValue($outVal);
                }
                $rowItems = $rowItems . str_replace("{PRICE}", $outVal, SummaryController::PRICE_COLUMN);
                $rowItems = $rowItems . str_replace("{PRICE}", $outVal, SummaryController::PRICE_COLUMN);
                array_push($rowArray, str_replace("{PRICECOLUMNS}", $rowItems, $newRow));
            }
        }
        return  $rowArray;
    }
    
    private function buildCostTotalRow(){
        return $this->buildTotalRow(false);
    }
    
    private function buildSellTotalRow() {
        return $this->buildTotalRow(true);
    }
    
    private function buildTotalRow($boolGetSell) {
        
        $sortedFrames = $this->taskFrameArray;
        ksort($sortedFrames);
        
        //foreach($sortedFrames as $lang => $frame)
        //$langKeys = array_keys($this->sortedTasksByTarget);
        $totalRow = SummaryController::TOTAL_ROW;
        $totals = "";
        $finalTotalVal = 0.0;
        $val = 0.0;
        
        foreach($sortedFrames as $lang => $frame) {
            $totals = $totals . $this->buildTotalTD($boolGetSell, $frame, $val);
            $finalTotalVal += $val;
        }
        // now the nondistributed if we have any...
        if($this->hasNondistributedTasks()) {
            $nonDist = $this->taskService->getCategorizedNonDistributableBillingTasks();
            foreach($nonDist as $category){
                $val = $boolGetSell ? $category->getItemsActualSellPriceTotal() : $category->getItemsBaseCostTotal();
                $replace = str_replace("{TOTAL}", QuoteToolUtils::getCurrencyFormattedValue($val),$this->buildCleanTotalTD($val));
                $totals = $totals . $replace;
                $finalTotalVal += $val;
                break; // there should only be one category.
            }
        }
        
        // now the fees and discounts... if we have any...
        if(!empty($this->addedFeesAndDiscounts)) {
            $totalAmount = 0;
            foreach($this->addedFeesAndDiscounts as $item) {
                $totalAmount += $boolGetSell ? $item->getAmount() : 0;
            }
            $finalTotalVal += $totalAmount;
            
            $outVal = "";
            if($totalAmount < 0) {
                $outVal = AdditionalFeeAndDiscountItem::formatOutPutAmount(QuoteToolUtils::getCurrencyFormattedValue(($totalAmount * -1)), $totalAmount);
            } else {
                $outVal = QuoteToolUtils::getCurrencyFormattedValue($totalAmount);
            }
            $replace = str_replace("{TOTAL}", $outVal,  $this->buildCleanTotalTD($outVal));
            $totals = $totals . $replace;
        }
        
        // check to see if the calculated sell total is less then the minimum if stated...
        $totalTitleEntry = "";
        $addedTotalClass = "";
        if($boolGetSell) {
            if($this->projectMinimum != -1) {
                if($this->projectMinimum > $finalTotalVal) {
                    $totalTitleEntry = str_replace("{TITLE}", LinguistTaskCategoryFrame::CUSTOM_TOTAL_TITLE, QuoteLineItem::TITLE_ELEMENT);
                    $addedTotalClass = QuoteLineItem::PRICE_IS_CUSTOM;
                    $finalTotalVal = $this->projectMinimum;
                }
            }
            if($this->projectManager->isProjectRushMinimum()) {
                $totalTitleEntry = str_replace("{TITLE}", ProjectManager::CUSTOM_TOTAL_TITLE, QuoteLineItem::TITLE_ELEMENT);
                $addedTotalClass = QuoteLineItem::PRICE_IS_CUSTOM;
            }
        }
        $replace = SummaryController::TOTAL_COLUMN_VALUE;
        $replace = str_replace("{TOTALISCUSTOM}", $addedTotalClass,$replace);
        $replace = str_replace("{TOTALTITLE}", $totalTitleEntry,$replace);
        $replace = str_replace("{TOTAL}", QuoteToolUtils::getCurrencyFormattedValue($finalTotalVal),$replace);
        
        $totals = $totals . $replace;
        
        // and finally...
        $theEntireRow = str_replace("{TOTALS}", $totals, $totalRow);
        
        $theEntireRow = str_replace("{COLUMNCOUNT}", $this->numberOfColumnsInSummaryTable - 1, $theEntireRow);
        
        return $theEntireRow;
    }
    
    private function buildCleanTotalTD($total) {
        $replace = SummaryController::TOTAL_COLUMN_VALUE;
        $replace = str_replace("{TOTALISCUSTOM}", "",$replace);
        $replace = str_replace("{TOTALTITLE}", "",$replace);
        $pos = strpos($total, "$");
        
        if($pos !== false) {
            $replace = str_replace("{TOTAL}", $total,$replace);
        } else {
            $replace = str_replace("{TOTAL}", QuoteToolUtils::getCurrencyFormattedValue($total),$replace);
        }
        
        return $replace;
    }
    
    private function buildTotalTD($boolGetSell, $lingFrame, &$total) {
        $totalEntry = self::TOTAL_COLUMN_VALUE;
        $total = $boolGetSell ? $lingFrame->getLinguistSellPriceTotal() : $lingFrame->getLinguistCosts();
        
        $totalTitleEntry = "";
        $addedTotalClass = "";
        if($boolGetSell) {
            if($lingFrame->isTotalMinimumApplied()) {
                $totalTitleEntry = str_replace("{TITLE}", LinguistTaskCategoryFrame::CUSTOM_TOTAL_TITLE, QuoteLineItem::TITLE_ELEMENT);
                $addedTotalClass = QuoteLineItem::PRICE_IS_CUSTOM;
            }
        }
        $totalEntry = str_replace('{TOTALISCUSTOM}', $addedTotalClass, $totalEntry);
        $totalEntry = str_replace('{TOTALTITLE}', $totalTitleEntry, $totalEntry);
        $totalEntry = str_replace("{TOTAL}", QuoteToolUtils::getCurrencyFormattedValue($total), $totalEntry);
        
        return $totalEntry;
    }
    
    private function buildGMOutputRows() {
        $percentTotals = "";
        $priceTotals = "";
        $finalSellTotalVal = 0.0;
        $finalCostTotalVal = 0.0;
        $sortedFrames = $this->taskFrameArray;
        ksort($sortedFrames);
        foreach($sortedFrames as $lang => $frame) {
            $this->projectManager->getDistroPMSellTotal($frame->getId());
            $sellPriceVal = $frame->getLinguistSellPriceTotal();
            $costPriceVal = $frame->getLinguistCosts();    
            $priceTotals = $priceTotals . str_replace("{AMOUNT}", QuoteToolUtils::getCurrencyFormattedValue($sellPriceVal - $costPriceVal), SummaryController::GM_COLUMN_VALUE);
            $percentTotals = $percentTotals . str_replace("{AMOUNT}", round((($sellPriceVal - $costPriceVal) / $sellPriceVal) * 100, 1), SummaryController::GM_COLUMN_VALUE);
            $finalSellTotalVal += $sellPriceVal;
            $finalCostTotalVal += $costPriceVal;
        }
        
        // now the nondistributed if we have any...
        if($this->hasNondistributedTasks()) {
            $nonDist = $this->taskService->getCategorizedNonDistributableBillingTasks();
            foreach($nonDist as $theCategory){
                $sellPriceVal = $theCategory->getItemsActualSellPriceTotal();
                $costPriceVal = $theCategory->getItemsBaseCostTotal();
                $priceTotals = $priceTotals . str_replace("{AMOUNT}", QuoteToolUtils::getCurrencyFormattedValue($sellPriceVal - $costPriceVal), SummaryController::GM_COLUMN_VALUE);
                $percentTotals = $percentTotals . str_replace("{AMOUNT}", $sellPriceVal == 0 ? 0 : round((($sellPriceVal - $costPriceVal) / $sellPriceVal) * 100, 1), SummaryController::GM_COLUMN_VALUE);
                $finalSellTotalVal += $sellPriceVal;
                $finalCostTotalVal += $costPriceVal;
                break; // there should only be one category.
            }
        }
        // now the fees and discounts... if we have any...
        if(!empty($this->addedFeesAndDiscounts)) {
            $totalAmount = 0;
            foreach($this->addedFeesAndDiscounts as $item) {
                $totalAmount += $item->getAmount();
            }
            $sellPriceVal = $totalAmount;
            $costPriceVal = 0;
            
            $outVal = "";
            if($totalAmount < 0) {
                $outVal = AdditionalFeeAndDiscountItem::formatOutPutAmount(QuoteToolUtils::getCurrencyFormattedValue(($totalAmount * -1)), $totalAmount);
            } else {
                $outVal = QuoteToolUtils::getCurrencyFormattedValue($totalAmount);
            }
            
            $priceTotals = $priceTotals . str_replace("{AMOUNT}", $outVal, SummaryController::GM_COLUMN_VALUE);
            $percentTotals = $percentTotals . str_replace("{AMOUNT}", "0", SummaryController::GM_COLUMN_VALUE);
            
            $finalSellTotalVal += $sellPriceVal;
            $finalCostTotalVal += $costPriceVal;
        }
        
        if($this->projectMinimum != -1) {
            if($this->projectMinimum > $finalSellTotalVal) {
                $finalSellTotalVal = $this->projectMinimum;
            }
        }
        
        $priceTotals = $priceTotals . str_replace("{AMOUNT}", QuoteToolUtils::getCurrencyFormattedValue($finalSellTotalVal - $finalCostTotalVal), SummaryController::GM_COLUMN_VALUE);
        $percentTotals = $percentTotals . str_replace("{AMOUNT}", round((($finalSellTotalVal - $finalCostTotalVal) / $finalSellTotalVal) * 100, 1), SummaryController::GM_COLUMN_VALUE);
        
        // and finally...`
        $theEntireShabang = str_replace("{GM_PRICE_DIFFERENCE}", $priceTotals, SummaryController::GM_ROWS);
        
        $theEntireShabang = str_replace("{GM_PERCENT_DIFFERENCE}", $percentTotals, $theEntireShabang);
        
        return $theEntireShabang;
    }
    
    private function hasNondistributedTasks() {
        return count($this->taskService->getNonDistributedBillableTasks()) !== 0;
        
    }
    
    private function formatLangHead($raw) {
        return str_replace(" ", "<br/>", $raw);
    }

    public function getHtml($parentId) {
        // do nothing.
    }

    const TARGET_LANG_COLUMN_HEAD =
        "<th>{NAME}</th>";
    
    const OTHER_SERVICES_COLUMN = "<th>Other<br/>Services</th>";
    
    const ADDIONAL_FEES_DISCOUNT_COLUMN = "<th>Additional<br/>Fees<br/>and<br/>Discounts</th>";
    
    const SELL_TABLE_HEAD_SPEC = 
        "<th>{SOURCELANG} to =&gt;</th>{TARGLANGCOLUMNS}{OTHERSERVICES}{ADDFEESDISCOUNTS}<th>Task<br/>Total</th>";
    
    const COST_GM_TABLE_HEAD_SPEC = 
        "<th>&nbsp;</th>{TARGLANGCOLUMNS}{OTHERSERVICES}{ADDFEESDISCOUNTS}<th>Task<br/>Total</th>";
    
    const TOTAL_ROW = 
        "<tr><td class=\"totalSpacer\">&nbsp;</td><td colspan=\"{COLUMNCOUNT}\" class=\"totalRow\">&nbsp;</td></tr><tr><td align=\"right\" style=\"font-weight:bold; border: none;\">Totals:</td>{TOTALS}</tr>";
    
    const TOTAL_COLUMN_VALUE = "<td {TOTALISCUSTOM} align=\"right\" ><input type=\"text\" size=\"8\" {TOTALTITLE} class=\"summary\" value=\"{TOTAL}\" readonly=\"readonly\" tabindex=\"-1\"/></td>";
        //"<td align=\"right\" style=\"font-weight:bold\">{TOTAL}</td>";
    
    const GM_COLUMN_VALUE = "<td align=\"center\">{AMOUNT}</td>";
    
    const BLANK_COLUMN = "<td>&nbsp;</td>";
    
    const TASK_ROW = 
        "<tr><td>{TASKNAME}</td>{PRICECOLUMNS}</tr>";
    
    const GM_ROWS = 
        "<tr><td>$</td>{GM_PRICE_DIFFERENCE}</tr><tr><td>%</td>{GM_PERCENT_DIFFERENCE}</tr>";
    
    const PRICE_COLUMN = 
            "<td align=\"right\">{PRICE}</td>";
    
    const SUMMARY_TABLE = 
        "<table border=\"1\" width=\"100%\" bgcolor=\"#FFFFFF\"><tbody><tr>{TABLE_HEADER}</tr>{TASK_LIST_ROWS}{TASK_TOTAL_ROW}</tbody></table>";
    
    const SELL_PRICE_DIV_ID = "sell_price_summary";
    
    const COST_DIV_ID = "cost_summary";
    
    const GROSS_MARGIN_DIV_ID = "gross_margin_summary";
    
    const GM_SUMMARY_TABLE = 
        "<table border=\"1\" width=\"100%\" bgcolor=\"#FFFFFF\"><tbody><tr>{GM_TABLE_HEADER}</tr>{GM_TOTALS_ROWS}</tbody></table>";
            
    
    const SUMMARY_FIELDSET_FORM = 
        "<div id=\"summary_section\"><fieldset><legend>Summary</legend><fieldset id=\"sellPriceField\" style=\"width:100%;\"><legend id=\"sellPriceLegend\">Sell Price</legend><div id=\"{SELL_PRICE_SUMMARY_ID}\">{SELL_PRICE_SUMMARY_TABLE}</div></fieldset><fieldset id=\"costField\" style=\"width:100%;\"><legend id=\"costLegend\">Cost</legend><div id=\"{COST_SUMMARY_ID}\">{COST_SUMMARY_TABLE}</div></fieldset><fieldset id=\"gmField\" style=\"width:80;\"><legend id=\"gmLegend\">Gross Margin</legend><div id=\"{GROSS_MARGIN_ID}\">{GM_SUMMARY_TABLE}</div></fieldset></fieldset></div>";
}
