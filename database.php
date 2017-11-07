<?php

require_once (__DIR__ . '/definitions.php');

require_once (__DIR__ . '/classes/fauxWorkfront/company.php');
require_once (__DIR__ . '/classes/fauxWorkfront/user.php');
require_once (__DIR__ . '/classes/fauxWorkfront/project.php');
require_once (__DIR__ . '/classes/fauxWorkfront/billableTask.php');

use llts\fauxWorkfront\company AS fauxCompany;
use llts\fauxWorkfront\project AS fauxProject;
use llts\fauxWorkfront\user AS fauxUser;
use llts\fauxWorkfront\billableTask AS fauxBillableTask;
use llts\fauxWorkfront\linguistTask AS fauxLinguistTask;

// We will use PDO to execute database stuff. 
// This will return the connection to the database and set the parameter
// to tell PDO to raise errors when something bad happens
function getDbConnection() {
    $DBDriver = defined('DBDriver') ? DBDriver : "mysql";

    $test = $DBDriver . ":dbname=" . DBName . ";host=" . DBServerName . ";charset=utf8";

    $db = new PDO($test, UserName, Password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

// This is the 'search' function that will return all possible rows starting with the keyword sent by the user
function searchForKeyword($keyword) {
    $db = getDbConnection();
    $stmt = $db->prepare("SELECT name as name FROM `workfrontcompanies` WHERE LOWER(name) LIKE LOWER(?) ORDER BY name");

    $keyword = $keyword . '%';
    $stmt->bindParam(1, $keyword, PDO::PARAM_STR, 100);

    $isQueryOk = $stmt->execute();

    $results = array();

    if ($isQueryOk) {
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } else {
        trigger_error('Error executing statement.', E_USER_ERROR);
    }

    $db = null;

    return $results;
}

function getFormatHoursData($format) {
    $db = getDbConnection();
    $stmt = $db->prepare("SELECT pages_per_hour FROM `fileformats` where file_type=?");
    $stmt->bindParam(1, $format, PDO::PARAM_STR, 100);

    $isQueryOk = $stmt->execute();
    $retArray = array();
    if ($isQueryOk) {
        if ($stmt->rowCount() == 1) {
            $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $retArray += ['pages_per_hour' => $results[0]];
        }
    } else {
        trigger_error('Error executing statement.', E_USER_ERROR);
    }

    $db = null;

    return $retArray;
}

function getTableRow($tableName, $key, $keyValue) {
    $db = getDbConnection();
    $formatSpec = "SELECT * FROM {tableName} where {key}='{keyValue}'";
    $statement = str_replace('{tableName}', $tableName, $formatSpec);
    $statement = str_replace('{key}', $key, $statement);
    $statement = str_replace('{keyValue}', $keyValue, $statement);

    $stmt = $db->prepare($statement);

    $isQueryOk = $stmt->execute();
    $retArray = array();
    if ($isQueryOk) {
        if ($stmt->rowCount() == 1) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($results as $theArray => $data) {
                foreach ($data as $key => $theItemValue) {
                    if (ctype_digit($theItemValue) && strlen($theItemValue) == 1) {
                        $test = (int) $theItemValue;
                        if ($test < 2) {
                            $theItemValue = $test == 0 ? false : true;
                        }
                    }
                    $retArray += [$key => $theItemValue];
                }
            }
        }
    } else {
        trigger_error('Error executing statement.', E_USER_ERROR);
    }

    $db = null;

    return $retArray;
}

function getOtherTasks() {

    $db = getDbConnection();
    $stmt = $db->prepare("SELECT task_name FROM `additionaltasks` order by task_order");

    $isQueryOk = $stmt->execute();

    $results = array();

    if ($isQueryOk) {
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } else {
        trigger_error('Error executing statement.', E_USER_ERROR);
    }
    $db = null;
    return $results;
}

function getCompanyPMRate($companyTable) {
    $retAmount = 10;
    $db = getDbConnection();
    $theStatement = "SELECT rate from " . $companyTable . " where task_name='Project_Management' AND units='percent'";
    $stmt = $db->prepare($theStatement);

    $isQueryOk = $stmt->execute();

    $results = array();

    if ($isQueryOk) {
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } else {
        trigger_error('Error executing statement.', E_USER_ERROR);
    }
    $db = null;
    if (is_array($results) && count($results) == 1) {
        $retAmount = $results[0] / 1000;
    }
    return $retAmount;
}
//
//$foo = getCompanyData("Crystal Run Healthcare");
//$foo = getCompanyData("Brightcove Inc.");
//$foo = getCompanyData("Test 123");
//print_r($foo);

function getCompanyData($company) {
    $db = getDbConnection();
    $projectManagmentPercent = 10;
    $discountAmount = 0;
    $databaseTableName = "";
    $stmt = $db->prepare("SELECT docTransPricingScheme, id, paymentTerms FROM `workfrontcompanies` where name=?");
    $stmt->bindParam(1, $company, PDO::PARAM_STR, 100);

    $isQueryOk = $stmt->execute();
    $retArray = array();
    if ($isQueryOk) {
        if ($stmt->rowCount() == 1) {
            $id = "";
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($results as $theArray => $data) {
                foreach ($data as $key => $theItemValue) {
                    if ($key == "id") {
                        $id = $theItemValue;
                    }
                    $retArray += [$key => $theItemValue];
                }
            }
        }
    } else {
        trigger_error('Error executing statement.', E_USER_ERROR);
    }

    $db = null;
    if (!empty($id)) {
        $clientInfo = getClientTableData($id);
        $table_name = "";
        if (!empty($clientInfo)) {
            $discountAmount = is_null($clientInfo['discount']) ? $discountAmount : (float)$clientInfo['discount'];
            if ($retArray['docTransPricingScheme'] == 'Client-Specific Pricing') {
                $table_name = $clientInfo['table_name'];
                if (!doesClientTableExist($table_name)) {
                    $retArray['docTransPricingScheme'] = 'Margin Pricing';
                    $errMsg = "No CSP table found for company " . $company . ". Applying Margin Pricing";
                    $retArray += ['errorMsg' => $errMsg];
                } else {
                    // get the custom project management value if it's in the table...
                    $projectManagmentPercent = getCompanyPMRate($table_name);
                    $databaseTableName = $table_name;
                }
            }
        } else {
            if ($retArray['docTransPricingScheme'] == 'Client-Specific Pricing') {
                $retArray['docTransPricingScheme'] = 'Margin Pricing';
                $errMsg = "No client table entry found for company " . $company . ". Applying Margin Pricing";
                $retArray += ['errorMsg' => $errMsg];
            }
        }
        if($retArray['docTransPricingScheme'] == 'Healthcare List Pricing') {
            $databaseTableName = "healthcare_list_pricing";
        }
        $retArray += ['pricingdatabase' => $databaseTableName];
        $retArray += ['discount' => $discountAmount];
        $retArray += ['pmpercent' => $projectManagmentPercent];
    }
    return $retArray;
}

function doesClientTableExist($table_name) {

    $ret = false;
    $db = getDbConnection();
    $stmt = $db->prepare("SHOW TABLES LIKE ?");
    $stmt->bindParam(1, $table_name, PDO::PARAM_STR, 100);

    $isQueryOk = $stmt->execute();

    if ($isQueryOk) {
        if ($stmt->rowCount() == 1) {
            $ret = true;
        }
    }

    $db = null;
    return $ret;
}

function getInternalCost($taskType) {
    $db = getDbConnection();
    $stmt = $db->prepare("SELECT costHourly FROM `internalcosts` where name=?");
    $stmt->bindParam(1, $taskType, PDO::PARAM_STR, 100);

    $isQueryOk = $stmt->execute();

    if ($isQueryOk) {
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } else {
        trigger_error('Error executing statement.', E_USER_ERROR);
    }
    $db = null;
    return $results[0];
}

function getClientTableData($comanyId) {
    $db = getDbConnection();
    $stmt = $db->prepare("SELECT table_name,discount FROM `clients` where attask_id=?");
    $stmt->bindParam(1, $comanyId, PDO::PARAM_STR, 100);

    $isQueryOk = $stmt->execute();
    $retArray = array();

    if ($isQueryOk) {
        if ($stmt->rowCount() == 1) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($results as $theArray => $data) {
                $foo = $data;
                foreach ($foo as $key => $theItemValue) {
                    $retArray += [$key => $theItemValue];
                }
            }
        }
    } else {
        trigger_error('Error executing statement.', E_USER_ERROR);
    }

    $db = null;

    return $retArray;
}

