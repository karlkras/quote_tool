<?php

require_once (__DIR__ . "/../enums/QuoteLineItem.Enum.php" );
require_once (__DIR__ . "/WorkUnit.php");
require_once (__DIR__ . "/QuoteConstants.php");
require_once (__DIR__ . "/../interfaces/IQuoteItem.php");
require_once (__DIR__ . "/../interfaces/IXmlPrintable.php");
require_once (__DIR__ . "/../interfaces/IHtmlRenderable.php");
require_once (__DIR__ . "/QuoteToolUtils.php");

/**
 * Description of QuoteLineItem
 *
 * @author Axian Developer
 */
abstract class QuoteLineItem implements IQuoteItem, IXmlPrintable, IHtmlRenderable {
    protected $workUnits;    
    protected $standardRateKey;
    protected $pricingRateDBLookupRef;
    protected $pricingMinimumDBLookupRef;
    protected $displayName;
    protected $name;
    protected $category;
    protected $description;
    protected $type;
    protected $id;
    protected $defaultRushFeePercentage = 0.0;
    protected $customRushFeePercentage = 0.0;
    protected $marginPercentage = 40; //default
    protected $supportsXmlPrintable = true;
    protected $shouldPrintXml = false;
    protected $alwaysRollUpCategory;
    protected $userSetSellPricePerUnit = -1;
    protected $clientDb;
    protected $customCost = -1;
    protected $customPrice = -1;
    protected $overridePrice = -1;
    protected $sellMinimum = -1;
    protected $sellBlock = -1;
    protected $isBlockASPPU = false;
    protected $userSetMarginPercent = -1;
    protected $areUnitsEditable = false;
    
    
    public function __construct(WorkUnit $workUnit, IQuoteItem $quoteItemInfo) {
        $this->workUnits = $workUnit;
        $this->name = $quoteItemInfo->getName();
        $this->id = $quoteItemInfo->getId();
        $this->category = $quoteItemInfo->getCategory();
        $this->type = $quoteItemInfo->getType();
        $this->clientDb = $quoteItemInfo->getClientDatabase();
        $this->pricingMinimumDBLookupRef = QuoteConstants::getMinimumPricingDBLookupRef(QuoteLineItem::typeHack($this->name));
        $this->pricingRateDBLookupRef = QuoteConstants::getRatePricingDBLookupRef(QuoteLineItem::typeHack($this->name));
        $this->supportsXmlPrintable = QuoteConstants::getCategoryPrintXmlSupport($this->category);
        $this->alwaysRollUpCategory = QuoteConstants::getCategoryAlwaysRollUp($this->category);
        $this->setSellPriceMinimum($quoteItemInfo->getSellPriceMinimum());
    }
    
    private static function typeHack($name) {
        
        if(strpos($name, 'OLR')) {
            return "OLR";
        }
        
        if(strpos($name, 'ICR')) {
            return "ICR";
        }
        
        if(strpos($name, 'PR')) {
            return "PR";
        }
        return $name;
    } 
    
    abstract function getGroupName();
    
    public function getId(){
        return $this->id;
    }
    
    public function getCategory() {
        return $this->category;
    }
    public function getName() {
        return $this->name;
    }
    
    public function getType() {
        return $this->type;
    }
    
    public function setDisplayName($dname) {
        $this->displayName = $dname;
    }
    
    public function getDisplayName() {
        return is_null($this->displayName) ?  $this->name : $this->displayName;
    }
    
    public function setCustomPrice($price) {
        $this->customPrice = $price;
    }
    
    public function setBlockPrice($price) {
        $this->sellBlock = $price;
    }
    
    public function getBlockPrice() {
        return $this->sellBlock;
    }
    
    public function setIsBlockASPPU() {
        $this->isBlockASPPU = true;
    }
    
    public function setUnitsEditable() {
        $this->areUnitsEditable = true;
    }
    
    public function areUnitsEditable() {
        return $this->areUnitsEditable;
    }
    
    private function isCustomBlockASSPU() {
        return $this->isBlockASPPU;
    }
    
    public function getCustomPrice() {
        return $this->customPrice;
    }
    
