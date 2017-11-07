<?php

require_once (__DIR__ . "/../interfaces/IXmlPrintable.php");
require_once (__DIR__ . "/../interfaces/IHtmlRenderable.php");
require_once (__DIR__ . "/../interfaces/IQuoteItem.php");
require_once (__DIR__ . "/QuoteConstants.php");

/**
 * Provides the logic to handle groupings of quote line item tasks in 
 * category buckets.
 * Initial need is to provide category roll ups, or individual task reporting 
 * for the quote xml output file.
 *
 * @author Axian Developer
 */
class QuoteItemCategory implements IXmlPrintable , IHtmlRenderable, IQuoteItem{
    protected $categoryItems = array();
    protected $supportsPrintXml;
    protected $name;
    protected $shouldPrintXml;
    protected $alwaysRollUp;
    protected $id;
    protected $sellMinimum = -1;
    
    
    public function __construct($categoryName) {
        $this->name = $categoryName;
        $this->supportsPrintXml = QuoteConstants::getCategoryPrintXmlSupport($categoryName);
    }
    
    public function renderXml() {
//        if($this->shouldPrintXml || $this->alwaysRollUp) {
//            echo "This is the main category: " , $this->getName() , '<br/>';
//        } else {
//            echo "These are the the child Items for category: " , $this->getName() , '<br/>';
//            foreach($this->categoryItems as $item) {
//                $item->renderXml();
//            }
//        }
    }
    
    public function getItemsRushFees() {
        $fees = 0.0;
        foreach($this->categoryItems as $quoteItem){
            if(is_a($quoteItem, 'ProjectManagerQuoteLineItem')) {
                continue;
            }
            $fees += $quoteItem->getRushFee();
        }
        return $fees;
    }
    
    public function getItemsActualSellPriceTotalWithRushFee() {
        $priceTotal = 0.0;
        foreach($this->categoryItems as $quoteItem){
            if(is_a($quoteItem, 'ProjectManagerQuoteLineItem')) {
                if($quoteItem->isDistributed()) {
                    continue;
                }
            }
            $priceTotal += $quoteItem->getActualSellPriceTotalWithRushFee();
        }
        return $priceTotal;
    }    

    /**
     * Determines if the category should roll up all task data for the xml report (true)
     * or all/part is not rolled up (false).
     * 
     * @param type $aBooleanValue  Boolean value that indicates whether this category should
     * roll up the contained task information (true) or not (false).
     */
    public function setShouldPrintXml($aBooleanValue) {
        $bool = filter_var($aBooleanValue, FILTER_VALIDATE_BOOLEAN);
        if($this->supportsPrintXml) {
            $this->shouldPrintXml = $aBooleanValue;
            foreach($this->categoryItems as $item) {
                $item->setShouldPrintXml(!$aBooleanValue);
            }
        }
    }
    
    public function getItemsBaseCostTotal() {
        $cost = 0.0;
        foreach($this->categoryItems as $quoteItem){
            $cost += $quoteItem->getBaseCostTotal();
        }
        return $cost;
    }
    
    public function getItemsUnitCount() {
        $count = 0.0;
        
        foreach($this->categoryItems as $quoteItem){
            $count += $quoteItem->getWorkUnitCount();
        }
        return $count;
    }
    
    public function getItemsActualSellPriceTotal() {
        $priceTotal = 0.0;
        foreach($this->categoryItems as $quoteItem){
            if(is_a($quoteItem, 'ProjectManagerQuoteLineItem')) {
                if($quoteItem->isDistributed()) {
                    continue;
                }
            }
            $priceTotal += $quoteItem->getActualSellPriceTotal();
        }
        return $priceTotal;
    }
    
    public function getItemsActualSellPriceTotalNoPM() {
        $priceTotal = 0.0;
        foreach($this->categoryItems as $quoteItem){
            if(is_a($quoteItem, 'ProjectManagerQuoteLineItem')) {
                if($quoteItem->isDistributed()) {
                    continue;
                }
            }
            $priceTotal += $quoteItem->getActualSellPriceTotal();
        }
        return $priceTotal;
    }    
    
    
        public function getPrintableTasks() {
        $itemArray = array();
        foreach($this->categoryItems as $item) {
            if($item->shouldPrintXml()) {
                array_push($itemArray, $item);
            }
        }
        return $itemArray;
    }
    
    public function getRolledTasks() {
        $itemArray = array();
        foreach($this->categoryItems as $item) {
            if(!$item->shouldPrintXml()) {
                array_push($itemArray, $item);
            }
        }
        return $itemArray;
    }
    
