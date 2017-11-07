<?php

namespace llts\fauxWorkfront;

include_once (__DIR__ . '/user.php');
include_once (__DIR__ . '/company.php');

/**
 * Description of project
 *
 * @author Axian Developer
 */
class project extends baseFauxObject {

    public $company; // company
    public $contact; // user
    public $sponsor; // user
    public $portfolio; // portfolio
    public $type = 'Localization'; // string
    public $pricingScheme; // string
    public $budget = 0; // double
    public $fileInventory; // string
    public $fileCount = 0; // double
    public $pageCount = 0; // double
    public $wordCount = 0; // double

    public function __construct($projName) {
        parent::__construct();
        $this->setName($this->getId() . " " . $projName);
    }
    
    public function getCompany() {
        return $this->company;
    }

    public function getContact() {
        return $this->contact;
    }

    public function getSponsor() {
        return $this->sponsor;
    }

    public function getPortfolio() {
        return $this->portfolio;
    }

    public function getType() {
        return $this->type;
    }

    public function getPricingScheme() {
        return $this->pricingScheme;
    }

    public function getBudget() {
        return $this->budget;
    }

    public function getFileInventory() {
        return $this->fileInventory;
    }

    public function getFileCount() {
        return $this->fileCount;
    }

    public function getPageCount() {
        return $this->pageCount;
    }

    public function getWordCount() {
        return $this->wordCount;
    }

    public function setCompany($company) {
        $this->company = $company;
        $this->pricingScheme = $company->getDocTransPricingScheme();
    }

    public function setContact($contact) {
        $this->contact = $contact;
    }

    public function setSponsor($sponsor) {
        $this->sponsor = $sponsor;
    }

    public function setPortfolio($portfolio) {
        $this->portfolio = $portfolio;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function setPricingScheme($pricingScheme) {
        $this->pricingScheme = $pricingScheme;
    }

    public function setBudget($budget) {
        $this->budget = $budget;
    }

    public function setFileInventory($fileInventory) {
        $this->fileInventory = $fileInventory;
    }

    public function setFileCount($fileCount) {
        $this->fileCount = $fileCount;
    }

    public function setPageCount($pageCount) {
        $this->pageCount = $pageCount;
    }

    public function setWordCount($wordCount) {
        $this->wordCount = $wordCount;
    }
}