    public function setCustomCost($cost) {
        $this->customCost = $cost;
    }
    
    public function getCustomCost() {
        return $this->customCost;
    }    
    
    public function renderHtml($parentId) {
        echo  $this->buildHtmlOutput($parentId);
    }
    
    public function getHtml($parentId){
        return $this->buildHtmlOutput($parentId);
    }
    
    abstract public function getRatePricingDBColumnKeys();
    
    public function getMinimumPricingDBColumnKeys() {
        return [QuoteConstants::getMinimumPricingDBLookupRef($this->getName())];
    }    
    
    public function setDescription($desc) {
        $this->description = $desc;
    }
    
    public function getDescription() {
        return is_null($this->description) ? "" : $this->description;
    }
    
    public function setStandardRateKey($key) {
        $this->standardRateKey = $key;
    }
    public function getStandardRateKey() {
        return $this->standardRateKey;
    }
    
    protected function getRateDBReferenceName() {
        return $this->pricingRateDBLookupRef;
    }    
    
    protected function getMinimumDBReferenceName() {
        return $this->pricingRateDBLookupRef;
    }      
    
    public function setWorkUnitCount($count){
        $this->workUnits->setUnitCount($count);
    }
    
    public function getWorkUnitCount(){
        return $this->workUnits->getUnitCount();
    }    
    
    public function setBaseRatePerUnit($rate){
        $this->workUnits->setBaseRatePerUnit($rate);
    }
    
    public function getBaseRatePerUnit(){
        return $this->workUnits->getBaseRatePerUnit();
    }
    
    public function setCustomRatePerUnit($rate){
        $this->workUnits->setCustomRatePerUnit($rate);
    }
    
    public function getCustomRatePerUnit(){
        return $this->workUnits->getCustomRatePerUnit();
    }
    
    public function setUserMarginPecentage($percent) {
        $this->userSetMarginPercent = $percent;
    }
    
    public function setMarginPercentage($percent){
        $this->marginPercentage = $percent;
    }
    
    public function getMarginPercentage() {
        return $this->userSetMarginPercent > -1 ? $this->userSetMarginPercent : $this->marginPercentage;
    }
    
    public function getWorkUnitType() {
        return  $this->workUnits->getUnitType();
    }
    
    public function setWorkUnitType(WorkUnitType $theEnum) {
        $this->workUnits->setUnitType($theEnum);
    }    
    
    public function getRushFee() {
        $rushFeePercent = $this->getRushFeePercentage();
        if($rushFeePercent > 0){
            $amount = ceil(($this->getActualSellPriceTotal() * $rushFeePercent)*100)/100;
            if($this->getWorkUnitType() == WorkUnitType::enum()->hours) {
                if($this->isCustomRushFeePercentage()) {
                    $rate = $this->getActualRatePerUnit();
                    $multiplier = round($rate * $rushFeePercent, 0);
                    $amount = $multiplier * $this->getWorkUnitCount();
                }
            }
            return $amount;
        } else {
            return 0;
        }
    }
    
    private function isRushFeePercentCustom($rushFeePercent) {
        return $rushFeePercent != 0.25 || $rushFeePercent != 0.5 || $rushFeePercent != 1.0;
    }
    
    public function getActualSellPriceTotalWithRushFee() {
        $actualSellPrice = $this->getActualSellPriceTotal();
        $rushFee = $this->getRushFee();
        return $actualSellPrice + $rushFee;
    }
    
    public function isCustomRushFeePercentage() {
        return $this->customRushFeePercentage > 0;
    }

    public function getActualRatePerUnit($format = true) {
        if ($format){
            $retval = $this->formatPricePerUnit($this->workUnits->getActualRatePerUnit());
        }else{
            $retval = $this->workUnits->getActualRatePerUnit();
        }
        return $retval;
    }
       
    private function formatPricePerUnit($value) {
        $formattedActual = 0;
        if($this->workUnits->getUnitType() == 'words')
        {
            // need to round up to next 100th
            $formattedActual = ceil($value * 100)/100;
        } elseif($this->workUnits->getUnitType() == 'hours'){
            // round to the nearest dollar.
            $formattedActual = ceil($value);
        }
        return number_format($formattedActual, 3);
    }
    
