<?PHP
ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 1);

session_start();
require_once('../uuid.php');

//check to see if we're logged in
if (!isset($_SESSION['userID'])) {
    die('You are not properly logged in and are unable to use this application');
} elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID)) {
    die('Fatal Error! Application authentication is not correct');
}

if (isset($_SESSION['postSettings'])) {
    $_POST = unserialize($_SESSION['postSettings']);
}

if (!isset($_POST['submit']) || (($_POST['submit'] != "Proceed") && ($_POST['submit'] != "Continue >"))) {
    header('location: index.php?error=1');
    exit;
}

require_once(__DIR__ . "/classes/ProjectManager.php");

include_once('classes/projectData.php');
require_once(__DIR__ . '/classes/PricingMySql.php');
require_once(__DIR__ . '/../function.fatal_handler.php');

getProjectAssets();
$projectObj = unserialize($_SESSION['projectObj']);

$_SESSION['proofReading'] = $_POST['proofReading'];

$callingPage = "";
if (isset($_POST['callingPage'])) {
    $callingPage = $_POST['callingPage'];
    $_SESSION['callingPage'] = $callingPage;
}

if (isset($_POST['us-linguists'])) {
    if ($_POST['us-linguists'] == 'attask') {
        $usBaseLinguists = $projectObj->company->usLinguistsRequired;
    } else {
        $usBaseLinguists = ($_POST['us-linguists'] == 'yes') ? true : false;
    }

    $_SESSION['useUSLinguists'] = $usBaseLinguists;
}

//get the project data from the post data
$projectData = new projectData();

if (isset($_POST['estDeliveryDate'])) {
    $projectData->set_reqDevDate($_POST['estDeliveryDate']);
} else {
    $projectData->set_reqDevDate("");
}

$projectData->set_rushFee(isset($_POST['rushFees']) ? (float) ($_POST['rushFees']) : 0);

$customRushApply = false;
if($projectData->get_rushFee() > 0) {
    $customRushApply = $projectObj->company->docTransPricingScheme == "Client-Specific Pricing";
}


$projectData->set_discountValue($_POST['discountAmount']);
$projectData->set_discountType($_POST['discountType']);
$projectData->set_billingCycle($_POST['cycle']);
$projectData->set_billingCycleOther(isset($_POST['cycleOther']) ? $_POST['cycleOther'] : "");
$projectData->set_initialPMPercent($_POST['pmPercent']);
$projectData->set_ratesUnlocked(false);
$projectData->set_customRushApply($customRushApply);
$projectData->set_pricingScheme($_POST['priceScheme']);
$projectData->set_pricing($_POST['priceScheme'] == "Margin Pricing" || $_POST['priceScheme'] == "LLS Pricing" ? false : true);
$callingPage = "";
if (isset($_POST['callingPage'])) {
    $callingPage = $_POST['callingPage'];
}
$projectData->set_callingPage($callingPage);

if ((isset($_POST['pmMinPerLanguage'])) && ($_POST['pmMinPerLanguage'] == 'yes')) {
    $projectData->set_pmMinPerLanguage(true);
} else {
    $projectData->set_pmMinPerLanguage(false);
}

if ((isset($_POST['qaRequired'])) && ($_POST['qaRequired'] == 'yes')) {
    $projectData->set_numberOfPages($_POST['numpages']);
    $projectData->set_qa_pagesPerHour($_POST['pagesPerHour']);
} else {
    $projectData->set_numberOfPages(-1);
    $projectData->set_qa_pagesPerHour(-1);
}

$projectData->set_chargeForProofreading($_POST['proofReading'] == 'yes');

$_SESSION['projectData'] = serialize($projectData);

$_SESSION['projectManager'] = serialize(new ProjectManager());

function getProjectAssets() {
    if (!isset($_SESSION['projectObj'])) {
        require_once('../attaskconn/LingoAtTaskService.php');
        $quotableProjects = unserialize($_SESSION['quotableProjects']);
        //unset($_SESSION['quotableProjects']);
        $projectID = $_POST['project'];
        //search through the available projects to find the one selected
        $projectStub = NULL;
        foreach ($quotableProjects as $quotableProj) {
            if ($quotableProj->id == $projectID) {
                $projectStub = $quotableProj;
                break;
            }
        }
        if ($projectStub === null) {
            echo "No quotable projects found!";
            exit;
        }

        //$_SESSION['projectStub'] = serialize($projectStub);
        //now get the full project from @task
        //get the list of tasks from the project
        try {
            set_time_limit(60);
            $api = new LingoAtTaskService();
            //$api = new LingoAtTaskService();
            $g = new getProject();
            $g->projStub = $projectStub;
            $projectObj = $api->getProject($g)->return;

            $_SESSION['projectObj'] = serialize($projectObj);

            // and now get the taskservice...
            $g = new getTaskService;
            $g->projStub = $projectStub;
            $taskService = $api->getTaskService($g)->return;
            if($taskService->billableTasks == null) {
                $taskService->billableTasks = array();
            }
            if($taskService->lingTasks == null) {
                $taskService->lingTasks = array();
            }
            
            $_SESSION['taskService'] = serialize($taskService);
        } catch (exception $e) {
            echo "<h2>There was a problem with the @task service</h2>";
            echo "<strong>Overview:</strong> " . $e->getMessage() . "<br><br>";

            echo "<strong>Detail:</strong> " . $e->detail->ProcessFault->message . "<br><br><br><hr>";


            echo "Debug Data:<br><pre>";
            var_dump($e);
            echo "</pre>";

            exit;
        }
    }
}

header('location: editQuote.php');
exit;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Error!</title>
    </head>

    <body>
        <h3>You should not be here!</h3>
        <p>Something really bad happened, you should duck</p>
        <a href="index.php">Click me</a>
    </body>
</html>
