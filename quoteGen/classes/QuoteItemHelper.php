<?php

require_once(__DIR__ . "/../interfaces/IQuoteItem.php");

/**
 * Description of CategoryHelper
 *
 * @author Axian Developer
 */
abstract class QuoteItemHelper implements IQuoteItem {
    protected $cat;
    protected $id;
    protected $name;
    protected $type;
    protected $clientDatabase;
    protected $sellMinimum = -1;
    
    public function __construct($category, $id, $name, $type, $clientDb = null) {
        $this->cat = $category;
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->setClientDatabase($clientDb);
    }
    
    public function getCategory() {
        return $this->cat;
    }
    
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getType() {
        return $this->type;
    }
    
    public function setId($id) {
        $this->id = $id;
    }
    public function setCategory($cat) {
        $this->cat = $cat;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function renderHtml() {
        return "";
    }
    
    public function getClientDatabase() {
        return $this->clientDatabase;
    }

    public function setClientDatabase($dbName) {
        $this->clientDatabase = $dbName;
    }
    
    public function getMinimumPricingDBColumnKeys() {
        return [QuoteConstants::getMinimumPricingDBLookupRef($this->getName())]; 
    }
    
    public function getSellPriceMinimum() {
        return $this->sellMinimum;
    }

    public function setSellPriceMinimum($min) {
        $this->sellMinimum = $min;
    }    

}

