<?php

require_once (__DIR__ . "/IQuoteItem.php");
require_once (__DIR__ . "/../classes/QuoteItemCategory.php");

/**
 *
 * @author Axian Developer
 */
interface ILinguistQuoteItem extends IQuoteItem {
    public function getSourceLang();
    public function getTargetLang();
    public function setCategoryParent(QuoteItemCategory $category);
    public function isAlwaysRollUp();
    public function setCostMinimum($min);
    public function getCostMinimum();
    
}
