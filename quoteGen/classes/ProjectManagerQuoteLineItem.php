<?php

require_once (__DIR__ . '/BillableQuoteLineItem.php');
require_once (__DIR__ . '/ProjectManager.php');
require_once (__DIR__ . '/TaskCatalogService.php');
require_once (__DIR__ . '/QuoteToolUtils.php');
require_once (__DIR__ . '/../interfaces/IPMInfoSupport.php');

/**
 * Description of ProjectManagerQuoteItem
 *
 * @author Axian Developer
 */
class ProjectManagerQuoteLineItem extends BillableQuoteLineItem {
    protected $pmSupport;
    protected $customPMPercent = -1;
    
    public function __construct(IBillableQuoteLineItem $quoteItemInfo, IPMInfoSupport $pmSupport) {
        //$this->projectManager = $projMan;
        parent::__construct(new WorkUnit(WorkUnitType::enum()->hours, 0), $quoteItemInfo);
        $this->pmSupport = $pmSupport;
        $this->setSellPriceMinimum($quoteItemInfo->getSellPriceMinimum());
    }    
    
    public function getActualSellPriceTotal() {
        $projTotal = $this->pmSupport->getPMSellTotal();
        $markupPercent = $this->getPercentage();
        $retTotal = $projTotal / (1-($markupPercent/100)) * ($markupPercent/100);
        
        if($this->getSellPriceMinimum() > $retTotal) {
            $retTotal = $this->getSellPriceMinimum();
            $this->setCustomPrice($retTotal);
        } else {
            $this->setCustomPrice(-1);
        }
        return $retTotal;
    }
    
    private function getPercentage() {
        return $this->customPMPercent == -1 ? $this->pmSupport->getPMPercent() : $this->customPMPercent;
    }
    
    public function setCustomPMPercent($percent) {
        $this->customPMPercent = $percent;
    }
    
    public function getBaseCostTotal() {
        $count = $this->getWorkUnitCount();
        $costTotal = $count * $this->workUnits->getBaseRatePerUnit();
        return $costTotal;
    }
    
    public function shouldPrintXml() {
        return true;
    }
    
    public function getWorkUnitCount() {
        $sellTotal = $this->pmSupport->getPMSellTotal();
        $pmASP = ($sellTotal / (1-($this->pmSupport->getPMPercent()/100)) * ($this->pmSupport->getPMPercent()/100));
        $testTotal = round( ($pmASP / 55)*4)/4;
        return $testTotal;
    }
    
    protected function buildHtmlOutput() {
        return $this->buildDistributed();
    }

    private function buildDistributed() {
        $outputSpec = ProjectManagerQuoteLineItem::LINEITEM_SPEC;
        $rollup = "";
        if($this->alwaysRollUpCategory ) {
            $rollup =   QuoteLineItem::NOT_SHOW_ROLLUP;
        } else {
            $spec = QuoteLineItem::SHOW_ROLLUP;
            $categoryName = $this->getCategory();
            $rollup = str_replace('{CATEGORY}', str_replace(" ", "_", $categoryName),$spec);
            $groupName = "";
            if(!empty($this->getGroupName())) {
                $groupName = " " . $this->getGroupName();
            }
            $rollup = str_replace('{GROUPNAME}', $groupName,$rollup);
            
            $rollup = str_replace('{ROLLUP_CHECKED}', $this->shouldPrintXml() ? self::SET_CHECKED : "", $rollup);
        }
        $this->getActualSellPriceTotal();
        
        $priceTitleEntry = "";
        $addedPriceClass = "";
        if($this->getCustomPrice() != -1) {
            $priceTitleEntry = str_replace("{TITLE}", QuoteLineItem::CUSTOM_PRICE_TITLE, QuoteLineItem::TITLE_ELEMENT);
            $addedPriceClass = QuoteLineItem::PRICE_IS_CUSTOM;
        }
        
        $ratesAreEditable = isset($_SESSION['ratesAreEditable']) ? true : false;
        
        $string = str_replace('{DISPLAYNAME}', $this->getDisplayName(), $outputSpec);
        $string = str_replace('{UNITCOUNT}', $this->getWorkUnitCount(), $string);
        $string = str_replace('{UNITTYPE}', $this->getWorkUnitType(), $string);
        $string = str_replace('{ID}', $this->getId(), $string);
        $string = str_replace('{PMPERCENT}', $this->getPercentage(), $string);
        $string = str_replace('{RATE}', QuoteToolUtils::getCurrencyFormattedValue($this->getBaseRatePerUnit()), $string);
        $string = str_replace('{RATEREADONLY}', $ratesAreEditable ? "" : QuoteLineItem::READONLY, $string);
        $string = str_replace('{RATEEDITABLE}', $ratesAreEditable ? QuoteLineItem::EDITABLE : "", $string);
        $string = str_replace('{COST}', $this->getFormattedCostTotal(), $string);
        $string = str_replace('{PRICETITLE}', $priceTitleEntry, $string);
        $string = str_replace('{ASPISCUSTOM}', $addedPriceClass, $string);
        $string = str_replace('{ASP}', $this->getFormattedActualSellPriceTotal(), $string);
        $string = str_replace('{ROLLUP}', $rollup, $string);
        $string = str_replace('{GMP}', $this->getActualGrossMarginPercentage(), $string);

        return $string;
    }
    
