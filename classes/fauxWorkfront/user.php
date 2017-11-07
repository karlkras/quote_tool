<?php

namespace llts\fauxWorkfront;

include_once (__DIR__ . '/baseFauxObject.php');

/**
 * Description of contact
 *
 * @author Axian Developer
 */
class user  extends baseFauxObject{
    public $firstName = ""; // string
    public $lastName = ""; // string
    public $address = ""; // string
    public $address2 = ""; // string
    public $city = ""; // string
    public $country = ""; // string
    public $state = ""; // string
    public $postalCode = ""; // string
    public $email = ""; // string
    public $scheduleID = ""; // string
    public $email2 = ""; // string
    public $emailSecondaryAddresses = ""; // string
    public $phone = ""; // string
    public $userName = ""; // string
    public $title = ""; // string
    public $roles = ""; // string
    public $company; // company
    public $timeZone = ""; // string
    public $llsClientID = ""; // string
    public $billingInstructions = ""; // string
    public $billingEmail = ""; // string
    public $os = ""; // string
    public $linguistSource = ""; // string
    public $sourceLanguage = ""; // string
    public $targetLanguage = ""; // string
    public $translatorSince = ""; // string
    public $altPhone1 = ""; // string
    public $altPhone2 = ""; // string
    public $fax = ""; // string
    public $pager = ""; // string
    public $taxID = ""; // string
    public $certificationDetails = ""; // string
    public $adminNotes = ""; // string
    public $pmComments = ""; // string
    public $agreements = ""; // string
    public $memberships = ""; // string
    public $certifications = ""; // string
    public $specialties = ""; // string
    public $tools = ""; // string
    public $qualified = ""; // string
    public $copyedit = 0.0; // float
    public $hourly = 0.0; // float
    public $min = 0.0; // float
    public $trReps = 0.0; // float
    public $trFuzzy = 0.0; // float
    public $trNew = 0.0; // float
    public $trceReps = 0.0; // float
    public $trceFuzzy = 0.0; // float
    public $trceNew = 0.0; // float
    public $mktReps = 0.0; // float
    public $mktFuzzy = 0.0; // float
    public $mktNew = 0.0; // float
    public $medicalReps = 0.0; // float
    public $medicalFuzzy = 0.0; // float
    public $medicalNew = 0.0; // float
    public $techReps = 0.0; // float
    public $techFuzzy = 0.0; // float
    public $techNew = 0.0; // float
    public $uiReps = 0.0; // float
    public $uiFuzzy = 0.0; // float
    public $uiNew = 0.0; // float
    public $helpReps = 0.0; // float
    public $helpFuzzy = 0.0; // float
    public $helpNew = 0.0; // float
    public $rushReps = 0.0; // float
    public $rushFuzzy = 0.0; // float
    public $rushNew = 0.0; // float
    public $lingoNetUser = false; // boolean
    public $legalEntity = ""; // string
    
    
    public function __construct($name) {
        parent::__construct($name);
    }
    
