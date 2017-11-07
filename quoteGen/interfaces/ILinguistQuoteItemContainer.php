<?php

require_once (__DIR__ . "/../interfaces/IQuoteItemContainer.php");
require_once (__DIR__ . "/../interfaces/ILinguistQuoteItem.php");
require_once (__DIR__ . "/../interfaces/ILinguistQuoteItem.php");

/**
 *
 * @author Axian Developer
 */
interface ILinguistQuoteItemContainer extends IQuoteItemContainer, ILinguistQuoteItem {
    public function getMinimumPricingDBColumnKeys();
    public function getCostMinimum();
    public function setCostMinimum($min);
}
