<?php

require_once (__DIR__ . '/../interfaces/IHtmlRenderable.php');
require_once (__DIR__ . '/BillableQuoteLineItem.php');
require_once (__DIR__ . '/TaskCategoryFrame.php');
include_once (__DIR__ . '/QuoteToolUtils.php');

/**
 * Description of OtherTaskCategoryFrame
 *
 * @author Axian Developer
 */
class OtherTaskCategoryFrame extends TaskCategoryFrame{
    protected $catObj;
    
    public function __construct($otherCat) {
        parent::__construct("Other Services", "other_services");
        $this->catObj = $otherCat;
    }
    
    public function getCategories() {
        return $this->catObj;
    }
    
    public function getItemsHtml() {
        return $this->catObj->getItemsHtml();
    }
    
    public function getPrintableTasks() {
        $itemArray = array();
        foreach($this->catObj as $item) {
            if($item->shouldPrintXml()) {
                array_push($itemArray, $item);
            }
        }
        return $itemArray;
    }
    
    public function getRolledTasks() {
        $itemArray = array();
        foreach($this->catObj as $item) {
            if(!$item->shouldPrintXml()) {
                array_push($itemArray, $item);
            }
        }
        return $itemArray;
    }
    
    public function buildTotalSection() {
        $output = OtherTaskCategoryFrame::TOTAL_SECTION;
        $string = str_replace("{TOTAL_ROW}", $this->totalRowToHtml(), $output);
        return $string;
    }

    public function outputChildrenTasks($parentId) {
        $this->catObj->renderHtml($parentId);
    }
    
    
    public function totalRowToHtml() {
        $output = OtherTaskCategoryFrame::TOTAL_ROW;
        $string = str_replace("{COST}", QuoteToolUtils::getCurrencyFormattedValue($this->getCategoryCosts()), $output);
        $string = str_replace("{TOTAL}", QuoteToolUtils::getCurrencyFormattedValue($this->getCategorySellPriceTotal()), $string);
        $string = str_replace("{GROSSMARGIN}", $this->getItemsGrossMarginPercentage(), $string);
        return $string;
    }
    
    
    public function getChildrenTasksHtml($fameId) {
        return $this->catObj->getHtml($fameId);
    }

    public function buildHeaderOutput() {
        return str_replace("{NAME}", $this->getName(), OtherTaskCategoryFrame::TABLE_HEAD) ;
    }

    public function closeFrame() {
        echo OtherTaskCategoryFrame::TABLE_CLOSE;
    }
    
    public function getCategoryCosts() {
        return $this->catObj->getItemsBaseCostTotal();
    }
    
    public function getCategorySellPriceTotal() {
        return $this->catObj->getItemsActualSellPriceTotal();
    }
    
    public function getItemsRushfees() {
        return $this->catObj->getItemsRushFees();
    }
    
    public function getItemsGrossMarginPercentage() {
        $sellTotal = $this->getCategorySellPriceTotal();
        $baseCost = $this->getCategoryCosts();
        if($sellTotal == 0) {
            return 0;
        }
        return round((($sellTotal - $baseCost) / $sellTotal) * 100, 1);
    }    
    
    
    const TABLE_HEAD = 
        "<fieldset><legend>{NAME}</legend><table border=\"1\" width=\"100%\" bgcolor=\"#FFFFFF\"><tbody><tr><th>Printable</th><th>Name</th><th colspan=\"2\">Units</th><th>Rate</th><th>Cost</th><th>% Margin</th><th>Calculated<br/>Sell Price<br/>Per Unit</th><th>Actual<br/>Sell Price<br/>Per Unit</th><th>Actual<br/>Sell Price</th><th>Actual<br/>GM%</th></tr>";
    
    const TOTAL_SECTION =
        "<tr><td colspan=\"5\" class=\"totalSpacer\">&nbsp;</td><td colspan=\"6\" class=\"totalRow\">&nbsp;</td></tr>{TOTAL_ROW}";       
    
    const TOTAL_ROW = 
        "<tr id=\"other_total_row\"><td colspan=\"5\" class=\"total_title\" >Totals:</td><td align=\"right\" class=\"cost\">{COST}</td><td colspan=\"3\">&nbsp;</td><td align=\"right\" class=\"asp\">{TOTAL}</td><td align=\"right\" class=\"gross_margin\">{GROSSMARGIN}</td></tr>";       
    
    const TABLE_CLOSE = 
        "</tbody></table></fieldset></div>";
    
}