    public function getFirstName() {
        return $this->firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function getAddress() {
        return $this->address;
    }

    public function getAddress2() {
        return $this->address2;
    }

    public function getCity() {
        return $this->city;
    }

    public function getCountry() {
        return $this->country;
    }

    public function getState() {
        return $this->state;
    }

    public function getPostalCode() {
        return $this->postalCode;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getScheduleID() {
        return $this->scheduleID;
    }

    public function getEmail2() {
        return $this->email2;
    }

    public function getEmailSecondaryAddresses() {
        return $this->emailSecondaryAddresses;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function getUserName() {
        return $this->userName;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getRoles() {
        return $this->roles;
    }

    public function getCompany() {
        return $this->company;
    }

    public function getTimeZone() {
        return $this->timeZone;
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

    public function getOs() {
        return $this->os;
    }

    public function getLinguistSource() {
        return $this->linguistSource;
    }

    public function getSourceLanguage() {
        return $this->sourceLanguage;
    }

    public function getTargetLanguage() {
        return $this->targetLanguage;
    }

    public function getTranslatorSince() {
        return $this->translatorSince;
    }

    public function getAltPhone1() {
        return $this->altPhone1;
    }

    public function getAltPhone2() {
        return $this->altPhone2;
    }

    public function getFax() {
        return $this->fax;
    }

    public function getPager() {
        return $this->pager;
    }

    public function getTaxID() {
        return $this->taxID;
    }

    public function getCertificationDetails() {
        return $this->certificationDetails;
    }

    public function getAdminNotes() {
        return $this->adminNotes;
    }

    public function getPmComments() {
        return $this->pmComments;
    }

    public function getAgreements() {
        return $this->agreements;
    }

    public function getMemberships() {
        return $this->memberships;
    }

    public function getCertifications() {
        return $this->certifications;
    }

    public function getSpecialties() {
        return $this->specialties;
    }

    public function getTools() {
        return $this->tools;
    }

    public function getQualified() {
        return $this->qualified;
    }

    public function getCopyedit() {
        return $this->copyedit;
    }

    public function getHourly() {
        return $this->hourly;
    }

    public function getMin() {
        return $this->min;
    }

    public function getTrReps() {
        return $this->trReps;
    }

    public function getTrFuzzy() {
        return $this->trFuzzy;
    }

    public function getTrNew() {
        return $this->trNew;
    }

    public function getTrceReps() {
        return $this->trceReps;
    }

    public function getTrceFuzzy() {
        return $this->trceFuzzy;
    }

    public function getTrceNew() {
        return $this->trceNew;
    }

    public function getMktReps() {
        return $this->mktReps;
    }

    public function getMktFuzzy() {
        return $this->mktFuzzy;
    }

    public function getMktNew() {
        return $this->mktNew;
    }

    public function getMedicalReps() {
        return $this->medicalReps;
    }

    public function getMedicalFuzzy() {
        return $this->medicalFuzzy;
    }

    public function getMedicalNew() {
        return $this->medicalNew;
    }

    public function getTechReps() {
        return $this->techReps;
    }

    public function getTechFuzzy() {
        return $this->techFuzzy;
    }

    public function getTechNew() {
        return $this->techNew;
    }

    public function getUiReps() {
        return $this->uiReps;
    }

    public function getUiFuzzy() {
        return $this->uiFuzzy;
    }

    public function getUiNew() {
        return $this->uiNew;
    }

    public function getHelpReps() {
        return $this->helpReps;
    }

    public function getHelpFuzzy() {
        return $this->helpFuzzy;
    }

    public function getHelpNew() {
        return $this->helpNew;
    }

    public function getRushReps() {
        return $this->rushReps;
    }

    public function getRushFuzzy() {
        return $this->rushFuzzy;
    }

    public function getRushNew() {
        return $this->rushNew;
    }

    public function getLingoNetUser() {
        return $this->lingoNetUser;
    }

    public function getLegalEntity() {
        return $this->legalEntity;
    }

    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function setAddress2($address2) {
        $this->address2 = $address2;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    public function setCountry($country) {
        $this->country = $country;
    }

    public function setState($state) {
        $this->state = $state;
    }

    public function setPostalCode($postalCode) {
        $this->postalCode = $postalCode;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setScheduleID($scheduleID) {
        $this->scheduleID = $scheduleID;
    }

    public function setEmail2($email2) {
        $this->email2 = $email2;
    }

    public function setEmailSecondaryAddresses($emailSecondaryAddresses) {
        $this->emailSecondaryAddresses = $emailSecondaryAddresses;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
    }

    public function setUserName($userName) {
        $this->userName = $userName;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setRoles($roles) {
        $this->roles = $roles;
    }

    public function setCompany($company) {
        $this->company = $company;
    }

    public function setTimeZone($timeZone) {
        $this->timeZone = $timeZone;
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

    public function setOs($os) {
        $this->os = $os;
    }

    public function setLinguistSource($linguistSource) {
        $this->linguistSource = $linguistSource;
    }

    public function setSourceLanguage($sourceLanguage) {
        $this->sourceLanguage = $sourceLanguage;
    }

    public function setTargetLanguage($targetLanguage) {
        $this->targetLanguage = $targetLanguage;
    }

    public function setTranslatorSince($translatorSince) {
        $this->translatorSince = $translatorSince;
    }

    public function setAltPhone1($altPhone1) {
        $this->altPhone1 = $altPhone1;
    }

    public function setAltPhone2($altPhone2) {
        $this->altPhone2 = $altPhone2;
    }

    public function setFax($fax) {
        $this->fax = $fax;
    }

    public function setPager($pager) {
        $this->pager = $pager;
    }

    public function setTaxID($taxID) {
        $this->taxID = $taxID;
    }

    public function setCertificationDetails($certificationDetails) {
        $this->certificationDetails = $certificationDetails;
    }

    public function setAdminNotes($adminNotes) {
        $this->adminNotes = $adminNotes;
    }

    public function setPmComments($pmComments) {
        $this->pmComments = $pmComments;
    }

    public function setAgreements($agreements) {
        $this->agreements = $agreements;
    }

    public function setMemberships($memberships) {
        $this->memberships = $memberships;
    }

    public function setCertifications($certifications) {
        $this->certifications = $certifications;
    }

    public function setSpecialties($specialties) {
        $this->specialties = $specialties;
    }

    public function setTools($tools) {
        $this->tools = $tools;
    }

    public function setQualified($qualified) {
        $this->qualified = $qualified;
    }

    public function setCopyedit($copyedit) {
        $this->copyedit = $copyedit;
    }

    public function setHourly($hourly) {
        $this->hourly = $hourly;
    }

    public function setMin($min) {
        $this->min = $min;
    }

    public function setTrReps($trReps) {
        $this->trReps = $trReps;
    }

    public function setTrFuzzy($trFuzzy) {
        $this->trFuzzy = $trFuzzy;
    }

    public function setTrNew($trNew) {
        $this->trNew = $trNew;
    }

    public function setTrceReps($trceReps) {
        $this->trceReps = $trceReps;
    }

    public function setTrceFuzzy($trceFuzzy) {
        $this->trceFuzzy = $trceFuzzy;
    }

    public function setTrceNew($trceNew) {
        $this->trceNew = $trceNew;
    }

    public function setMktReps($mktReps) {
        $this->mktReps = $mktReps;
    }

    public function setMktFuzzy($mktFuzzy) {
        $this->mktFuzzy = $mktFuzzy;
    }

    public function setMktNew($mktNew) {
        $this->mktNew = $mktNew;
    }

    public function setMedicalReps($medicalReps) {
        $this->medicalReps = $medicalReps;
    }

    public function setMedicalFuzzy($medicalFuzzy) {
        $this->medicalFuzzy = $medicalFuzzy;
    }

    public function setMedicalNew($medicalNew) {
        $this->medicalNew = $medicalNew;
    }

    public function setTechReps($techReps) {
        $this->techReps = $techReps;
    }

    public function setTechFuzzy($techFuzzy) {
        $this->techFuzzy = $techFuzzy;
    }

    public function setTechNew($techNew) {
        $this->techNew = $techNew;
    }

    public function setUiReps($uiReps) {
        $this->uiReps = $uiReps;
    }

    public function setUiFuzzy($uiFuzzy) {
        $this->uiFuzzy = $uiFuzzy;
    }

    public function setUiNew($uiNew) {
        $this->uiNew = $uiNew;
    }

    public function setHelpReps($helpReps) {
        $this->helpReps = $helpReps;
    }

    public function setHelpFuzzy($helpFuzzy) {
        $this->helpFuzzy = $helpFuzzy;
    }

    public function setHelpNew($helpNew) {
        $this->helpNew = $helpNew;
    }

    public function setRushReps($rushReps) {
        $this->rushReps = $rushReps;
    }

    public function setRushFuzzy($rushFuzzy) {
        $this->rushFuzzy = $rushFuzzy;
    }

    public function setRushNew($rushNew) {
        $this->rushNew = $rushNew;
    }

    public function setLingoNetUser($lingoNetUser) {
        $this->lingoNetUser = $lingoNetUser;
    }

    public function setLegalEntity($legalEntity) {
        $this->legalEntity = $legalEntity;
    }



}