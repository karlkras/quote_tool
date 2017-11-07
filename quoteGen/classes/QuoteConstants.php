<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of QuoteContants
 *
 * @author Axian Developer
 */
class QuoteConstants {
    
    const PROJECT_DESCRIPTION = "Thank you for the opportunity to provide you with an estimate for localization services. We have estimated the scope of work based on the files provided by you as listed below, and the services requested:";

    private static $LineItemConstants = array(
        // Linguistic items...
        'tr_ce_fuzzy_text' => array('displayname' => 'TR+CE - Fuzzy Text', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Translate_+_CopyEdit#FUZZY_TEXT#', 'standardRateKey' => 'trce_fuzzy', 'description' => 'Hi match probability (> 85%)'),
        'tr_ce_new_text' => array('displayname' => 'TR+CE - New Text', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Translate_+_CopyEdit#NEW_TEXT#', 'standardRateKey' => 'trce_new', 'description' => 'Requires full translation.'),
        'tr_ce_words' => array('displayname' => 'TR+CE - Words', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Translate_+_CopyEdit#NEW_TEXT#', 'standardRateKey' => 'trce_new', 'description' => 'Requires full translation.'),
        'tr_ce_matchrep_text' => array('displayname' => 'TR+CE - 100% Matches/Repetitions', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Translate_+_CopyEdit#Match_Text#', 'standardRateKey' => 'trce_100Match', 'description' => 'No translation required.'),
        
        'tr_fuzzy_text' => array('displayname' => 'TR - Fuzzy Text', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Translate_+_CopyEdit#FUZZY_TEXT#', 'standardRateKey' => 'trce_fuzzy', 'description' => 'Hi match probability (> 85%)'),
        'tr_new_text' => array('displayname' => 'TR - New Text', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Translate_+_CopyEdit#NEW_TEXT#', 'standardRateKey' => 'trce_new', 'description' => 'Requires full translation.'),
        'tr_words' => array('displayname' => 'TR - Words', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Translate_+_CopyEdit#NEW_TEXT#', 'standardRateKey' => 'trce_new', 'description' => 'Requires full translation.'),
        'tr_matchrep_text' => array('displayname' => 'TR - 100% Matches/Repetitions', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Translate_+_CopyEdit#Match_Text#', 'standardRateKey' => 'trce_100Match', 'description' => 'No translation required.'),
 
        'OLR' => array('displayname' => 'Online Review', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Online_Review-Lng', 'standardRateKey' => 'hourly', 'description' => 'Designated task for an Online Review.'),
        'VO' => array('displayname' => 'Voice-Over Recording', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => '', 'standardRateKey' => 'hourly', 'description' => 'Designated task for an Online Review.'),
        'ICR' => array('displayname' => 'In Country Review', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'In_Country_Review-Lng', 'standardRateKey' => 'hourly', 'description' => 'A project review task performed in the target country.'),
        'PR' => array('displayname' => 'Proofreading', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Proofreading_/_Linguistic_QA', 'standardRateKey' => 'hourly', 'description' => 'A project review task performed in the target country.'),
        'TR+CE' => array('displayname' => 'Translate & Copyedit', 'minimumPricingDBReference' => 'Minimum', 'ratePricingDBReferenceName' => '', 'standardRateKey' => 'hourly', 'description' => 'A project review task performed in the target country.'),
        'TR/CE' => array('displayname' => 'Translate & Copyedit', 'minimumPricingDBReference' => 'Minimum', 'ratePricingDBReferenceName' => '', 'standardRateKey' => 'hourly', 'description' => 'A project review task performed in the target country.'),
        'TR' => array('displayname' => 'Translation', 'minimumPricingDBReference' => 'Minimum', 'ratePricingDBReferenceName' => '', 'standardRateKey' => 'hourly', 'description' => 'A project review task performed in the target country.'),
        'BT' => array('displayname' => 'Translation', 'minimumPricingDBReference' => 'Minimum', 'ratePricingDBReferenceName' => '', 'standardRateKey' => 'hourly', 'description' => 'A project review task performed in the target country.'),
        'TCP' => array('displayname' => 'Translation', 'minimumPricingDBReference' => 'Minimum', 'ratePricingDBReferenceName' => '', 'standardRateKey' => 'hourly', 'description' => 'A project review task performed in the target country.'),
        // Formatting items:
        'Formatting Specialist' => array('displayname' => 'Formatting Specialist', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Formatting_Specialist', 'standardRateKey' => 'hourly', 'description' => 'A project review task performed in the target country.'),
        'DTP Coordination' => array('displayname' => 'DTP Coordination', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'DTP_Coordination', 'standardRateKey' => 'hourly', 'description' => 'A project review task performed in the target country.'),
        'DTP EM cleanup' => array('displayname' => 'DTP EM cleanup', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'DTP_Coordination', 'standardRateKey' => 'hourly', 'description' => 'A project review task performed in the target country.'),
        'Format 1' => array('displayname' => 'Format 1', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Formatting_(DTP)', 'standardRateKey' => 'hourly', 'description' => 'A project review task performed in the target country.'),
        'Format 2' => array('displayname' => 'Format 2', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Formatting_(DTP)', 'standardRateKey' => 'hourly', 'description' => 'A project review task performed in the target country.'),
        'Format 3' => array('displayname' => 'Format 3', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Formatting_(DTP)', 'standardRateKey' => 'hourly', 'description' => 'A project review task performed in the target country.'),
        'Format 4' => array('displayname' => 'Format 4', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Formatting_(DTP)', 'standardRateKey' => 'hourly', 'description' => 'A project review task performed in the target country.'),
        'Final Format' => array('displayname' => 'Final Format', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Final_Format', 'standardRateKey' => 'hourly', 'description' => 'A project review task performed in the target country.'),
        'PDF Creation' => array('displayname' => 'PDF Creation', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'PDF_Creation', 'standardRateKey' => 'hourly', 'description' => 'A project review task performed in the target country.'),
        'Graphic Design' => array('displayname' => 'Graphic Design', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Graphic_Design', 'standardRateKey' => 'hourly', 'description' => 'Graphic design work.'),
        // Engineering items:
        'TM Work' => array('displayname' => 'Translation Memory Maintenance', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'TM_Work', 'standardRateKey' => 'hourly', 'description' => 'The process of creating a PDF document.'),
        'TM Work 1' => array('displayname' => 'Translation Memory Maintenance', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'TM_Work', 'standardRateKey' => 'hourly', 'description' => 'The process of creating a PDF document.'),
        'TM Work 2' => array('displayname' => 'Translation Memory Maintenance', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'TM_Work', 'standardRateKey' => 'hourly', 'description' => 'The process of creating a PDF document.'),
        'UI Engineering' => array('displayname' => 'UI Engineering', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'UI_Engineering', 'standardRateKey' => 'hourly', 'description' => 'The process of creating a PDF document.'),
        'File Treatments' => array('displayname' => 'File Treatments', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'File_Treatments', 'standardRateKey' => 'hourly', 'description' => 'The process of creating a PDF document.'),
        'Functional QA' => array('displayname' => 'Functional QA', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Functional_QA', 'standardRateKey' => 'hourly', 'description' => 'The process of creating a PDF document.'),
        'Graphic Editing' => array('displayname' => 'Graphic Editing', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Graphic_Editing', 'standardRateKey' => 'hourly', 'description' => 'The process of creating a PDF document.'),
        'Help Engineering' => array('displayname' => 'Help Engineering', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Help_Engineering', 'standardRateKey' => 'hourly', 'description' => 'The process of creating a PDF document.'),
        'Online Review-Lab' => array('displayname' => 'Online Review-Lab', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Online_Review-Lab', 'standardRateKey' => 'hourly', 'description' => 'The process of creating a PDF document.'),
        'Test Planning & Scripting' => array('displayname' => 'Test Planning & Scripting', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Test_Planning_&_Scripting', 'standardRateKey' => 'hourly', 'description' => 'The process of creating a PDF document.'),
        'Flash Engineering' => array('displayname' => 'Flash Engineering', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Flash_Engineering', 'standardRateKey' => 'hourly', 'description' => 'The process of creating a PDF document.'),
        'Website Engineering' => array('displayname' => 'Website_Engineering', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Website_Engineering', 'standardRateKey' => 'hourly', 'description' => 'The process of creating a PDF document.'),
        'Senior Engineering' => array('displayname' => 'Senior Engineering', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Senior_Engineering', 'standardRateKey' => 'hourly', 'description' => 'The process of creating a PDF document.'), 
        // QA Tasks:
        'Document QA Specialist' => array('displayname' => 'Document QA Specialist', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Document_QA_Specialist', 'standardRateKey' => 'hourly', 'description' => 'The process of creating a PDF document.'),
        'QA Coordination' => array('displayname' => 'QA Coordination', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'QA_Coordination', 'standardRateKey' => 'hourly', 'description' => 'The process of creating a PDF document.'),
        'QA 1' => array('displayname' => 'QA 1', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'QA_Coordination', 'standardRateKey' => 'hourly', 'description' => 'The process of creating a PDF document.'), 
        'QA 2' => array('displayname' => 'QA 2', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'QA_Coordination', 'standardRateKey' => 'hourly', 'description' => 'The process of creating a PDF document.'),
        'Quality Assurance Review' => array('displayname' => 'Quality Assurance Review', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'Quality_Assurance_Review', 'standardRateKey' => 'hourly', 'description' => 'QA Work.'),
        'QA EM Cleanup' => array('displayname' => 'QA EM Cleanup', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'QA_1', 'standardRateKey' => 'hourly', 'description' => 'The process of creating a PDF document.'),
        'QA Review of Proofreader Comments' => array('displayname' => 'QA Review of Proofreader Comments', 'minimumPricingDBReference' => '', 'ratePricingDBReferenceName' => 'QA_1', 'standardRateKey' => 'hourly', 'description' => 'The process of creating a PDF document.'),
        // Project Management
        'Project Management' => array('displayname' => 'Project Management', 'minimumPricingDBReference' => 'Minimum_pm', 'ratePricingDBReferenceName' => 'Project_Management', 'standardRateKey' => 'hourly', 'description' => 'Project Management Services.'),
        'Project Manager' => array('displayname' => 'Project Management', 'minimumPricingDBReference' => 'Minimum_pm', 'ratePricingDBReferenceName' => 'Project_Management', 'standardRateKey' => 'hourly', 'description' => 'The process of creating a PDF document.')
        
    );
    
    
    
    public static $ProjectInfoData = array (
        'Date of quote' => array('name' => 'estDate', 'functionName' => 'getDate' ),
        'Project ID' => array('name' => 'projectid', 'functionName' => 'getProjectId' ),
        'Project Name' => array('name' => 'projectName', 'functionName' => 'getProjectName' ),
        'Client' => array('name' => 'client', 'functionName' => 'getClientName' ),
        'Client contact' => array('name' => 'clientContact', 'functionName' => 'getClientContact' ),
        'Sponsor' => array('name' => 'sponsor', 'functionName' => 'getSponsor' ),
        'Requested Delivery Date' => array('name' => 'devdate', 'functionName' => 'getDeliveryDate'),
        'Rush Fee' => array('name' => 'rushfee', 'functionName' => 'getRushFee'),
        'Discount' => array('name' => 'discount', 'functionName' => 'getDiscount'),
        'Billing Terms' => array('name' => 'billingterms', 'functionName' => 'getBillingTerms'),
        'Billing Cycle' => array('name' => 'billingcycle', 'functionName' => 'getBillingCycle'),
        'Pricing applied' => array('name' => 'pricingapplied', 'functionName' => 'getPricingApplied'),
    );
       
    private static $CategoryPrintXmlSupport = array (
        'Linguistic' => true,
        'Formatting' => true,
        'Engineering' => true,
        'Quality Assurance' => true,
        'Other Services' => true,
        'Project Management' => true
    );
    
    private static $CategoryUnitsEditable = array (
        'Linguistic' => false,
        'Formatting' => false,
        'Engineering' => false,
        'Quality Assurance' => true,
        'Other Services' => false,
        'Project Management' => false
    );    
    
    private static $AlwaysRollUpCategory = array(
        'Linguistic' => false,
        'Formatting' => false,
        'Engineering' => false,
        'Quality Assurance' => true,
        'Other Services' => false,
        'Project Management' => true
    );
        
    
    public static $OutputTaskCategory = array(
        //Linguistic tasks...
        'ICR' => 'Linguistic',
        'OLR' => 'Linguistic',
        'PR' => 'Linguistic',
        'TR+CE' => 'Linguistic',
        'TR/CE' => 'Linguistic',
        'TR' => 'Linguistic',
        'TCP' => 'Linguistic',
        'BT' => 'Linguistic',
        'VO' => 'Linguistic',
        //Formatting Tasks
        'Formatting Specialist' => 'Formatting',
        //Engineering Tasks...
        'Localization Engineer' => 'Engineering', 
        //Quality Assurance tasks...
        'Document QA Specialist' => 'Quality Assurance',
        //Project Manager  - not sure why this one is acting
        'Project Manager' => 'Project Management'
    );
    
    public static function getCategoryPrintXmlSupport($catName){
        return self::$CategoryPrintXmlSupport[$catName];
    }

    public static function getLineItemConstants() {
        return self::$LineItemConstants;
    }

    public static function getDisplayName($refName) {
        if( array_key_exists($refName,self::$LineItemConstants)){
            return self::$LineItemConstants[$refName]['displayname'];
        } else {
            return $refName;
        }
    }

    public static function getDescription($refName) {
        if( array_key_exists($refName,self::$LineItemConstants)){
            return self::$LineItemConstants[$refName]['description'];
        } else {
            return "";
        }
    }

    public static function getRatePricingDBLookupRef($refName) {
        if( array_key_exists($refName,self::$LineItemConstants)){
            return self::$LineItemConstants[$refName]['ratePricingDBReferenceName'];
        } else {
            return "";
        }
    }
    
    public static function getMinimumPricingDBLookupRef($refName) {
        if( array_key_exists($refName,self::$LineItemConstants)){
            return self::$LineItemConstants[$refName]['minimumPricingDBReference'];
        } else {
            return "";
        }            
       
    }

    public static function getStandardRateKey($refName) {
        if( array_key_exists($refName,self::$LineItemConstants)){        
            return self::$LineItemConstants[$refName]['standardRateKey'];
        } else {
            return "";
        }             
    }
    
    public static function getTaskCategory($jobRole, $isOther = false) {
        try {
            if($isOther) {
                return "Other Services";
            }
            $ret =  self::$OutputTaskCategory[$jobRole];
            if(empty($ret)) {
                throw new Exception("Task category for " . $jobRole . " not found.");
            }
        } catch (Exception $e){
            throw $e;
        }
        return $ret;
    }
    
    public static function getCategoryAlwaysRollUp($catName){
        return self::$AlwaysRollUpCategory[$catName];
    }
    
    public static function getProjectInfoKeys() {
        return array_keys(self::$ProjectInfoData);
    }
    
    public static function getCategoryCanEditUnits($catName) {
        return self::$CategoryUnitsEditable[$catName];
    }
}
