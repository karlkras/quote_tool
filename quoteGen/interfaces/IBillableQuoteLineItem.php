<?php

require_once (__DIR__ . "/IQuoteItem.php");
require_once(__DIR__ . '/../enums/DistributedTypeEnum.php');

/**
 *
 * @author Axian Developer
 */
interface IBillableQuoteLineItem extends IQuoteItem{
    public function getDistributedType();
    public function getTargetLangCount();
    public function setTargetLangCount($count);
    
}
