<?php
require_once(__DIR__ . '/QuoteToolUtils.php');

/**
 * Description of BillableTaskData
 *
 * @author Axian Developer
 */
class BillableTaskData {
    protected $distributionStategy;
    protected $name;
    protected $id;
    protected $type;
    
    public function __construct($distributionStategy, $name, $id, $type) {
        $this->distributionStrategy = QuoteToolUtils::getDistributionEnum($distributionStategy);
        $this->name = $name;
        $this->id = $id;
        $this->type = $type;
    }
    public function getDistributionStrategy() {
        return $this->distributionStrategy;
    }

    public function getName() {
        return $this->name;
    }

    public function getId() {
        return $this->id;
    }

    public function getType() {
        return $this->type;
    }
}
