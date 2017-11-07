<?php
namespace llts\fauxWorkfront;

include_once (__DIR__ . '/../../definitions.php');

include_once (__DIR__ . '/../../quotegen/classes/QuoteToolUtils.php');

include_once (__DIR__ . '/baseFauxObject.php');

/**
 * Description of company
 *
 * @author Axian Developer
 */
class company extends baseFauxObject {
    
    public $docTransPricingScheme = ""; // string Client-Specific Pricing, LLS Pricing, Healthcare List Pricing, Margin Pricing
    public $paymentTerms = 30; // int  default to 30.
    public $commissionType = ""; // string empty.
    public $addPMSurchargeforDocTrans = true; // boolean default true
    public $usLinguistsRequired = false; // boolean default false
    public $passTradosLeveraging = true; // boolean default true.
    public $checkKnowledgeMgt = true; // boolean default false.
    public $clientID = 0; // int 5 digit number representing client.
    public $userDataID = ""; // string default empty.
    public $greatPlainsID = ""; // string default empty.
    public $prospect = ""; // string default empty.
    public $llsClientID = ""; // string default empty.
    public $billingInstructions = ""; // string default empty.
    public $billingEmail = ""; // string default empty.
    public $legalEntity = "LLS"; // string default 'LLS'
    public $abbreviation = ""; // string default empty.
    public $guid = ""; // string 
    
    public function __construct($name) {
        parent::__construct($name);
    }
    
    public function getDocTransPricingScheme() {
        return $this->docTransPricingScheme;
    }

    public function getPaymentTerms() {
        return $this->paymentTerms;
    }

    public function getCommissionType() {
        return $this->commissionType;
    }

    public function getAddPMSurchargeforDocTrans() {
        return $this->addPMSurchargeforDocTrans;
    }

    public function getUsLinguistsRequired() {
        return $this->usLinguistsRequired;
    }

    public function getPassTradosLeveraging() {
        return $this->passTradosLeveraging;
    }

    public function getCheckKnowledgeMgt() {
        return $this->checkKnowledgeMgt;
    }

    public function getClientID() {
        return $this->clientID;
    }

    public function getUserDataID() {
        return $this->userDataID;
    }

    public function getGreatPlainsID() {
        return $this->greatPlainsID;
    }

    public function getProspect() {
        return $this->prospect;
    }

    public function getLlsClientID() {
        return $this->llsClientID;
    }

    public function getBillingInstructions() {
        return $this->billingInstructions;
    }

    public function getBillingEmail() {
        return $this->billingEmail;
    }

    public function getLegalEntity() {
        return $this->legalEntity;
    }

    public function getAbbreviation() {
        return $this->abbreviation;
    }

    public function getGuid() {
        return $this->guid;
    }

    public function setDocTransPricingScheme($docTransPricingScheme) {
        $this->docTransPricingScheme = $docTransPricingScheme;
    }

    public function setPaymentTerms($paymentTerms) {
        $this->paymentTerms = $paymentTerms;
    }

    public function setCommissionType($commissionType) {
        $this->commissionType = $commissionType;
    }

    public function setAddPMSurchargeforDocTrans($addPMSurchargeforDocTrans) {
        $this->addPMSurchargeforDocTrans = $addPMSurchargeforDocTrans;
    }

    public function setUsLinguistsRequired($usLinguistsRequired) {
        $this->usLinguistsRequired = $usLinguistsRequired;
    }

    public function setPassTradosLeveraging($passTradosLeveraging) {
        $this->passTradosLeveraging = $passTradosLeveraging;
    }

    public function setCheckKnowledgeMgt($checkKnowledgeMgt) {
        $this->checkKnowledgeMgt = $checkKnowledgeMgt;
    }

    public function setClientID($clientID) {
        $this->clientID = $clientID;
    }

    public function setUserDataID($userDataID) {
        $this->userDataID = $userDataID;
    }

    public function setGreatPlainsID($greatPlainsID) {
        $this->greatPlainsID = $greatPlainsID;
    }

    public function setProspect($prospect) {
        $this->prospect = $prospect;
    }

    public function setLlsClientID($llsClientID) {
        $this->llsClientID = $llsClientID;
    }

    public function setBillingInstructions($billingInstructions) {
        $this->billingInstructions = $billingInstructions;
    }

    public function setBillingEmail($billingEmail) {
        $this->billingEmail = $billingEmail;
    }

    public function setLegalEntity($legalEntity) {
        $this->legalEntity = $legalEntity;
    }

    public function setAbbreviation($abbreviation) {
        $this->abbreviation = $abbreviation;
    }

    public function setGuid($guid) {
        $this->guid = $guid;
    }
}