    function isMinimumApplied(){
        return $this->pmSupport->isTotalMinimumApplied();
    }
    
    const LINEITEM_SPEC = 
        "<tr id=\"task-{ID}\" class=\"projman\"><td align=\"right\">&nbsp;</td><td>{DISPLAYNAME}</td><td class=\"unitcount\" >{UNITCOUNT}</td><td>{UNITTYPE}</td><td><input type=\"text\" size=\"6\" value=\"{RATE}\" class=\"{RATEEDITABLE} rate decimalOnly\" {RATEREADONLY} /></td><td align=\"right\" ><input type=\"text\" size=\"6\" value=\"{COST}\" class=\"cost\" readonly=\"readonly\" tabindex=\"-1\" /></td><td colspan=\"3\"><input type=\"text\" class=\"pmpercent editable wholeNumberOnly\" name=\"pmpercent\" value=\"{PMPERCENT}\" size=\"6\"/><span class=\"instruction\">% of total sale price</span></td><td {ASPISCUSTOM} align=\"right\" ><input type=\"text\" size=\"6\" class=\"asp\" {PRICETITLE} value=\"{ASP}\" readonly=\"readonly\" tabindex=\"-1\" /></td><td class=\"gmp\" align=\"right\">{GMP}</td></tr>";
    
    const LINEITEM_SPEC_NOT_DISTRIBUTED = 
        "<tr id=\"task-{ID}-row1\"><td align=\"right\"><input type=\"checkbox\" class=\"task_rollup\"/></td><td>{DISPLAYNAME}</td><td><input type=\"text\" name=\"units\" value=\"{UNITCOUNT}\" size=\"6\" class=\"units editable decimalOnly\"></td><td>{UNITTYPE}</td><td><input type=\"text\" size=\"6\" value=\"{RATE}\" class=\"rate decimalOnly\" readonly /></td><td align=\"right\" ><input type=\"text\" size=\"6\" value=\"{COST}\" class=\"cost\" readonly /></td><td colspan=\"5\" style=\"background-color:#CCCCCC\">&nbsp;</td></tr><tr id=\"task-{ID}-row2\"><td colspan=\"6\" style=\"background-color:#CCCCCC\">&nbsp;</td><td align=\"right\"><input type=\"text\" class=\"pmpercent editable wholeNumberOnly\" name=\"pmpercent\" value=\"{PMPERCENT}\" size=\"6\"></td><td class=\"instruction\">% of total<br>sale price</td><td>&nbsp;</td><td class=\"asp\" align=\"right\">{ASP}</td><td class=\"grossmargin\" align=\"right\">{GMP}</td></tr>";    
}
