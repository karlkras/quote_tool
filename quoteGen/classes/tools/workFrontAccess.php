<?php

ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 1);

require_once(__DIR__ . "/../../../attaskconn/LingoAtTaskService.php");
require_once(__DIR__ . '/../../../quotegen/classes/PricingMySql.php');

/**
 * This utility class is for the purpose of moving workfront data into local
 * database table storage to be utilized by custom applications.
 * It's main purpose is to alleviate network overhead in using the Workfront api
 * for accessing custom project information.
 */
class WorkfrontDataCollector {

    // <editor-fold desc="Class variables">
    private $myDBConnection;
    private $wfAPI;

    // </editor-fold>
    // <editor-fold desc="Class constants">
    const INTERNAL_COST_TYPES = ["Formatting Specialist", "Document QA Specialist", "Project Manager", "Localization Engineer"];
    const DROP_INTERNALCOST_TABLE_STRING = "DROP TABLE IF EXISTS internalcosts";
    const INSERT_INTERNALCOST_HEADER_SPEC = "INSERT INTO internalcosts
    (name, costHourly)
    VALUES\n";
    const INSERT_INTERNALCOST_VALUE_SPEC = "('{name}', {costHourly}),\n";
    const CREATE_INTERNALCOST_TABLE_STRING = "CREATE TABLE internalcosts (
  name varchar(50) PRIMARY KEY,
  costHourly int(4) unsigned NOT NULL)";
    const DROP_FILEFORMATS = "DROP TABLE IF EXISTS fileFormats";
    const CREATE_FILEFORMATS = "CREATE TABLE fileFormats (
  file_type varchar(50) PRIMARY KEY,
  pages_per_hour int(6) DEFAULT '0') ";
    const FILEFORMAT_INSERT_STATEMENT = "insert into fileFormats (file_type, pages_per_hour) 
    values
    ('CorelDraw', 3),
    ('Email', 0),
    ('Excel', 0),
    ('Flare', 0),
    ('Flash', 0),
    ('Framemaker', 4),
    ('HTML', 0),
    ('Illustrator', 2),
    ('InDesign', 4),
    ('Other', 0),
    ('PDF (convert to Indesign)', 4),
    ('PDF (convert to Word)', 6),
    ('PageMaker', 4),
    ('PhotoShop', 2),
    ('PowerPoint', 10),
    ('Publisher', 4),
    ('Quark XPress', 4),
    ('RoboHelp', 0),
    ('TXT', 0),
    ('Translation Memory', 0),
    ('UI Resource Files', 0),
    ('Word', 6),
    ('XML', 0)";
    
    const DROP_SOURCELANGUAGE_TABLE_STRING = "DROP TABLE IF EXISTS sourcelanguages";
    const INSERT_SOURCELANGUAGE_HEADER_SPEC = "INSERT INTO sourcelanguages
    (langName)
    VALUES\n";
    const INSERT_LANGUAGE_VALUE_SPEC = "('{langName}'),\n";
    const CREATE_SOURCELANGUAGE_TABLE_STRING = "CREATE TABLE sourcelanguages (
  langName varchar(50) PRIMARY KEY)";
    const DROP_TARGETLANGUAGE_TABLE_STRING = "DROP TABLE IF EXISTS targetlanguages";
    const INSERT_TARGETLANGUAGE_HEADER_SPEC = "INSERT INTO targetlanguages
    (langName)
    VALUES\n";
    const CREATE_TARGETLANGUAGE_TABLE_STRING = "CREATE TABLE targetlanguages (
  langName varchar(50) PRIMARY KEY)";
    const DROP_SALESREP_TABLE_STRING = "DROP TABLE IF EXISTS salesreps";
    const INSERT_SALESREP_HEADER_SPEC = "INSERT INTO salesreps
    (id, userName, firstName, lastName, email, phone, title)
    VALUES\n";
    const INSERT_SALESREP_VALUE_SPEC = "({id}, '{userName}', '{firstName}', '{lastName}', '{email}', '{phone}', '{title}'),\n";
    const CREATE_SALESREP_TABLE_STRING = "CREATE TABLE salesreps (
  id int(11) unsigned PRIMARY KEY,
  userName varchar(80) UNIQUE KEY,
  firstName varchar(80),
  lastName varchar(80),
  email varchar(100),
  phone varchar(50),
  title varchar(100))";
    const DROP_COMPANY_TABLE_STRING = "DROP TABLE IF EXISTS workfrontcompanies";
    const INSERT_COMPANY_HEADER_SPEC = "INSERT INTO workfrontcompanies
    (id, docTransPricingScheme,paymentTerms,commissionType,addPMSurchargeforDocTrans,usLinguistsRequired,passTradosLeveraging,checkKnowledgeMgt,clientID,userDataID,greatPlainsID,prospect,llsClientID,billingInstructions,billingEmail,legalEntity,abbreviation,guid, name,dtBucket)
    VALUES\n";
    const INSERT_COMPANY_VALUE_SPEC = "({id}, '{docTransPricingScheme}',{paymentTerms},'{commissionType}',{addPMSurchargeforDocTrans},{usLinguistsRequired},{passTradosLeveraging},{checkKnowledgeMgt},{clientID},'{userDataID}','{greatPlainsID}', '{prospect}','{llsClientID}','{billingInstructions}','{billingEmail}','{legalEntity}', '{abbreviation}','{guid}', '{name}','{dtBucket}'),\n";
    const ALTER_COMPANY_TABLE = "ALTER TABLE `workfrontcompanies` CHANGE `name` `name` VARCHAR( 100 )
