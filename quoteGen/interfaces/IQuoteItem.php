<?php

require_once (__DIR__ . '/ICataLogSupport.php');

/**
 *
 * @author Axian Developer
 */
interface IQuoteItem extends ICatagorySupport{
    public function getId();
    public function getName();
    public function getType();
    public function setId($id);
    public function setName($name);
    public function setType($type);
    public function setClientDatabase($dbName);
    public function getClientDatabase();
    public function getMinimumPricingDBColumnKeys();
    public function setSellPriceMinimum($min);
    public function getSellPriceMinimum();
}
