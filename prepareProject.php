<?PHP

include_once("database.php");

include_once( __DIR__ . "/classes/fauxWorkFront/taskService.php");

use llts\fauxWorkfront\linguistTask AS fauxLinguistTask;
use llts\fauxWorkfront\taskService AS fauxTaskService;
use llts\fauxWorkfront\wordCounts AS fauxWordCount;

ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 1);



//include_once('classes/ProjectInfo.php');

session_start();
require_once('uuid.php');

//check to see if we're logged in
if (!isset($_SESSION['userID']) || !isset($_POST)) {
    header('location:login.php?location=ballpark');
    exit;
} elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID)) {
    header('location:login.php?err=6&location=ballpark');
    exit;
}

prepareProject();
header('location: ./quotegen/getProject.php');
exit;

function prepareProject() {
    $theProject = generateProject();
    $wordCounts = generateWordCounts();
    $linguistTasks = generateLinguistTasks($theProject->getId(), $wordCounts);
    $billableTasks = generateBillableTasks($theProject->getId());
    $taskService = new fauxTaskService($theProject->getId());
    $taskService->billableTasks = $billableTasks;
    $taskService->lingTasks = $linguistTasks;

    $_SESSION['projectObj'] = serialize($theProject);
    $_SESSION['taskService'] = serialize($taskService);
    $_SESSION['postSettings'] = serialize($_POST);
}

function generateWordCounts() {
    return new fauxWordCount(isset($_POST['words_type_new']) ? (int) $_POST['words_type_new'] : 0, isset($_POST['words_type_match']) ? (int) $_POST['words_type_match'] : 0, isset($_POST['words_type_fuzzy']) ? (int) $_POST['words_type_fuzzy'] : 0);
}

function getLanguageEstimators() {
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

    $estimators = array();
    $sourceLang = $_POST['source_lang'];
    $estimators += [$sourceLang => array()];
    $theTargetLangs = $_POST['targ_langs'];
    $langPair = new languagePair();
    $langPair->sourceLang = $sourceLang;
    foreach ($theTargetLangs as $theLang) {
        $langPair->targetLang = $theLang;
        $lingStandardRatesArg = new getLinguistStandardRates();
        $lingStandardRatesArg->langPairs = $langPair;
        $langEstimator = $workFrontAPI->getLinguistStandardRates($lingStandardRatesArg)->return;
        if (!is_null($langEstimator)) {
            $estimators[$sourceLang] +=[$theLang => $langEstimator[0]];
        } else {
            customError("Language estimator for " . $sourceLang . " to " . $theLang . " not found.<br>Contact management to add this linguist to the system.");
            return;
        }
    }
    $workFrontAPI = null;
    return $estimators;
}

function generateProject() {
    $testProj = createProject($_POST['projectName']);
    $company = createCompany($_POST['companyName'], $_POST['priceScheme'], $_POST['companyType']);
    $companyContact = createContact($_POST['clientFirstName'], $_POST['clientLastName'], $company->getName());
    $testProj->setCompany($company);
    $testProj->setSponsor(createSalesrep($_POST['sales_rep']));
    $testProj->setContact($companyContact);

    return $testProj;
}

function generateBillableTasks($projId) {
    $billableTasks = array();
    $langCount = count($_POST['targ_langs']);
    if (isset($_POST['tmWorkHours'])) {
        $tmHours = (float) $_POST['tmWorkHours'] * $langCount;
        if ($tmHours > 0) {
            $task = createBillableTask("TM Work 1", $projId, $tmHours);
            array_push($billableTasks, $task);
        }
    }
    if (isset($_POST['uiEngineeringHours'])) {
        $uiHours = (float) $_POST['uiEngineeringHours'] * $langCount;
        if ($uiHours > 0) {
            $task = createBillableTask("UI Engineering", $projId, $uiHours);
            array_push($billableTasks, $task);
        }
    }

    if (isset($_POST['numpages'])) {
        if ($_POST['numpages'] > 0) {
            $task = createBillableTask("QA 1", $projId, 0);
            array_push($billableTasks, $task);
            $task = createBillableTask("QA Coordination", $projId, 0);
            array_push($billableTasks, $task);
        }
    }

    if (isset($_POST['formattingHours'])) {
        $formattingHours = (float) $_POST['formattingHours'] * $langCount;
        if ($formattingHours > 0) {
            $task = createBillableTask("Format 1", $projId, $formattingHours);
            array_push($billableTasks, $task);
        }
    }

    if (isset($_POST['pmPercent'])) {
        $pmPercent = (float) $_POST['pmPercent'];
        if ($pmPercent > 0) {
            $task = createBillableTask("Project Management", $projId, 0);
            array_push($billableTasks, $task);
        }
    }

    if (isset($_POST['additionalTasks'])) {
        foreach ($_POST['additionalTasks'] as $item) {
            switch ($item) {
                case "Miscellaneous":
                    $task = createBillableTask("Miscellaneous", $projId, 1 * $langCount);
                    array_push($billableTasks, $task);
                    break;
                case "Synching":
                    $task = createBillableTask("Synching", $projId, 1 * $langCount);
                    array_push($billableTasks, $task);
                    break;
            }
        }
    }
    return $billableTasks;
}

