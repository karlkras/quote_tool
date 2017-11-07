<?php
namespace llts\fauxWorkfront;


include_once (__DIR__ . '/../../quotegen/classes/QuoteToolUtils.php');

/**
 * Description of baseFauxObject
 *
 * @author Axian Developer
 */
abstract class baseFauxObject {
    public $id = 0; // integer
    public $name = ""; // string
    
    protected function __construct($name = "") {
        $this->id = \QuoteToolUtils::getFauxWFId(5);
        $this->name = $name;
    }
    
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }
}
