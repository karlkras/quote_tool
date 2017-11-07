<?php

require_once (__DIR__ . "/../interfaces/IQuoteItemContainer.php");
require_once (__DIR__ . "/QuoteLineItem.php");
require_once (__DIR__ . "/../interfaces/IContainerXmlPrintable.php");
require_once (__DIR__ . "/../interfaces/IHtmlRenderable.php");

/**
 * Description of BaseQuoteItemCollection
 *
 * @author Axian Developer
 */
abstract class BaseQuoteItemContainer implements IQuoteItemContainer, IHtmlRenderable{
    protected $itemArray = array();
    protected $supportsXmlPrintable = true;
    protected $shouldPrintXml = true;
    protected $type;
    protected $category;
    protected $id;
    protected $name;
    protected $alwaysRollUp;
    
    public function __construct($idInfo) {
        $this->type = $idInfo->type;
        $this->category = QuoteConstants::getTaskCategory($this->type);
        $this->id = $idInfo->id;;
        $this->name = $idInfo->name;
        $this->supportsXmlPrintable = QuoteConstants::getCategoryPrintXmlSupport($this->category);
        $this->alwaysRollUp = QuoteConstants::getCategoryAlwaysRollUp($this->category);
    }
    
    public function getActualSellPriceTotal() {
        $total = 0.0;
        foreach($this->itemArray as $item){
            $total += $item->getActualSellPriceTotal();
        }
        return $total;
    }
    
    public function getActualSellPriceTotalWithRushFee() {
        $total = 0.0;
        foreach($this->itemArray as $item){
            $total += $item->getActualSellPriceTotalWithRushFee();
        }
        return $total;
    } 
    
    public function getRushFee() {
        $total = 0.0;
        foreach($this->itemArray as $item){
            $total += $item->getRushFee();
        }
        return $total;
    }     
    
    public function renderHtml($parentId) {
        foreach($this->getLineItems() as $item){
            $item->renderHtml($parentId);
        }
    }
    
    public function getHtml($parentId) {
        $string = "";
        foreach($this->getLineItems() as $item){
            $string .= $item->getHtml($parentId);
        }
        return $string;
    }
    
    public function getItemHtmlArray() {
        $retArray = array();
        foreach($this->getLineItems() as $item){
            $retArray["task-" . $item->getId()]=$item->getHtml(null);
        }
        return $retArray;
    }
    
    abstract function getMinimumPricingDBColumnKeys();
    
    public function getUnitType() {
        return array_values($this->getLineItems())[0]->getUnitType();
    }
    
    
    public function getUnitCount() {
        $count = 0;
        foreach($this->getLineItems() as $item){
            $count += $item->getWorkUnitCount();
        }
        return $count;
    }
    
    public function getBaseCostTotal() {
        $total = 0;
        foreach($this->getLineItems() as $item){
            $total += $item->getBaseCostTotal();
        }
        return $total;
    }
    
    /**
     * Adds a line item to be served by this container...
     * @param \QuoteLineItem $item
     */
    public function putLineItem(QuoteLineItem $item, $uniqueKey) {
        $this->itemArray += [$uniqueKey => $item];
    }
    
    public function getLineItem($uniqueKey) {
        return $this->itemArray[$uniqueKey];
    }
    
    public function getLineItems() {
        return $this->itemArray;
    }
    
    public function thisSupportsXmlPrinting() {
        return $this->supportsXmlPrintable;
    }
    
    public function setShouldPrintXml($aBooleanValue) {
        $bool = filter_var($aBooleanValue, FILTER_VALIDATE_BOOLEAN);
        $this->shouldPrintXml = $bool;
        foreach($this->getLineItems() as $item){
            $item->setShouldPrintXml($bool);
        }
    }
    
    public function shouldPrintXml() {
        return $this->shouldPrintXml;
    }
    
    public function getType() {
        return $this->type;
    }

    public function getCategory() {
        return $this->category;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }
    
    public function setType($type) {
        $this->type = $type;
    }

    public function setCategory($category) {
        $this->category = $category;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function alwaysRollUp() {
        return $this->alwaysRollUp;
    }
    
    public function setSellPriceMinimum($minimum){
        $this->sellPriceMinimum = $minimum;
    }
    
    public function getSellPriceMinimum() {
        return $this->sellPriceMinimum;
    }
    
    const TASK_COLLECTION_DIV_OPEN = 
        "<div class=\"taskset\">";
    
    const TASK_COLLECTION_DIV_CLOSE = 
        "</div>";    
}