function getTargetLanguages() {
    $db = getDbConnection();
    $stmt = $db->prepare("SELECT langName FROM `targetlanguages`");

    $isQueryOk = $stmt->execute();

    $results = array();

    if ($isQueryOk) {
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } else {
        trigger_error('Error executing statement.', E_USER_ERROR);
    }
    $db = null;
    return $results;
}

function getSourceLanguages() {
    $db = getDbConnection();
    $stmt = $db->prepare("SELECT langName FROM `sourcelanguages`");

    $isQueryOk = $stmt->execute();

    $results = array();

    if ($isQueryOk) {
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } else {
        trigger_error('Error executing statement.', E_USER_ERROR);
    }
    $db = null;
    return $results;
}

function getSalesReps() {
    $db = getDbConnection();
    $stmt = $db->prepare("SELECT userName FROM `salesreps`");

    $isQueryOk = $stmt->execute();

    $results = array();

    if ($isQueryOk) {
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } else {
        trigger_error('Error executing statement.', E_USER_ERROR);
    }
    $db = null;
    return $results;
}

function getFileFormats() {
    $db = getDbConnection();
    $stmt = $db->prepare("SELECT file_type FROM `fileformats`");

    $isQueryOk = $stmt->execute();

    $results = array();

    if ($isQueryOk) {
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } else {
        trigger_error('Error executing statement.', E_USER_ERROR);
    }
    $db = null;
    return $results;
}

