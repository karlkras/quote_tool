<?php

require_once (__DIR__ . '/../interfaces/IHtmlRenderable.php');
require_once (__DIR__ . '/LinguistQuoteLineItem.php');
require_once (__DIR__ . '/TaskCategoryFrame.php');
include_once (__DIR__ . '/QuoteToolUtils.php');
require_once (__DIR__ . '/../interfaces/IPMInfoSupport.php');
require_once (__DIR__ . '/ProjectManager.php');

/**
 * Description of TaskCategoryFrame
 *
 * @author Axian Developer
 */
class LinguistTaskCategoryFrame extends TaskCategoryFrame implements IPMInfoSupport{
    protected $catObj;
    protected $wordCount = 0;
    protected $projectManager;
    protected $languageMinimum = -1;
    protected $totalMinimumApplied = false;
    protected $minimumRushFeeTotal = -1;
    
    const LANGUAGE_MIN_LOOKUP_KEY = "Minimum_lang";
    
    /*
     * Categories need to be rendered in the following order:
     * 1) Linguistic
     * 2) Formatting
     * 3) Engineering
     * 4) Quality Assurance
     * 5) Project Management
     */
    const CATEGORY_NAMES = ["Linguistic", "Formatting", "Engineering", "Quality Assurance", "Project Management"];
    
    public function __construct($targetLang, $categoriedTasks, $wordCount, ProjectManager $projMan, $minimumRushFeeTotal = -1, $minimumTotal = -1) {
        parent::__construct($targetLang, QuoteToolUtils::makeLanguageId($targetLang));
        $this->catObj = $categoriedTasks;
        $this->wordCount = $wordCount;
        $this->projectManager = $projMan;
        if($minimumRushFeeTotal > -1) {
            $this->minimumRushFeeTotal = $minimumRushFeeTotal;
        } else {
            $this->getLanguagageRushMinimum();
        }
        if($minimumTotal > -1) {
            $this->languageMinimum = $minimumTotal;
        } else {
            $this->getLanguageMinimum();
        }
    }
    
    protected function getLanguageMinimum() {
        $this->getValueFromDB('rate', $this->languageMinimum);
    }
    
    protected function getLanguagageRushMinimum() {
        $this->getValueFromDB('rush_rate', $this->minimumRushFeeTotal);
    }
    
    private function getValueFromDB($key, &$value) {
        $lookupArray = self::getDBLookupArray($this);
        if(!empty($lookupArray)) {
            $value = QuoteToolUtils::getCustomRatePricingItem($lookupArray, $key, 
                    $this->projectManager->getPricingTable(), $this->projectManager->getDBConnection());
        }
    }
    
    private static function getDBLookupArray($thisInstance) {
        $retArr = array();
        if(!is_null($thisInstance->projectManager->getPricingTable())){
            $langSpecific = self::LANGUAGE_MIN_LOOKUP_KEY . "=" . str_replace(" ", "_", $thisInstance->projectManager->getTaskCatalog()->getSourceLang()) . "=" . str_replace(" ", "_", $thisInstance->getName());
            $default = self::LANGUAGE_MIN_LOOKUP_KEY;
            array_push($retArr, $langSpecific);
            array_push($retArr, $default);
        }
        return $retArr;
    }
    
    public function getMinimumRushFreeTotal() {
        return $this->minimumRushFeeTotal;
    }
    
    public function insertProjectManagerCategory(QuoteItemCategory $projManCat) {
        $this->catObj[$projManCat->getName()]=$projManCat;
    }
    
    public function getCategories(){
        return $this->catObj;
    }
    
    public function getLinguistCosts() {
        $total = 0.0;
        foreach($this->catObj as $category) {
            $total += $category->getItemsBaseCostTotal();
        }
        return $total;
    }
    
    public function isTotalMinimumApplied() {
        return $this->totalMinimumApplied;
    }
    
    public function closeFrame() {
        echo TaskCategoryFrame::TABLE_CLOSE;
    }
    
    public function getMinimumRushFeeTotal() {
        return $this->minimumRushFeeTotal;
    }
    
    public function getLinguistSellPriceTotal($doMinimum = true) {
        $total = 0.0;
        foreach($this->catObj as $category) {
            $total += $category->getItemsActualSellPriceTotal();
        }
        $total += $this->projectManager->getDistroPMSellTotal($this->getId());
        if($doMinimum && $this->languageMinimum != -1){
            if($this->languageMinimum < $total){
                $this->totalMinimumApplied = false;
                return $total;
            } else {
                $this->totalMinimumApplied = true;
                return $this->languageMinimum;
            }
        } else {
            return $total;
        }
    }
    
    private function getLinguistSellPriceTotalBase() {
        $total = 0.0;
        foreach($this->catObj as $category) {
            $total += $category->getItemsActualSellPriceTotal();
        }
        return $total;
    }
    
    public function getLinguistSellPriceTotalWithRushFee() {
        $total = 0.0;
        foreach($this->catObj as $category) {
            $total += $category->getItemsActualSellPriceTotalWithRushFee();
        }
        return $total;
    }   
    
    public function getLinguistRushFeeTotal() {
        $rushFee = 0.0;
        foreach($this->catObj as $category) {
            $rushFee += $category->getItemsRushFees();
        }
        return $rushFee;
    }
    