    public function shouldPrintXml() {
        return $this->shouldPrintXml;
    }

    public function thisSupportsXmlPrinting() {
        return $this->supportsPrintXml;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function putLineItem(IQuoteItem $anItem) {
        if(empty($this->categoryItems)) {
            if(is_a($anItem, 'IXmlPrintable')) {
                $this->alwaysRollUp = $anItem->alwaysRollUp();
            }
            
        }
        array_push($this->categoryItems, $anItem);
    }
    
    public function getLineItems(){
        return $this->categoryItems;
    }

    public function alwaysRollUp() {
        return $this->alwaysRollUp;
    }
   
    public function renderHtml($parentId) {
        echo $this->buildOutput();
        
        //array_values($this->categoryItems)[0]->renderHtml();
        foreach($this->categoryItems as $quoteInfo) {
            $quoteInfo->renderHtml($parentId);
        }
    }
    public function getHtml($parentId) {
        $theOutput = $this->buildOutput();
        
        foreach($this->categoryItems as $quoteInfo) {
            //$string .= $quoteInfo->getHtml($parentId);
            $test = $quoteInfo->getHtml($parentId);
            $theOutput = $theOutput . $test;
        }
        return $theOutput;
    }
    
    public function getItemsHtml() {
        $retArray = array();
        foreach($this->categoryItems as $quoteInfo) {
            //$string .= $quoteInfo->getHtml($parentId);
            $test = $quoteInfo->getHtml(null);
            $retArray += ['task-' . $quoteInfo->getId() => $test];
        }
        return $retArray;
    }    
    
    private function buildOutput() {
        if($this->alwaysRollUp) {
            $outSpec = QuoteItemCategory::OUTPUT_SPEC_ALWAYS_ROLLS;
        } else {
            $outSpec = QuoteItemCategory::OUTPUT_SPEC;
            $outSpec = str_replace('{ROLLUP_CHECKED}', $this->shouldPrintXml() ? self::SET_CHECKED : "", $outSpec);
        }
        
        $outSpec = str_replace('{LINKNAME}', str_replace(" ", "_", $this->name), $outSpec);
        $outSpec = str_replace('{NAME}', $this->name, $outSpec);
        return str_replace('{ID}', $this->id, $outSpec);
    }

    public function getCategory() {
        return __CLASS__;
    }

    public function getId() {
        return $this->id;
    }

    public function getType() {
        return $this->name;
    }

    public function setCategory($cat) {
        // nothing here either...
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        // don't do anything
    }

    public function setType($type) {
        return "";
    }

    public function getClientDatabase() {
        
    }

    public function setClientDatabase($dbName) {
        
    }

    public function getMinimumPricingDBColumnKeys() {
        return array();
    }

    public function getSellPriceMinimum() {
        return $this->sellMinimum;
    }

    public function setSellPriceMinimum($min) {
        $this->sellMinimum = $min;
    }

    public function getCustomPrice() {
        
    }

    public function setCustomPrice($price) {
        
    }
    
    const SET_CHECKED = "checked=\"checked\"";

    const OUTPUT_SPEC =
        "<tr><td align=\"left\"><input type=\"checkbox\" class=\"{LINKNAME}rolled printroller\" name=\"{LINKNAME}rolled\" {ROLLUP_CHECKED} /></td><td colspan=\"10\" class=\"categoryHead\"><b>{NAME}</b></td></tr>";   
    
    const OUTPUT_SPEC_ALWAYS_ROLLS =
        "<tr><td align=\"left\"><input type=\"checkbox\" disabled=\"disabled\" checked=\"checked\" /></td><td colspan=\"10\" class=\"categoryHead\"><b>{NAME}</b></td></tr>";  
}

//echo QuoteItemCategory::OUTPUT_SPEC;

//$categories = array("Linguistic", "Quality Assurance");
//foreach($categories as $categoryName ) {
//    
//    echo "Printing output for category " , $categoryName, "<br/><br/>";
//
//    $category = new QuoteItemCategory($categoryName);
//
//    require_once (__DIR__ . "/XmlPrintableQuoteItemHelper.php");
//
//    for($i = 1; $i < 5; $i++) {
//        $category->putLineItem(new XmlPrintableQuoteItemHelper($categoryName, $i, 'Item' .$i, "test"));
//    }
//
//
//    $category->setShouldPrintXml(false);
//
//    echo "Should print child xml <br/>" ;
//    $category->renderXml();
//
//    $category->setShouldPrintXml(true);
//
//    echo "Should print category xml <br/>" ;
//$category->renderXml();
//}





