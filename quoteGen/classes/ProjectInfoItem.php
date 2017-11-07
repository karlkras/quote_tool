<?php


/**
 * Description of ProjectInfoItem
 *
 * @author Axian Developer
 */
class ProjectInfoItem {
    protected $name;
    protected $reference;
    protected $value;
    
    public function __construct($name, $reference, $value) {
        $this->name = $name;
        $this->reference = $reference;
        $this->value = $value;
    }
    
    public function getName() {
        return $this->name;
    }

    public function getReference() {
        return $this->reference;
    }

    public function getValue() {
        return $this->value;
    }    
}
