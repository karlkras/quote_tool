<?php

require_once (__DIR__ . '/../interfaces/IHtmlRenderable.php');
require_once (__DIR__ . '/AdditionalFeeAndDiscountItem.php');
require_once (__DIR__ . '/QuoteToolUtils.php');

/**
 * Description of AdditionalFeesAndDiscountController
 *
 * @author Axian Developer
 */
class AdditionalFeesAndDiscountController implements IHtmlRenderable{
    protected $fee_and_discount_list = array();
    
    protected $id = AdditionalFeesAndDiscountController::ID_NAME;
    
    public function getId() {
        return $this->id;
    }
    
    public function renderHtml($parentId) {
        if(!empty($this->fee_and_discount_list)){
            echo str_replace("{DIVID}", $this->getId(), AdditionalFeesAndDiscountController::DIV_IDENTIFIER);
            echo AdditionalFeesAndDiscountController::FEE_AND_DISCOUNT_FORM_BEGIN;
            $this->outputItems($parentId);
            echo $this->generateTotalSection();
            echo AdditionalFeesAndDiscountController::FEE_AND_DISCOUNT_FORM_CLOSE;
            echo AdditionalFeesAndDiscountController::END_DIV;
        }
    }
    
    public function getHtml($parentId) {
        $string ="";
        if(!empty($this->fee_and_discount_list)){
            $string .= AdditionalFeesAndDiscountController::FEE_AND_DISCOUNT_FORM_BEGIN;
            $string .= $this->getHtmlOutputIttems($parentId);
            $string .= $this->generateTotalSection();
            $string .= AdditionalFeesAndDiscountController::FEE_AND_DISCOUNT_FORM_CLOSE;
        }
        return $string;
    }
    
    public function getItems() {
        return $this->fee_and_discount_list;
    }
    
    private function generateTotalSection() {
        $string = AdditionalFeesAndDiscountController::TOTAL_ROW;
        $string = str_replace("{FORMATTED_AMOUNT}", $this->getFormattedItemsTotal(), $string);
        return $string;
    }
    
    private function outputItems($parentId) {
        foreach($this->fee_and_discount_list as $item) {
            $item->renderHtml($parentId);
        }
    }
    
    private function getHtmlOutputIttems($parentId) {
        $string = "";
        foreach($this->fee_and_discount_list as $item) {
            $string .= $item->getHtml($parentId);
        }
        return $string;
    }
    
    
    
    public function addItem(AdditionalFeeAndDiscountItem $item) {
        array_push($this->fee_and_discount_list, $item);
    }
    
    public function getSellTotal() {
        $total = 0.0;
        foreach($this->fee_and_discount_list as $item) {
            $total += $item->getAmount();
        }
        return $total;
    }
    
    private function getFormattedItemsTotal() {
        $total = $this->getSellTotal();
        $normalizedAmount = $total < 0 ? ($total * -1) : $total;
        return AdditionalFeeAndDiscountItem::formatOutPutAmount(QuoteToolUtils::getCurrencyFormattedValue($normalizedAmount), $total);
    
    }
    
    const DIV_IDENTIFIER = "<div id=\"{DIVID}\">";
    const END_DIV = "</div>";
    const ID_NAME = "additional_fees_discounts";
    
    const FEE_AND_DISCOUNT_FORM_BEGIN = 
         "<fieldset style=\"width:40%\"><legend>Additional Fees / Credits</legend><table border=\"1\" width=\"100%\" bgcolor=\"#FFFFFF\"><tbody><tr><th>Description</th><th>Amount</th></tr>";   
        
    const FEE_AND_DISCOUNT_FORM_CLOSE = 
        "</tbody></table></fieldset>";
    
    const TOTAL_ROW = 
        "<tr><td class=\"totalSpacer\">&nbsp;</td><td class=\"totalRow\">&nbsp;</td></tr><tr><td class=\"total_title\" ><strong>Additional Fees/Credits Total:</strong></td><td align=\"right\" width=\"20%\">{FORMATTED_AMOUNT}</td></tr>";
}
