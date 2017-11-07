<?php

require_once(__DIR__ . "/../interfaces/ICatalogSupport.php");
require_once(__DIR__ . "/BaseQuoteItemContainer.php");

/**
 * Description of CatalogService
 *
 * @author Axian Developer
 */
abstract class CatalogService {
    protected $taskArray = array();
    
    abstract public function addItem(ICatagorySupport $item);
    abstract public function getAllItems();
    
    public function enumerateById() {
        $allItems = $this->getAllItems();
        $retItems = array();
        foreach($allItems as $value) {
            $retItems += [$value->getId() => $value];
            if(is_a($value, "BaseQuoteItemContainer")){
                $lineItems = $value->getLineItems();
                foreach($lineItems as $lineItem) {
                    $retItems += [$lineItem->getId() => $lineItem];
                }
            }
        }
        return $retItems;
    }
}