    public function setDefaultRushFeePercentage($rushPercent){
        if (FALSE === is_numeric($rushPercent)) {
            throw new InvalidArgumentException('QuoteLineItem set_RushFeePercentage expected Argument 1 to be numeric');
        }
        $this->defaultRushFeePercentage = $rushPercent;
    }
    
    public function setCustomRushFeePercentage($rushPercent){
        if (FALSE === is_numeric($rushPercent)) {
            throw new InvalidArgumentException('QuoteLineItem set_RushFeePercentage expected Argument 1 to be numeric');
        }
        if($this->defaultRushFeePercentage != $rushPercent){
            $this->customRushFeePercentage = $rushPercent;
        } else {
            $this->customRushFeePercentage = 0.0;
        }
    }
    
    public function getCalculatedSellPricePerUnit() {
        if($this->getBaseCostTotal() == 0 || 
                $this->workUnits->getUnitCount() == 0 || 
                $this->getMarginPercentage() == 0) {
            return 0;
        }
        $baseCalc = $this->getBaseCostTotal()  / (1 - ($this->getMarginPercentage() / 100)) / $this->workUnits->getUnitCount();
        return round(($baseCalc), 5 ,PHP_ROUND_HALF_UP);
    }
    
    public function getActualSellPricePerUnit() {
        $ret = 0;
        if($this->userSetSellPricePerUnit != -1 ) {
            $ret = $this->userSetSellPricePerUnit;
        } else {
            $ret = $this->isCustomPricePerUnit() ? $this->getCustomRatePerUnit() : 
                QuoteToolUtils::roundUp($this->getCalculatedSellPricePerUnit());
        }
        return $ret;
    }
    
    public function setUserSellPricePerUnit($amount){
        $this->userSetSellPricePerUnit = $amount;
    }
    
    public function isCustomPricePerUnit(){
        return $this->workUnits->isCustomUnitRate();
    }
    
    public function getDefaultRushFeePercentage() {
        return  $this->defaultRushFeePercentage;
    }
    
    public function getRushFeePercentage(){
        return $this->customRushFeePercentage == 0.0 ? $this->defaultRushFeePercentage : $this->customRushFeePercentage;
    }
    
    public function getBaseCostTotal() {
        if($this->customCost != -1){
            return $this->customCost;
        }
        $retVal = 0;
        $retVal = $this->workUnits->getBaseRatePerUnit();
        $retVal = $retVal * $this->workUnits->getUnitCount();
        return $retVal;
    }
    
    public function getActualSellPriceTotal() {
        if($this->getBlockPrice() > -1) {
            return $this->getBlockPrice();
        }
        if($this->customPrice > -1) {
            return $this->customPrice;
        }
        $retVal = $this->getActualSellPricePerUnit();
        $retVal = $retVal * $this->workUnits->getUnitCount();
        return $retVal;
    }
    
    public function getActualGrossMarginPercentage() {
        if($this->getBaseCostTotal() == 0){
            return 0;
        }
        
        if(($this->isCustomPricePerUnit() && $this->getCustomRatePerUnit() == (int)0)
                || $this->getActualSellPriceTotal() == (int)0 ) {
            return 0;
        }
        return round((($this->getActualSellPriceTotal() - $this->getBaseCostTotal()) / $this->getActualSellPriceTotal()) * 100, 1);
    }
    
    public function getFormattedCostTotal(){
        if($this->getBaseCostTotal() == 0) {
            return QuoteToolUtils::getCurrencyFormattedValue($this->getBaseCostTotal());
        }
        return $this->getFormattedTotal($this->getBaseCostTotal());
    }
        
    
    public function getFormattedActualSellPriceTotal(){
        return $this->getFormattedTotal($this->getActualSellPriceTotal());
    }    
    
    private function getFormattedTotal($rawTotal) {
        $fmtr = $a = new \NumberFormatter("en-US", \NumberFormatter::CURRENCY); 
        return $fmtr->format($rawTotal);
    }
    