function generateLinguistTasks($projId, $wordCounts) {
    $lingTasks = array();
    $estimators = getLanguageEstimators();
    if (!empty($estimators)) {
        foreach ($estimators as $sourceLang => $items) {
            foreach ($items as $targeName => $estimator) {
                array_push($lingTasks, createLinguistTask($sourceLang, $targeName, "TR+CE", $projId, $wordCounts, $estimator, fauxLinguistTask::TRCE_TASK_TYPE));

                if ($_POST['proofReading'] != "none") {
                    if (isset($_POST['proofreadingHours'])) {
                        $hours = (float) $_POST['proofreadingHours'];
                        if ($hours > 0) {
                            array_push($lingTasks, createLinguistTask($sourceLang, $targeName, "PR", $projId, $wordCounts, $estimator, fauxLinguistTask::HOURLY_TASK_TYPE, $hours));
                        }
                    }
                }
                if (isset($_POST['additionalTasks'])) {
                    $adds = createAdditionalLinguistTasks($_POST['additionalTasks'], $sourceLang, $targeName, $projId, $wordCounts, $estimator);
                    if (!empty($adds)) {
                        foreach ($adds as $entry) {
                            array_push($lingTasks, $entry);
                        }
                    }
                }
            }
        }
    }
    // now sort them...    
    return sortLinguistTasks($lingTasks);
}

function sortLinguistTasks($lingTasks) {
    $hourlyTasks = array();
    $wordTasks = array();
    $catNames = fauxLinguistTask::CATEGORY_NAMES;
    foreach ($lingTasks as $task) {
        if ($task->getCategoryName() == $catNames[fauxLinguistTask::TRCE_TASK_TYPE]) {
            array_push($wordTasks, $task);
        } else {
            array_push($hourlyTasks, $task);
        }
    }
    foreach ($hourlyTasks as $hourTask) {
        array_push($wordTasks, $hourTask);
    }
    return $wordTasks;
}

function createLinguistTask($src, $trg, $name, $projId, $wordCounts, $estimator, $taskType, $workRequired = null) {
    $aTask = new fauxLinguistTask(generateName($src, $trg, $name), $name, $projId, $src, $trg, $wordCounts, $estimator, $taskType);
    if (!is_null($workRequired)) {
        $aTask->getLtask()->workRequired = $workRequired;
    }
    return $aTask;
}

function generateName($src, $trg, $name) {
    return $src . " to " . $trg . " " . $name;
}

function customError($message = null) {
    $_POST = array();
    echo "<html><br>\n";
    echo "<body><br>\n";
    echo "<form action='estimate_1.php' >\n";
    echo "<h2>There was a problem while generating the project for the ballparker</h2>\n";
    if (!is_null($message)) {
        echo "<strong>Overview:</strong> " . $message . "<br><br>\n";
        error_log($message);
    }
    echo "<strong>Detail:</strong> Exiting process<br><br><br>\n";
    echo "<input type='submit'><br>\n";
    echo "</form><br>\n";
    echo "</body><br>\n";
    echo "</html>\n";
    die;
}

function testEstimators() {
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
    $theTargetLangs = getTargetLanguages();
    $sourceLang = 'English (US)';
    $estimators = array();
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
    return $estimators;
}
