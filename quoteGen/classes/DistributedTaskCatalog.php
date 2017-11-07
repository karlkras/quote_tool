<?php

require_once(__DIR__ . '/BillableQuoteLineItem.php');
require_once(__DIR__ . '/QuoteToolUtils.php');

/**
 * Description of DistributedCatalog
 *
 * @author Axian Developer
 */
class DistributedTaskCatalog {
    protected $taskArray = array();
    public function addDistributedTask($targetLang, BillableQuoteLineItem $theItem){
        $idLang = QuoteToolUtils::makeLanguageId($targetLang);
        if(!array_key_exists($idLang, $this->taskArray)){
             $this->taskArray[$idLang] = array();
        }
        
        if(!array_key_exists($theItem->getId(), $this->taskArray[$idLang])){
             $this->taskArray[$idLang][$theItem->getId()] = $theItem;
        }
    }
    
    public function getDistributedArray() {
        return $this->taskArray;
    }
}
