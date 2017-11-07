<?php

require_once (__DIR__ . '/IQuoteItem.php');
/**
 *
 * @author Axian Developer
 */
interface IQuoteItemContainer extends IQuoteItem{
    public function getActualSellPriceTotal();
    public function putLineItem(QuoteLineItem $item, $uniqueKey);
    public function getLineItem($uniqueKey);
    public function getLineItems();
    public function getUnitCount();
    public function getUnitType();
}