    private function getItemsGrossMarginPercentage($sellTotal) {
        $baseCost = $this->getLinguistCosts();
        return round((($sellTotal - $baseCost) / $sellTotal) * 100, 1);
    }
    
    public function buildHeaderOutput() {
        $output = LinguistTaskCategoryFrame::TABLE_HEAD;
        $string = str_replace("{TARGLANG_ID}", QuoteToolUtils::makeLanguageId($this->name), $output);
        $string = str_replace("{TARGLANG}", $this->projectManager->getTaskCatalog()->getSourceLang(). " to " . $this->name, $string);
        return $string;
    }    
    
    function buildTotalSection() {
        $totalData = $this->totalRowToHtml();
        $output = LinguistTaskCategoryFrame::TOTAL_SECTION;
        $string = str_replace("{TOTAL_ROW}", $totalData, $output);
        return $string;
    }
    
    public function totalRowToHtml() {
        $output = LinguistTaskCategoryFrame::TOTAL_DATA;
        
        $totalVal = $this->getLinguistSellPriceTotal();
        
        $totalTitleEntry = "";
        $addedTotalClass = "";
        if($this->totalMinimumApplied) {
            $totalTitleEntry = str_replace("{TITLE}", self::CUSTOM_TOTAL_TITLE, QuoteLineItem::TITLE_ELEMENT);
            $addedTotalClass = QuoteLineItem::PRICE_IS_CUSTOM;
        }
        
        
        $string = str_replace("{TARGLANG_ID}", QuoteToolUtils::makeLanguageId($this->name), $output);
        $string = str_replace("{COST}", QuoteToolUtils::getCurrencyFormattedValue($this->getLinguistCosts()), $string);
        $string = str_replace("{TOTAL}", QuoteToolUtils::getCurrencyFormattedValue($totalVal), $string);
        $string = str_replace("{PERWORD}",  $this->wordCount == 0 ? 0 : (round(($totalVal / $this->wordCount), 3)) , $string);
        $string = str_replace('{TOTALISCUSTOM}', $addedTotalClass, $string);
        $string = str_replace('{TOTALTITLE}', $totalTitleEntry, $string);
        $string = str_replace("{GROSSMARGIN}", $this->getItemsGrossMarginPercentage($totalVal), $string);
        return $string;
    }

    public function outputChildrenTasks($frameId) {
        //$test = $this->getChildrenTasksHtml($frameId);
        foreach(self::CATEGORY_NAMES as $catName){
            if(array_key_exists($catName, $this->catObj)) {
                $category = $this->catObj[$catName];
                $category->renderHtml($frameId);
            }
        }
    }

    public function getChildrenTasksHtml($frameId) {
        $string = "";
        foreach(self::CATEGORY_NAMES as $catName){
            if(array_key_exists($catName, $this->catObj)) {
                $category = $this->catObj[$catName];
                $string .= $category->getHtml($frameId);
            }
        }
        return $string;
    }

    public function getPMPercent() {
        return $this->projectManager->getPMPercent();
    }

    public function getPMSellTotal() {
        return $this->getLinguistSellPriceTotalBase();
    }
    
    const CUSTOM_TOTAL_TITLE = "Total price calculated from language minimum.";

    const TABLE_HEAD = 
        "<fieldset style=\"width:80\"><legend><a href=\"#\" onclick=\"return toggleQuoteItemID('languageDiv-{TARGLANG_ID}', 'toggleImg-{TARGLANG_ID}')\" ><img id=\"toggleImg-{TARGLANG_ID}\" src=\"../images/minus.png\" border=\"0\" alt=\"minus\"/></a>{TARGLANG}</legend><div id=\"languageDiv-{TARGLANG_ID}\"><table border=\"1\" bgcolor=\"#FFFFFF\" width=\"100%\"><tr><th>Printable</th><th>Name</th><th colspan=\"2\"># of Units</th><th>Rate/Unit</th><th>Cost</th><th>% Margin</th><th>Calculated<br/>Sell Price<br/>Per Unit</th><th>Actual<br/>Sell Price<br/>Per Unit</th><th>Actual<br/>Sell Price</th><th>Actual<br/>GM%</th></tr>";    
    
    const TOTAL_SECTION =
        "<tr><td colspan=\"5\" class=\"totalSpacer\">&nbsp;</td><td colspan=\"6\" class=\"totalRow\">&nbsp;</td></tr>{TOTAL_ROW}";
    
    const TOTAL_DATA = 
        "<tr id=\"totalrow-{TARGLANG_ID}\"><td colspan=\"5\" class=\"total_title\" >Totals:</td><td align=\"right\" id=\"cost-{TARGLANG_ID}\">{COST}</td><td align=\"center\" colspan=\"3\" class=\"instruction\">Sell Price Per Word <input type=\"text\" size=\"6\" id=\"perword-{TARGLANG_ID}\" value=\"{PERWORD}\" readonly=\"readonly\" tabindex=\"-1\" /><td {TOTALISCUSTOM} align=\"right\"><input type=\"text\" size=\"6\" id=\"asp-{TARGLANG_ID}\" class=\"asp\" {TOTALTITLE} value=\"{TOTAL}\" readonly=\"readonly\" tabindex=\"-1\"/></td><td align=\"right\" id=\"gm-{TARGLANG_ID}\">{GROSSMARGIN}</td></tr>";
}