CHARACTER SET latin1 COLLATE latin1_bin NULL DEFAULT NULL";
    const CREATE_COMPANY_TABLE_STRING = "CREATE TABLE workfrontcompanies (
  id int(11) unsigned PRIMARY KEY,
  docTransPricingScheme varchar(50) DEFAULT '',
  paymentTerms smallint(6) DEFAULT '0',
  commissionType varchar(50) DEFAULT '',
  addPMSurchargeforDocTrans bit(1) DEFAULT b'1',
  usLinguistsRequired bit(1) DEFAULT b'0',
  passTradosLeveraging bit(1) DEFAULT b'1',
  checkKnowledgeMgt bit(1) DEFAULT b'0',
  clientID int(11) unsigned DEFAULT '0',
  userDataID varchar(50) DEFAULT '',
  greatPlainsID varchar(50) DEFAULT '',
  prospect varchar(50) DEFAULT '',
  llsClientID varchar(50) DEFAULT '',
  billingInstructions varchar(256) DEFAULT '',
  billingEmail varchar(50) NOT NULL,
  legalEntity varchar(50) DEFAULT '',
  abbreviation varchar(10) DEFAULT '',
  guid varchar(50) UNIQUE KEY,
  name varchar(100) UNIQUE KEY,
  dtBucket varchar(100) DEFAULT '')";

    // </editor-fold>

    public function __construct() {
        try {
            set_time_limit(60);
            $this->wfAPI = new LingoAtTaskService();
        } catch (exception $e) {
            echo "There was a problem with the @task service\n";
            echo "Overview:" . $e->getMessage() . "\n\n";

            echo "Detail: " . $e->detail->ProcessFault->message . "\n\n";
            echo "Debug Data:\n";
            var_dump($e);
            echo "\n";

            exit;
        }
    }

    public function getCompanies() {
        $this->myDBConnection = new PricingMySql();
        echo "Calling workfront system for current company listings... \n";
        $clientCompanies = $this->wfAPI->getClientCompanies(new getClientCompanies())->return;
        echo "Completed workfront current company listing query.\n";

        echo "Dropping workfront company table...\n";
        $this->queryExecute($this->myDBConnection, self::DROP_COMPANY_TABLE_STRING);

        echo "Drop of workfront company table complete.\n";

        echo "Creating workfront company table...\n";
        $this->queryExecute($this->myDBConnection, self::CREATE_COMPANY_TABLE_STRING);
        echo "Creation of workfront company table complete.\n";

        echo "Altering workfront company table so name column can be case sensitive...\n";
        $this->queryExecute($this->myDBConnection, self::ALTER_COMPANY_TABLE);
        echo "Table alter complete.\n";


        echo "Beginning insert company data process...\n";
        $theSqlFile = self::INSERT_COMPANY_HEADER_SPEC;
        foreach ($clientCompanies as $companyObj) {
            $theSqlFile .= $this->replaceCompanyTokens(self::INSERT_COMPANY_VALUE_SPEC, $companyObj);
        }

        $theSqlFile = substr($theSqlFile, 0, strlen($theSqlFile) - 2);

        $this->queryExecute($this->myDBConnection, $theSqlFile);
        echo "Insert company data, and table generation complete.\n";

        $this->myDBConnection->close();
    }

    public function getSalesReps() {
        $this->myDBConnection = new PricingMySql();
        echo "Calling workfront system for current salesrep listings... \n";
        $theSalesStaff = $this->wfAPI->getLingoStaff(new getLingoStaff())->return->salesReps;
        echo "Completed workfront current salesrep listing query.\n";

        echo "Dropping workfront salesrep table if exists...\n";
        $this->queryExecute($this->myDBConnection, self::DROP_SALESREP_TABLE_STRING);

        echo "Drop of workfront salesrep table complete.\n";

        echo "Creating workfront salesrep table...\n";
        $this->queryExecute($this->myDBConnection, self::CREATE_SALESREP_TABLE_STRING);
        echo "Creation of workfront salesrep table complete.\n";

        echo "Beginning insert salesrept data process...\n";
        $theSqlFile = self::INSERT_SALESREP_HEADER_SPEC;
        foreach ($theSalesStaff as $obj) {
            $theSqlFile .= $this->replaceSalesRepTokens(self::INSERT_SALESREP_VALUE_SPEC, $obj);
        }

        $theSqlFile = substr($theSqlFile, 0, strlen($theSqlFile) - 2);

        $this->queryExecute($this->myDBConnection, $theSqlFile);
        echo "Insert salesrep data, and table generation complete.\n";

        $this->myDBConnection->close();
    }

    public function getInternalCosts() {
        $this->myDBConnection = new PricingMySql();
        echo "Calling workfront system for current internalCost listings... \n";

        $internalRates = new getInternalStandardRates();
        $costArray = array();

        foreach (self::INTERNAL_COST_TYPES as $roleName) {
            $internalRates->roleNames = $roleName;

            $ret = $this->wfAPI->getInternalStandardRates($internalRates)->return;
            $costArray += [$ret->name => $ret->costHourly];
        }
        echo "Completed workfront current internalCost listing query.\n";

        echo "Dropping workfront internalCost table if exists...\n";
        $this->queryExecute($this->myDBConnection, self::DROP_INTERNALCOST_TABLE_STRING);

        echo "Drop of workfront internalCost table complete.\n";

        echo "Creating workfront internalCost table...\n";
        $this->queryExecute($this->myDBConnection, self::CREATE_INTERNALCOST_TABLE_STRING);
        echo "Creation of workfront internalCost table complete.\n";

        echo "Beginning insert salesrept data process...\n";
        $theSqlFile = self::INSERT_INTERNALCOST_HEADER_SPEC;
        foreach ($costArray as $key => $value) {
            $theSqlFile .= $this->replaceInternalCostTokens(self::INSERT_INTERNALCOST_VALUE_SPEC, $key, $value);
        }

        $theSqlFile = substr($theSqlFile, 0, strlen($theSqlFile) - 2);

        $this->queryExecute($this->myDBConnection, $theSqlFile);
        echo "Insert salesrep data, and table generation complete.\n";

        $this->myDBConnection->close();
    }

    public function getFileFormats() {
        $this->myDBConnection = new PricingMySql();

        echo "Dropping fileformats table if exists...\n";
        $this->queryExecute($this->myDBConnection, self::DROP_FILEFORMATS);
        echo "Drop of fileformats table complete.\n";
        echo "Creating fileformats table...\n";
        $this->queryExecute($this->myDBConnection, self::CREATE_FILEFORMATS);
        echo "Creation of fileformats table complete.\n";
        echo "Beginning insert fileformats data process...\n";
        $this->queryExecute($this->myDBConnection, self::FILEFORMAT_INSERT_STATEMENT);
        echo "Insert fileformats data and table generation complete.\n";
    }

    public function getLanguages() {
        $this->myDBConnection = new PricingMySql();
        echo "Calling workfront system for current source and target languages... \n";

        $theService = $this->wfAPI->getLanguageService(new getLanguageService())->return;
        $sourceLangs = $theService->sourceLanguages;
        $targLangs = $theService->targetLanguages;

        echo "Completed workfront current source and target languages query.\n";

        echo "Dropping workfront language tables if exists...\n";
        $this->queryExecute($this->myDBConnection, self::DROP_SOURCELANGUAGE_TABLE_STRING);
        $this->queryExecute($this->myDBConnection, self::DROP_TARGETLANGUAGE_TABLE_STRING);

        echo "Drop of workfront language tables complete.\n";

        echo "Creating workfront internalCost table...\n";
        $this->queryExecute($this->myDBConnection, self::CREATE_SOURCELANGUAGE_TABLE_STRING);
        $this->queryExecute($this->myDBConnection, self::CREATE_TARGETLANGUAGE_TABLE_STRING);
        echo "Creation of workfront internalCost table complete.\n";

        echo "Beginning insert language data process...\n";

        // first the source languages...
        $theSqlFile = self::INSERT_SOURCELANGUAGE_HEADER_SPEC;
        foreach ($sourceLangs as $theLang) {
            if ($theLang != "Multi-Language") {
                $theSqlFile .= $this->replaceLanguageTokens(self::INSERT_LANGUAGE_VALUE_SPEC, $theLang);
            }
        }
        $theSqlFile = substr($theSqlFile, 0, strlen($theSqlFile) - 2);

        $this->queryExecute($this->myDBConnection, $theSqlFile);
        echo "Insert sourcelanguage data, and table generation complete.\n";

        // now the target languages...
        $theSqlFile = self::INSERT_TARGETLANGUAGE_HEADER_SPEC;
        foreach ($targLangs as $theLang) {
            $theSqlFile .= $this->replaceLanguageTokens(self::INSERT_LANGUAGE_VALUE_SPEC, $theLang);
        }
        $theSqlFile = substr($theSqlFile, 0, strlen($theSqlFile) - 2);

        $this->queryExecute($this->myDBConnection, $theSqlFile);
        echo "Insert targetlanguages data, and table generation complete.\n";
    }

    static function queryExecute(PricingMySql $conn, $query) {
        if ($conn->query($query) === TRUE) {
            
        } else {
            echo "error execution " . $query . " " . $conn->error;
            exit();
        }
    }

    private function convertBool($bool) {
        return $bool === true ? 1 : 0;
    }

    private function replaceCompanyTokens($spec, $obj) {
        $retString = $spec;

        $retString = str_replace("{id}", $obj->id, $retString);
        $retString = str_replace("{docTransPricingScheme}", $obj->docTransPricingScheme, $retString);
        $retString = str_replace("{paymentTerms}", $obj->paymentTerms, $retString);
        $retString = str_replace("{commissionType}", $obj->commissionType, $retString);
        $retString = str_replace("{addPMSurchargeforDocTrans}", $this->convertBool($obj->addPMSurchargeforDocTrans), $retString);
        $retString = str_replace("{usLinguistsRequired}", $this->convertBool($obj->usLinguistsRequired), $retString);
        $retString = str_replace("{passTradosLeveraging}", $this->convertBool($obj->passTradosLeveraging), $retString);
        $retString = str_replace("{checkKnowledgeMgt}", $this->convertBool($obj->checkKnowledgeMgt), $retString);
        $retString = str_replace("{clientID}", $obj->clientID, $retString);
        $retString = str_replace("{userDataID}", $obj->userDataID, $retString);
        $retString = str_replace("{greatPlainsID}", $obj->greatPlainsID, $retString);
        $retString = str_replace("{prospect}", $obj->prospect, $retString);
        $retString = str_replace("{llsClientID}", $obj->llsClientID, $retString);
        $retString = str_replace("{billingInstructions}", str_replace('#', 'no.', addslashes($obj->billingInstructions)), $retString);
        $retString = str_replace("{billingEmail}", $obj->billingEmail, $retString);
        $retString = str_replace("{legalEntity}", $obj->legalEntity, $retString);
        $retString = str_replace("{abbreviation}", $obj->abbreviation, $retString);
        $retString = str_replace("{guid}", $obj->guid, $retString);
        $retString = str_replace("{name}", str_replace('#', 'no.', addslashes($obj->name)), $retString);
        $retString = str_replace("{dtBucket}", $obj->dtBucket, $retString);

        return $retString;
    }

    private function replaceSalesRepTokens($spec, $obj) {
        $retString = $spec;

        $retString = str_replace("{id}", $obj->id, $retString);
        $retString = str_replace("{userName}", $obj->userName, $retString);


        $retString = str_replace("{firstName}", $obj->firstName, $retString);
        $retString = str_replace("{lastName}", $obj->lastName, $retString);
        $retString = str_replace("{email}", $obj->email, $retString);
        $retString = str_replace("{phone}", $obj->phone, $retString);
        $retString = str_replace("{title}", $obj->title, $retString);



        return $retString;
    }

    private function replaceInternalCostTokens($spec, $name, $value) {
        $retString = $spec;

        $retString = str_replace("{name}", addslashes($name), $retString);
        $retString = str_replace("{costHourly}", $value, $retString);

        return $retString;
    }

    private function replaceLanguageTokens($spec, $lang) {
        $retString = $spec;
        $retString = str_replace("{langName}", addslashes($lang), $retString);
        return $retString;
    }

}

$doIt = new WorkfrontDataCollector();
$doIt->getFileFormats();
$doIt->getCompanies();
$doIt->getSalesReps();
$doIt->getInternalCosts();
$doIt->getLanguages();

