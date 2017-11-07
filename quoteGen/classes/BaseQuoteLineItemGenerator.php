<?php

require_once(__DIR__ . '/PricingMySql.php');
require_once(__DIR__ . '/../enums/QuoteLineItem.Enum.php');
require_once(__DIR__ . '/WorkUnit.php');
require_once (__DIR__ . '/QuoteToolUtils.php');
require_once (__DIR__ . '/TaskCatalogService.php');

/**
 * Description of BaseQuoteLineItemGenerator
 *
 * @author Axian Developer
 */
class BaseQuoteLineItemGenerator {
    protected $applyRushFee = false;
    protected $applyCustomRushFees = false;
    protected $defaultRushFeePercentage = 0.0;
    protected $theRawTask;
    protected $catalogService;
    protected $projectObj;
    protected $pricingScheme;
    protected $pricingDbConnection;
    protected $pricingDbTable;
    protected $rushFee;
    
    public function __construct($theTask, TaskCatalogService $catalogService, $projectObj, $pricing, PricingMySql $myDBConn) {
        
        $this->rushFee = isset($_SESSION['rushFee']) ? $_SESSION['rushFee'] : 0;
        $this->applyRushFee = $this->rushFee != 0;
                
        $this->theRawTask = $theTask;
        $this->catalogService = $catalogService;
        $this->projectObj = $projectObj;
        $this->pricing = $pricing;
        $this->pricingDbConnection = $myDBConn;
        $this->defaultRushFeePercentage = $this->rushFee;
        if($this->rushFee > 0) {
            $this->applyCustomRushFees = 
                    $this->projectObj->company->docTransPricingScheme == "Client-Specific Pricing";
        }
        $this->pricingDbTable = 
                QuoteToolUtils::getPricingSchemeDatabaseIfApplicable($this->pricing, $this->projectObj, $this->pricingDbConnection);
    }
}