function getPricingSchemes() {
    return ["LLS Pricing", "Healthcare List Pricing", "Margin Pricing"];
}

function createCompany($companyName, $pricingScheme, $companyType) {
    $retCompany = new fauxCompany($companyName);

    if ($companyType == "client") {
        $existingCompanyArray = getTableRow('workfrontcompanies', 'name', $companyName);

        if (!empty($existingCompanyArray)) {
            foreach ($existingCompanyArray as $key => $theValue) {
                try {
                    if (property_exists($retCompany, $key)) {
                        $value = $retCompany->$key;
                        if (is_bool($theValue)) {
                            if (is_bool($value)) {
                                $retCompany->$key = $theValue;
                            } else {
                                $realVal = $theValue ? 1 : 0;
                                $retCompany->$key = $realVal;
                            }
                        } else {
                            $retCompany->$key = $theValue;
                        }
                    }
                } catch (Exception $ex) {
                    continue;
                }
            }
        }
    }
    $retCompany->docTransPricingScheme = $pricingScheme;
    return $retCompany;
}

function createSalesrep($userName) {
    $existingUserArray = getTableRow('salesReps', 'userName', $userName);

    $salesRep = new fauxUser($existingUserArray['firstName'] . " " . $existingUserArray['lastName']);
    $salesRep->userName = $userName;
    $salesRep->id = $existingUserArray['id'];
    $salesRep->title = $existingUserArray['title'];
    $salesRep->firstName = $existingUserArray['firstName'];
    $salesRep->lastName = $existingUserArray['lastName'];
    $salesRep->email = $existingUserArray['email'];
    $salesRep->phone = $existingUserArray['phone'];

    return $salesRep;
}

function createContact($firstName, $lastName, $company) {
    $companyContact = new fauxUser($firstName . " " . $lastName);
    $companyContact->firstName = $firstName;
    $companyContact->lastName = $lastName;
    $companyContact->company = $company;

    return $companyContact;
}

function createProject($projectName) {
    return new fauxProject($projectName);
}

function createBillableTask($taskName, $projId, $workRequired) {
    $type = "";
    switch ($taskName) {
        case "Format 1" :
            $type = "Formatting Specialist";
            break;
        case "UI Engineering" :
        case "TM Work 1" :
        case "Miscellaneous" :
        case "Synching" :
            $type = "Localization Engineer";
            break;
        case "Project Management" :
            $type = "Project Manager";
            break;
        case "Quality Assurance Review" :
        case "QA 1" :
        case "QA Coordination" :
            $type = "Document QA Specialist";
            break;
    }
    if (!empty($type)) {
        $hourlyRate = (float) getInternalCost($type);
        return new fauxBillableTask($taskName, $type, $projId, $hourlyRate, $workRequired);
    }
    return null;
}

