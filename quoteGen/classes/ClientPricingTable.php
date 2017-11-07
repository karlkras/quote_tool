<?php

require_once (__DIR__ . '/../interfaces/IClientPricingSupport.php');
require_once (__DIR__ . '/PricingMySql.php');
/**
 * Since not all client pricing table schemas support all rate fields,
 * this utility class will report on what support it does or does not
 * have.
 *
 * @author Karl Krasnowsky
 */
class ClientPricingTable implements IClientPricingSupport{
    private $rateSupport = false;
    private $rushRateSupport = false;
    private $dataTableName = "";
    private $mySqlConn = null;
    
    
    public function __construct($dataTableName, PricingMySql $dbConn) {
        $this->dataTableName = $dataTableName;
        $this->mySqlConn = $dbConn;
        $this->initSupport();
    }   

    public function supportsRate() {
        return $this->rateSupport;
    }

    public function supportsRushRate() {
        return $this->rushRateSupport;
    }
    
    private function initSupport() {
        if(!is_null($this->mySqlConn) && !empty($this->dataTableName)){
            $query = sprintf(self::COLUMNQUERY,$this->dataTableName);
            if($result = $this->mySqlConn->query(sprintf(self::COLUMNQUERY,$this->dataTableName))){
                $finfo = $result->fetch_fields();
                $this->rateSupport = $this->isColumnInTable($finfo, self::RATE_COLUMN);
                $this->rushRateSupport = $this->isColumnInTable($finfo, self::RUSH_RATE_COLUMN);
                
                $result->free();
            }
        }
    }
    
    private function isColumnInTable($tableColumns, $name) {
        foreach($tableColumns as $column){
            if($column->name === $name) {
                return true;
            }
        }
        return false;
    }
}

