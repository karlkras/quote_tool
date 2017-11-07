<?php

require_once (__DIR__ . "/../interfaces/IHtmlRenderable.php");
require_once (__DIR__ . "/../interfaces/IXmlPrintable.php");
require_once (__DIR__ . "/QuoteItemCategory.php");
require_once (__DIR__ . "/QuoteConstants.php");

/**
 * Description of CategoryPrinterController
 *
 * @author Axian Developer
 */
class CategoryPrinterController implements IHtmlRenderable {
    protected $printableCategories = array();
    
    
    public function renderHtml($parentId) {
        echo CategoryPrinterController::HTML_FIELDSET_HEAD;
        
        $this->outputCategoryControllers();
        
        echo CategoryPrinterController::HTML_FIELDSET_CLOSE;
        
        $this->outputPackageControllers();
        
    }
    
    public function addCategoryPrintController(QuoteItemCategory $cat) {
        $theCatName = $cat->getName();
        if(!QuoteConstants::getCategoryAlwaysRollUp($theCatName)) {
            if(!in_array($cat->getType(), $this->printableCategories)) {
                array_push($this->printableCategories, $cat->getType());
            }
        }
    }
    
    
    private function outputCategoryControllers() {
        $n = count($this->printableCategories);
        for($i = 0; $i < $n; $i++) {
            echo str_replace("{CATNAME}", array_values($this->printableCategories)[$i], CategoryPrinterController::PRINT_CONTROL_INPUT);
            if($i < ($n - 1)) {
                echo "<br/>\n";
            }
        }
    }
    
    public function getCategoryNames() {
        return array_keys($this->printableCategories);
    }

    public function getHtml($parentId) {
        // do nothing.
    }
    
    public function outputPackageControllers(){
        if ((count($this->printableCategories)==1)&&(in_array('Linguistic', $this->printableCategories))){
            return;
        } elseif (!in_array('Formatting',  $this->printableCategories)){
            return;
        }
        
        echo CategoryPrinterController::HTML_FIELDSET_PACKAGE_HEAD;
        $n = count($this->printableCategories);
        for($i = 0; $i < $n; $i++) {
            $catName = array_values($this->printableCategories)[$i];
            if ($catName === 'Engineering'){
                echo str_replace("{CATNAME}", $catName, CategoryPrinterController::PACKAGE_CONTROL_INPUT);
                echo "<br/>\n";
            }
        }
        if ($n >= 2){
            echo "<input class=\"package_efforts\" value=\"AllInternalpackage\" type=\"checkbox\"/>All Internal Efforts";
        }
        echo CategoryPrinterController::HTML_FIELDSET_CLOSE;
    }

    const HTML_FIELDSET_HEAD = 
        "<fieldset><legend><a href=\"#\" onclick=\"return toggleQuoteItemID('bundleDiv', 'toggleImg-Bundle')\"><img id=\"toggleImg-Bundle\" src=\"../images/minus.png\" border=\"0\" alt=\"minus\" /></a>Bundle Efforts In All Languages</legend><div id=\"bundleDiv\">";
    
    const HTML_FIELDSET_PACKAGE_HEAD = 
        "<fieldset><legend><a href=\"#\" onclick=\"return toggleQuoteItemID('packageDiv', 'toggleImg-Package')\"><img id=\"toggleImg-Package\" src=\"../images/minus.png\" border=\"0\" alt=\"minus\" /></a>Package Efforts with formatting</legend><div id=\"packageDiv\">";
    
    const PACKAGE_CONTROL_INPUT = "<input class=\"package_efforts\" value=\"{CATNAME}package\" type=\"checkbox\"/>{CATNAME}";
     
    const HTML_FIELDSET_CLOSE =
        "</div></fieldset>";
    
    const PRINT_CONTROL_INPUT =
        "<input class=\"bundle_efforts\" value=\"{CATNAME}rolled\" type=\"checkbox\"/>{CATNAME}";
}

//echo CategoryPrinterController::HTML_FIELDSET_HEAD;
//echo CategoryPrinterController::PRINT_CONTROL_INPUT;
//echo CategoryPrinterController::HTML_FIELDSET_CLOSE;

//echo CategoryPrinterController::HTML_FIELDSET_HEAD;
//echo CategoryPrinterController::HTML_FIELDSET_CLOSE;


