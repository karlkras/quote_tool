<?php

require_once (__DIR__ . "/../enums/WorkUnitType.php");

class WorkUnit {

    private $unitType;
    private $unitCount;
    private $baseRatePerUnit;
    private $customRatePerUnit = -1;

    public function __construct(WorkUnitType $unitType, $unitCount) {
        $this->unitType = $unitType;
        $this->unitCount = $unitCount;
    }

    public function getUnitType() {
        return $this->unitType->getName();
    }
    
    public function isCustomUnitRate() {
        return $this->customRatePerUnit > -1;
    }

    public function getUnitCount() {
        return $this->unitCount;
    }

    public function getBaseRatePerUnit() {
        return $this->baseRatePerUnit;
    }

    public function setBaseRatePerUnit($rate) {
        if (FALSE === is_numeric($rate)) {
            throw new InvalidArgumentException('WorkUnit setBaseRatePerUnit expected Argument 1 to be a long');
        }
        $this->baseRatePerUnit = $rate;
    }
    
    public function getCustomRatePerUnit() {
        return $this->customRatePerUnit;
    }

    public function setCustomRatePerUnit($rate) {
        if (FALSE === is_numeric($rate)) {
            throw new InvalidArgumentException('WorkUnit setCustomRatePerUnit expected Argument 1 to be a long');
        }
        $this->customRatePerUnit = $rate;
    }
    
    public function getActualRatePerUnit() {
        if (is_null($this->customRatePerUnit)|| ($this->customRatePerUnit == -1)){
            return $this->baseRatePerUnit;
        }else{
            return $this->customRatePerUnit;
        }
        //return (is_null($this->customRatePerUnit)||($this->customRatePerUnit == -1)) ? $this->baseRatePerUnit :  $this->customRatePerUnit;
    }

    public function setUnitCount($count) {
        if (FALSE === is_numeric($count)) {
            throw new InvalidArgumentException('WorkUnit setUnitCount expected Argument 1 to be an Integer');
        }
        $this->unitCount = $count;
    }

    public function setUnitType(WorkUnitType $type) {
        $this->unitType = $type;
    }
}