    public function setCategory($cat) {
        $this->category = $cat;
        
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function thisSupportsXmlPrinting() {
        return $this->supportsXmlPrintable;
    }
    
    public function setShouldPrintXml($aBooleanValue) {
        $bool = filter_var($aBooleanValue, FILTER_VALIDATE_BOOLEAN);
        $this->shouldPrintXml = $bool;
    }
    
    public function shouldPrintXml() {
        return $this->shouldPrintXml;
    }
    
    
    public function alwaysRollUp() {
        return $this->alwaysRollUpCategory;
        
    }
    
    public function getClientDatabase() {
        return $this->clientDb;
    }

    public function setClientDatabase($dbName) {
        $this->clientDb = $dbName;
    }    
    
    public function getSellPriceMinimum() {
        return $this->sellMinimum;
    }

    public function setSellPriceMinimum($min) {
        $this->sellMinimum = $min;
    }   
    
    protected function buildHtmlOutput($parentId) {
        
        $outputSpec = QuoteLineItem::LINEITEM_SPEC;
        
        $divId = $this->getId();
        
        if(!is_null($parentId)) {
            if(is_a($this, 'BillableQuoteLineItem')){
                if($this->isDistributed()){
                    $divId = $divId . "-" . $parentId;
                }
            }
        }
        $divId = "task-" . $divId;
        
        $tableData = $this->buildTableData();
        
        $string = str_replace('{ID}', $divId, $outputSpec);
        $string = str_replace('{TABLEDATA}', $tableData, $string);
        
        return $string;
    }
    
    public function buildTableData() {
        
        $theUnitEntry = $this->getWorkUnitCount();
        if($this->getWorkUnitType() == WorkUnitType::enum()->words) {
            $theUnitEntry = number_format($theUnitEntry);
        }
        if(QuoteConstants::getCategoryCanEditUnits($this->getCategory()) || $this->areUnitsEditable()) {
            $theUnitEntry = str_replace("{UNITS}", $theUnitEntry, QuoteLineItem::UNITS_EDITABLE);
        }
        
        $asppuTitleEntry = "";
        $addedASPPUClass = "";
        if($this->isCustomPricePerUnit()) {
            $overRideTest = (float)$this->userSetSellPricePerUnit;
            $customRatePer = (float)$this->getCustomRatePerUnit();
            if($overRideTest != -1 && $overRideTest !== $customRatePer) {
                $addedASPPUClass = $this->userSetSellPricePerUnit > $this->getCustomRatePerUnit() ? 
                        QuoteLineItem::ASPPU_IS_OVERRIDDEN_HIGHER : QuoteLineItem::ASPPU_IS_OVERRIDDEN_LOWER;
                $asppuTitleEntry = str_replace("{TITLE}", QuoteLineItem::CUSTOM_ASPPUOVERRIDE_TITLE, QuoteLineItem::TITLE_ELEMENT);
            } else {
                $addedASPPUClass = QuoteLineItem::ASPPU_IS_CUSTOM;
                $asppuTitleEntry = $costTitleEntry = str_replace("{TITLE}", 
                        $this->isCustomBlockASSPU() ? self::BLOCK_ASPPU_TITLE : self::CUSTOM_ASPPU_TITLE, self::TITLE_ELEMENT);
            }
        }
        
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
        
        $costTitleEntry = "";
        $addedCostClass = "";
        if($this->getCustomCost() != -1) {
            $costTitleEntry = str_replace("{TITLE}", QuoteLineItem::CUSTOM_COST_TITLE, QuoteLineItem::TITLE_ELEMENT);
            $addedCostClass = QuoteLineItem::COST_IS_CUSTOM;
        }
        
        $priceTitleEntry = "";
        $addedPriceClass = "";
        if($this->getCustomPrice() != -1) {
            $priceTitleEntry = str_replace("{TITLE}", QuoteLineItem::CUSTOM_PRICE_TITLE, QuoteLineItem::TITLE_ELEMENT);
            $addedPriceClass = QuoteLineItem::PRICE_IS_CUSTOM;
        }
        
        if(empty($priceTitleEntry)) {
            if($this->getBlockPrice() > -1) {
                $priceTitleEntry = str_replace("{TITLE}", QuoteLineItem::BLOCK_PRICE_TITLE, QuoteLineItem::TITLE_ELEMENT);
                $addedPriceClass = QuoteLineItem::PRICE_IS_LLS;
            }
        }
        
        $ratesAreEditable = isset($_SESSION['ratesAreEditable']) ? true : false;
        
            
        $string = QuoteLineItem::TABLE_DATA_SPEC;
        $string = str_replace('{DISPLAYNAME}', htmlentities($this->getDisplayName()), $string);
        $string = str_replace('{UNITCOUNT}', $theUnitEntry, $string);
        $string = str_replace('{UNITTYPE}', $this->getWorkUnitType(), $string);
        $string = str_replace('{RATE}', $this->getCategory() == "Linguistic" ? $this->getBaseRatePerUnit() : QuoteToolUtils::getCurrencyFormattedValue($this->getBaseRatePerUnit()), $string);
        $string = str_replace('{RATEREADONLY}', $ratesAreEditable ? "" : self::READONLY, $string);
        $string = str_replace('{RATEEDITABLE}', $ratesAreEditable ? self::EDITABLE : "", $string);
        $string = str_replace('{COST}', $this->getFormattedCostTotal(), $string);
        $string = str_replace('{MARGIN}', $this->getMarginPercentage(), $string);
        $string = str_replace('{ASPPUISCUSTOM}', $addedASPPUClass, $string);
        $string = str_replace('{ASPPUTITLE}', $asppuTitleEntry, $string);
        $string = str_replace('{ROLLUP}', $rollup, $string);
        $string = str_replace('{CSPPU}', number_format($this->getCalculatedSellPricePerUnit(), 5), $string);
        $string = str_replace('{COSTTITLE}', $costTitleEntry, $string);
        $string = str_replace('{PRICETITLE}', $priceTitleEntry, $string);
        $string = str_replace('{COSTISCUSTOM}', $addedCostClass, $string);
        $string = str_replace('{ASPISCUSTOM}', $addedPriceClass, $string);
        $string = str_replace('{ASPPU}', number_format($this->getActualSellPricePerUnit(), 3), $string);
        $string = str_replace('{ASP}', $this->getFormattedActualSellPriceTotal(), $string);
        $string = str_replace('{GMP}', $this->getActualGrossMarginPercentage(), $string);
        
        return $string;
        
    }
    
    
    const CUSTOM_COST_TITLE = "Cost calculated from translator hourly minimum.";
    
    const CUSTOM_PRICE_TITLE = "Sell price calculated from language minimum.";
    
    const BLOCK_PRICE_TITLE = "Sell price calculated by standard LLS Pricing.";
    
    const CUSTOM_ASPPU_TITLE = "Sell price per unit calculated from from client pricing table.";
    
    const BLOCK_ASPPU_TITLE = "Sell price per unit calculated by standard LLS Pricing.";
    
    const CUSTOM_ASPPUOVERRIDE_TITLE = "Sell price per unit client pricing has been overridden by user.";
    
    const TITLE_ELEMENT = "title=\"{TITLE}\"";
    
    const NOT_SHOW_ROLLUP = "&nbsp;";
    
    const UNITS_EDITABLE = "<input type=\"text\" class=\"units editable decimalOnly\" value=\"{UNITS}\" size=\"6\" />";
    
    const SHOW_ROLLUP = "<input type=\"checkbox\" class=\"{CATEGORY}ChildRolled task_rollup{GROUPNAME}\" {ROLLUP_CHECKED} />";
    
    const SET_CHECKED = "checked=\"checked\"";
    
    const ASPPU_IS_CUSTOM = "class=\"customindicator\"";
    
    const ASPPU_IS_OVERRIDDEN_HIGHER = "class=\"userOverrideHigher\"";
    
    const ASPPU_IS_OVERRIDDEN_LOWER = "class=\"userOverrideLower\"";
    
    const COST_IS_CUSTOM = "class=\"costMinimum\"";
    
    const PRICE_IS_CUSTOM = "class=\"priceMinimum\"";
    
    const PRICE_IS_LLS = "class=\"priceLLS\"";
    
    const READONLY = "readonly=\"readonly\" tabindex=\"-1\"";
    
    const EDITABLE = "editable";
            
    const LINEITEM_SPEC = 
        "<tr id=\"{ID}\">{TABLEDATA}</tr>";
    
    const TABLE_DATA_SPEC =
        "<td align=\"right\">{ROLLUP}</td><td>{DISPLAYNAME}</td><td class=\"unitcount\" >{UNITCOUNT}</td><td>{UNITTYPE}</td><td><input type=\"text\" size=\"6\" value=\"{RATE}\" class=\"{RATEEDITABLE} rate decimalOnly\" {RATEREADONLY} /></td><td {COSTISCUSTOM} align=\"right\" ><input type=\"text\" size=\"6\" value=\"{COST}\" {COSTTITLE} class=\"cost\" readonly=\"readonly\" tabindex=\"-1\" /></td><td><input type=\"text\" class=\"editable margin wholeNumberOnly\" value=\"{MARGIN}\" size=\"6\" /></td><td><input type=\"text\" class=\"csppu\" value=\"{CSPPU}\" size=\"6\" readonly=\"readonly\" tabindex=\"-1\" /></td><td {ASPPUISCUSTOM} align=\"center\"><input type=\"text\" size=\"6\" class=\"editable asppu decimalOnly\" {ASPPUTITLE} value=\"{ASPPU}\" /></td><td {ASPISCUSTOM} align=\"right\"><input type=\"text\" size=\"6\" class=\"asp\" {PRICETITLE} value=\"{ASP}\" readonly=\"readonly\" tabindex=\"-1\" /></td><td class=\"gmp\" align=\"right\">{GMP}</td>";
    
 
//    const LINEITEM_SPEC = 
//        "                <!-- begin line item spec -->
//                <tr>          
//                    <td align=\"right\">
//                       <input type=\"checkbox\" name=\"print-{ID}\" id=\"print-{ID}\" checked=\"checked\" />
//                    </td>             
//                    <td>{DISPLAYNAME}</td>
//                    <td>{UNITCOUNT}</td>
//                    <td>{UNITTYPE}</td>
//                    <td name=\"rate\" id=\"{ID}\">{RATE}</td>
//                    <td align=\"right\" id=\"{ID}\">{COST}</td>
//                    <td>
//                       <input type=\"text\" class=\"editable\" value=\"{MARGIN}\" size=\"6\" id=\"markup-{ID}\" />
//                    </td>
//                    <td id=\"cspp-new-1061705\">{CSPPU}</td>
//                    <td style=\"background-color:#FFCC00; color:white\">
//                       <input type=\"text\" size=\"6\" class=\"editable\" name=\"aspp-{ID}\" id=\"aspp-{ID}\" value=\"{ASPPU}\" />
//                    </td>
//                    <td id=\"asp-new-1061705\" align=\"right\">{ASP}</td>
//                    <td id=\"gm-new-1061705\" align=\"right\">{GMP}</td>
//                </tr>
//                <!-- end line item spec -->\n";
//    
//     const LINEITEM_SPEC_ALWAYS_ROLLUP = 
//        "                <!-- begin line item spec -->
//                <tr>          
//                    <td align=\"right\">
//                       &nbsp;
//                    </td>             
//                    <td>{DISPLAYNAME}</td>
//                    <td>{UNITCOUNT}</td>
//                    <td>{UNITTYPE}</td>
//                    <td name=\"rate\" id=\"{ID}\">{RATE}</td>
//                    <td align=\"right\" id=\"{ID}\">{COST}</td>
//                    <td>
//                       <input type=\"text\" class=\"editable\" value=\"{MARGIN}\" size=\"6\" id=\"markup-{ID}\" />
//                    </td>
//                    <td id=\"cspp-new-1061705\">{CSPPU}</td>
//                    <td style=\"background-color:#FFCC00; color:white\">
//                       <input type=\"text\" size=\"6\" class=\"editable\" name=\"aspp-{ID}\" id=\"aspp-{ID}\" value=\"{ASPPU}\" />
//                    </td>
//                    <td id=\"asp-new-1061705\" align=\"right\">{ASP}</td>
//                    <td id=\"gm-new-1061705\" align=\"right\">{GMP}</td>
//                </tr>
//                <!-- end line item spec -->\n";
}    