function createAdditionalLinguistTasks($additionalTaskArray, $srcLang, $targLang, $projectId, $wordCount, $estimator) {
    $linguistAdditions = array();
    foreach ($additionalTaskArray as $item) {
        $theTaskData = getTableRow("additionaltasks", "task_name", $item);
        if (!empty($theTaskData)) {
            if ($theTaskData["task_class"] === "linguistic") {
                $taskName = $theTaskData["task_type"];
                if ($theTaskData["linguist_type"] === 'Hourly Miscellaneous Task') {
                    $taskType = fauxLinguistTask::HOURLY_TASK_TYPE;
                } else {
                    $taskType = fauxLinguistTask::TRCE_TASK_TYPE;
                }
                array_push($linguistAdditions, createDBLinguistTask($srcLang, $targLang, $taskName, $projectId, $wordCount, $estimator, $taskType));
            }
        }
    }
    return $linguistAdditions;
}

function createDBLinguistTask($src, $trg, $name, $projId, $wordCounts, $estimator, $taskType) {
    $aTask = new fauxLinguistTask(generateDBTaskName($src, $trg, $name), $name, $projId, $src, $trg, $wordCounts, $estimator, $taskType);
    if (fauxLinguistTask::HOURLY_TASK_TYPE == $taskType) {
        $aTask->getLtask()->workRequired = 1;
    }
    return $aTask;
}

function generateDBTaskName($src, $trg, $name) {
    return $src . " to " . $trg . " " . $name;
}

//testEstimatorsAndLanguages();

function testEstimatorsAndLanguages() {
    $testArray = ["Afghani", "Belgian", "Cambodian", "Cantonese", "Dutch (Belgium)", "Fulani", "Ibo", "Iraqi", "Kiswahili", "Mandarin", "Nordic", "Persian", "Visayan", "Yupik"];


    $outArray = testLanguages($testArray, getSourceLanguages());

    if (!empty($outArray)) {
        echo "<p><label>Unexpected language(s) found in source languages:</label><br>";
        print_r($outArray) . "</p>";
    } else {
        echo "<p><label>Test of hidden source languages SUCCESS!</label></p>";
    }

    $theTargetLangs = getTargetLanguages();

    $outArray = testLanguages($testArray, $theTargetLangs);

    if (!empty($outArray)) {
        echo "<p><label>Unexpected language(s) found in target languages:</label><br>";
        print_r($outArray) . "</p>";
    } else {
        echo "<p><label>Test of hidden target languages SUCCESS!</label></p>";
    }

    $outArray = testEsimators($theTargetLangs);
    if (!empty($outArray)) {
        echo "<p><label>The following languages do not have an estimator for English (US)<label><br>";
        print_r($outArray) . "</p>";
    }
}

function testLanguages($testArray, $languages) {
    $outArray = array();
    if (!is_null($testArray)) {
        foreach ($languages as $theLang) {
            if (in_array($theLang, $testArray)) {
                array_push($outArray, $theLang);
            }
        }
    }
    return $outArray;
}

function testEsimators($theTargetLangs) {
    $estimators = array();
    if (!is_null($theTargetLangs)) {
        require_once(__DIR__ . '/attaskconn/LingoAtTaskService.php');
        $workFrontAPI = null;
        try {
            set_time_limit(60);
            $workFrontAPI = new LingoAtTaskService();
        } catch (exception $e) {
            echo "<h2>There was a problem with the @task service</h2>";
            echo "<strong>Overview:</strong> " . $e->getMessage() . "<br><br>";

            echo "<strong>Detail:</strong> " . $e->detail->ProcessFault->message . "<br><br><br><hr>";
            echo "Debug Data:<br><pre>";
            var_dump($e);
            echo "</pre>";
            exit;
        }

        $sourceLang = 'English (US)';

        $langPair = new languagePair();
        $langPair->sourceLang = $sourceLang;
        foreach ($theTargetLangs as $theLang) {
            $langPair->targetLang = $theLang;
            $foo = new getLinguistStandardRates();
            $foo->langPairs = $langPair;
            $langEstimator = $workFrontAPI->getLinguistStandardRates($foo)->return;
            if (is_null($langEstimator)) {
                array_push($estimators, $theLang);
            }
        }
        $workFrontAPI = null;
    }
    return $estimators;
}
