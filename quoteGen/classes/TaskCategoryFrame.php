<?php

require_once (__DIR__ . '/../interfaces/IHtmlRenderable.php');
require_once (__DIR__ . '/../interfaces/IIdSupport.php');
include_once (__DIR__ . '/QuoteToolUtils.php');
/**
 * Description of TaskCategoryFrame
 *
 * @author Axian Developer
 */
abstract class TaskCategoryFrame implements IHtmlRenderable, IIdSupport{
    protected $name;
    protected $id;
    
    public function __construct($name, $id) {
        $this->name = $name;
        $this->id = $id;
    }

    public function renderHtml($parentId) {
        echo str_replace("{ID}", $this->id, TaskCategoryFrame::DIV_IDENTIFIER);
        echo $this->buildHeaderOutput();
        $this->outputChildrenTasks($this->id);
        echo $this->buildTotalSection();
        echo $this->closeFrame();
    }

    abstract function buildHeaderOutput();
    abstract function buildTotalSection();
    abstract function closeFrame();
    abstract function outputChildrenTasks($frameId);
    abstract function getChildrenTasksHtml($frameId);
    abstract function totalRowToHtml();
    
    
    public function setId($id) {
        $this->id = $id;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getHtml($parentId) {
        $string = "";
        $string .= $this->buildHeaderOutput();
        $string .= $this->getChildrenTasksHtml($this->id);
        $string .= $this->buildTotalSection();
        $string .= $this->closeFrame();
        
        return $string;
    }
    
    const DIV_IDENTIFIER = "<div id=\"{ID}\">";
    const END_DIV = "</div>";
    
    const TABLE_CLOSE = 
     "</table></div></fieldset></div>";
    
    const TABLE_HEAD = 
    "<fieldset><legend><a href=\"#\" onclick=\"return toggleQuoteItemID('languageDiv-{TARGLANG}', 'toggleImg-{TARGLANG}')\" ><img id=\"toggleImg-{TARGLANG}\" src=\"../images/minus.png\" alt=\"minus\" border=\"0\" /></a>{TARGLANG}</legend><table border=1 bgcolor=\"#FFFFFF\" width=\"100%\"><tr><th>Printable</th><th>Name</th><th colspan=\"2\"># of Units</th><th>Rate/Unit</th><th>Cost</th><th>% Margin</th><th>Calculated<br/>Sell Price<br/>Per Unit</th><th>Actual<br>Sell Price<br/>Per Unit</th><th>Actual<br/>Sell Price</th><th>Actual<br/>GM%</th></tr>";
    
}
