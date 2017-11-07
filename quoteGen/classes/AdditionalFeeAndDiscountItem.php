<?php

require_once (__DIR__ . '/../interfaces/IHtmlRenderable.php');
require_once (__DIR__ . '/QuoteToolUtils.php');
/**
 * Description of AdditionalFeeAndDiscountItem
 *
 * @author Axian Developer
 */
class AdditionalFeeAndDiscountItem implements IHtmlRenderable{
    protected $description;
    protected $amount;
    protected $id;
    
    public function __construct($description, $amount, $id) {
        $this->description = $description;
        $this->amount = $amount;
        $this->id = $id;
    }
    
    public function renderHtml($parentId) {
        echo $this->getHtml($parentId);
    }
    
    public function getHtml($parentId) {
        $string = AdditionalFeeAndDiscountItem::OUTPUT_ROW_SPEC;
        $string = str_replace("{DESCRIPTION}", $this->getDescription(), $string);
        $string = str_replace("{ID}", $this->getId(), $string);
        $string = str_replace("{FORMATTED_AMOUNT}", $this->getFormattedAmount(), $string);
        return $string;
    }
    
    public function getFormattedAmount(){
        $displayAmount = $this->amount;
        if($displayAmount < 0) {
            $displayAmount = $displayAmount * -1;
        }
        return AdditionalFeeAndDiscountItem::formatOutPutAmount(QuoteToolUtils::getCurrencyFormattedValue($displayAmount), $this->amount);
    }
    
    public static function formatOutPutAmount($displayAmount, $acualAmount) {
        if($acualAmount < 0) {
            return str_replace("{AMOUNT}", $displayAmount, AdditionalFeeAndDiscountItem::FORMATTED_NEGATIVE_OUTPUT_SPEC);
        } else {
            return $displayAmount;
        }
    }
    
    public function getAmount() {
        return $this->amount;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function getId() {
        return $this->id;
    }
    
    const FORMATTED_NEGATIVE_OUTPUT_SPEC = "({AMOUNT})";
    
    const OUTPUT_ROW_SPEC = 
        "<tr><td align=\"left\" class=\"add_fee_discount_descript\">{DESCRIPTION}</td><td align=\"right\" id=\"{ID}\">{FORMATTED_AMOUNT}</td></tr>";
            
}
