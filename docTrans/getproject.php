<?PHP

ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 1);
session_start();
require_once('../uuid.php');

//check to see if we're logged in
if (!isset($_SESSION['userID']))
{
	header('location:../login.php?location=standard');
	exit;
}
elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID))
{
	header('location:../login.php?err=6&location=standard');
	exit;
}
$_SESSION['hitMinimum'] = false;
$_SESSION['proofReading'] = $_POST['proofReading'];

require_once('../attaskconn/LingoAtTaskService.php');
include_once('../definitions.php');
include_once('pricing/lls.php');
include_once('pricing/Coto.php');
include_once('pricing/NetworkOmni.php');
include_once('pricing/clientSpecific.php');
include_once('pricing/rates.php');
include_once('pm.php');
include_once('saveXML.php');

if (!isset($_POST['submit']) || ($_POST['submit'] != "Proceed"))
{
	header('location: index.php?error=1');
	exit;
}

//Get data from the POST variable
$projectID = $_POST['project'];
$holidayCost = ($_POST['holiday'] == 'yes') ? true : false;

if($holidayCost) {
	$rushFee = 0.25;
}

switch($_POST['rushFees']) {
	case 'rf25':
		$rushFee = 0.25;
		break;
	case 'rf50':
		$rushFee = 0.50;
		break;
	case 'custom25':
		$rushFee = 'custom25';
		break;
	case 'custom50';
		$rushFee = 'custom50';
		break;
	default:
		$rushFee = 0;
}

$_SESSION['rushFee'] = $rushFee;




//get the list of quotable projects from the session
$quotableProjects = unserialize($_SESSION['quotableProjects']);



//search through the available projects to find the one selected
$projectStub = NULL;
if (count($quotableProjects) == 1)
{
	$projectStub = $quotableProjects;
}
elseif (count($quotableProjects) > 1)
{
	foreach ($quotableProjects as $qp)
	{
		if ($qp->id == $projectID)
		{
			$projectStub = $qp;
			break;
		}
	}
}
else
{
	echo "No quotable projects found!";
	exit;
}

$_SESSION['projectStub'] = serialize($projectStub);

//now get the full project from @task
//get the list of tasks from the project
try {
	set_time_limit(60);
	$api = new LingoAtTaskService();
	$g = new getProject($projectStub);
	$projectObj = $api->getProject($g)->return;
	$_SESSION['projectObj'] = serialize($projectObj);
}
catch(exception $e) {
	echo "<h2>There was a problem with the @task service</h2>";
	echo "<strong>Overview:</strong> ". $e->getMessage(). "<br><br>";
	
	echo "<strong>Detail:</strong> ". $e->detail->ProcessFault->message ."<br><br><br><hr>";

	echo "Debug Data:<br><pre>";
	var_dump($e);
	echo "</pre>";
	exit;
}

//get the list of tasks from the project
try{
	set_time_limit(60);
	$api = new LingoAtTaskService();
	$g = new getTaskService;
	$g->projStub = $projectStub;
	$taskService = $api->getTaskService($g)->return;
	$taskService->rushFee = 0;
	//this hack is no longer needed, leaving code for historic purposes
	//
	//quick hack to fix taskService->projID being zero
	//$taskService->projID = $projectStub->id;
	
	$_SESSION['taskService'] = serialize($taskService);

}
catch(exception $e)
{
	echo "<h2>There was a problem with the @task service</h2>";
	echo "<strong>Overview:</strong> ". $e->getMessage(). "<br><br>";
	
	echo "<strong>Detail:</strong> ". $e->detail->ProcessFault->message ."<br><br><br><hr>";
	
	
	echo "Debug Data:<br><pre>";
	var_dump($e);
	echo "</pre>";
	
	exit;
}

//create a table to store DTP pricing so that we can access it later
$dtpData = array();
$_SESSION['dtpData'] = serialize($dtpData);


//get the client information from the project,
//and get the pricing scheme that client uses.
$pricingScheme = $projectObj->company->docTransPricingScheme;
$pmSurcharge = $projectObj->company->addPMSurchargeforDocTrans;

if ($_POST['us-linguists'] == 'attask')
	$usBaseLinguists = $projectObj->company->usLinguistsRequired;
else
	$usBaseLinguists = ($_POST['us-linguists'] == 'yes') ? true : false;
	
if ($_POST['pass-leveraging'] == 'attask')
	$passTrados = $projectObj->company->passTradosLeveraging;
else
	$passTrados = ($_POST['pass-leveraging'] == 'yes') ? true : false;

	$_SESSION['useUSLinguists'] = $usBaseLinguists;

	$sellRates = array();
	$bundleInternal = $_SESSION['bundleInternal'];

switch ($pricingScheme)
{
	case 'LLS Pricing': 
		$totalPrice = LLS_Pricing(
			$taskService,
			$rushFee,
			$pmSurcharge,
			$sellRates,
			$projectObj
		);
		$dtpData = unserialize($_SESSION['dtpData']);
		//printTable($taskService, $dtpData, $totalPrice, NULL);
		$projectObj->budget = $_SESSION['totalCost'];
		$_SESSION['totalprice'] = $totalPrice;
		save_to_xml($taskService, $projectObj, $sellRates, false, $bundleInternal);
		exit;
		break;
	
	case 'NetworkOmni Pricing':
		$totalPrice = NetworkOmni_Pricing(
			$taskService,
			$rushFee,
			$pmSurcharge,
			$sellRates
		);
		$dtpData = unserialize($_SESSION['dtpData']);
		$projectObj->budget = $_SESSION['totalCost'];
		$_SESSION['totalprice'] = $totalPrice;
		save_to_xml($taskService, $projectObj, $sellRates, false, $bundleInternal);
		//printTable($taskService, $dtpData, $totalPrice, NULL);
		exit;
		break;
		
	case 'Coto/TI Pricing':
		$totalPrice = Coto_Pricing(
			$taskService,
			$rushFee,
			$pmSurcharge,
			$sellRates
		);
		$dtpData = unserialize($_SESSION['dtpData']);
		$projectObj->budget = $_SESSION['totalCost'];
		//printTable($taskService, $dtpData, $totalPrice, NULL);
		$_SESSION['totalprice'] = $totalPrice;
		save_to_xml($taskService, $projectObj, $sellRates, false, $bundleInternal);
		exit;
		break;
		
	case 'Margin Pricing':
		$_SESSION['passTrados'] = $passTrados;
		$_SESSION['pmSurcharge'] = $pmSurcharge;
		$_SESSION['projectObj'] = serialize($projectObj);
		header('location: margin.php');
		exit;
		break;
	
	case 'Client-Specific Pricing':

        $schemeTableName = getSchemeName($projectObj);

        if ($schemeTableName != 'none')
		{
			$totalPrice = clientSpecific(
				$taskService,
				$sellRates,
				$rushFee,
				$schemeTableName,
				$pmSurcharge,
				$passTrados
			);
		}
		else
		{
			$_SESSION['passTrados'] = $passTrados;
			$_SESSION['pmSurcharge'] = $pmSurcharge;
			$_SESSION['projectObj'] = serialize($projectObj);
			header('location: margin.php?err=pricing');
			exit;
		}
		$dtpData = unserialize($_SESSION['dtpData']);
		$projectObj->budget = $_SESSION['totalCost'];
		//printTable($taskService, $dtpData, $totalPrice, $schemeTableName, $passTrados);
		$_SESSION['totalprice'] = $totalPrice;
		save_to_xml($taskService, $projectObj, $sellRates, $passTrados, $bundleInternal);
		exit;
		break;

    case 'Healthcare List Pricing':

        $formatting_table = getSchemeName($projectObj);
        // this sets the pricing table to use the one below
        $schemeTableName = "healthcare_list_pricing";

        $totalPrice = clientSpecific(
            $taskService,
            $sellRates,
            $rushFee,
            $schemeTableName,
            $pmSurcharge,
            $passTrados
        );


        $dtpData = unserialize($_SESSION['dtpData']);
        $projectObj->budget = $_SESSION['totalCost'];
        //printTable($taskService, $dtpData, $totalPrice, $schemeTableName, $passTrados);
        $_SESSION['totalprice'] = $totalPrice;
        save_to_xml($taskService, $projectObj, $sellRates, $passTrados, $bundleInternal);
        exit;
        break;
}




//save_to_xml($taskService, $projectObj);



//put the objects in the session
/*$_SESSION['projectObj'] = serialize($projectObj);
$_SESSION['thisProject'] = serialize($thisProject);
$_SESSION['taskService'] = serialize($taskService);
*/


/*u = new updateProjectPricing();
$u->arg0 = $projectObj;
$u->arg1 = $taskService;

$api->updateProjectPricing($u);
*/


function printTable($taskService, $dtpData, $totalPrice, $clientScheme, $passTrados)
{

	echo "<table border=1>";
	echo "<tr><th>Language</th><th>Word count</th><th>Task</th><th>Price</th></tr>";
	if ($totalPrice == calculateMinimum($taskService))
	{
		if (count($taskService->lingTasks) < 2)
		{
			echo "<tr><td>",$taskService->lingTasks->sourceLang, " to ", $taskService->lingTasks->targLang, "</td>";
			echo "<td>";
			if ($taskService->lingTasks->wordCounts->wordCount > 0)
				echo $taskService->lingTasks->wordCounts->wordCount," words";
			else
				echo "&nbsp;";
			echo "</td>";
			switch($taskService->lingTasks->ltask->type)
			{
				case 'PR': $name = 'Proofreading'; break;
				case 'TR': $name = 'Translation'; break;
				case 'TR+CE': $name = 'Translation & Editing'; break;
			}
			echo "<td>",$name, "</td><td>", $totalPrice, "</td></tr>";
		}
		else
		{
			echo "<tr><td>",$taskService->lingTasks[0]->sourceLang, " to ", $taskService->lingTasks[0]->targLang, "</td>";
			echo "<td>";
			if ($taskService->lingTasks[0]->wordCounts->wordCount > 0)
				echo $taskService->lingTasks[0]->wordCounts->wordCount," words";
			else
				echo "&nbsp;";
			echo "</td>";
			switch($taskService->lingTasks[0]->ltask->type)
			{
				case 'PR': $name = 'Proofreading'; break;
				case 'TR': $name = 'Translation'; break;
				case 'TR+CE': $name = 'Translation & Editing'; break;
			}
			echo "<td>",$name, "</td><td>", $totalPrice, "</td></tr>";
		}
	}
	else
	{
		if (count($taskService->lingTasks) < 2)
		{
			$dtpPrice = 0;
			$taskPrice = $taskService->lingTasks->ltask->price;
	
			if ($taskService->lingTasks->wordCounts->formattingHours != 0)
			{
				$dtpPrice = $dtpData[$taskService->lingTasks->ltask->id];
				$taskPrice = $taskService->lingTasks->ltask->price - $dtpPrice;
			}
			
			if ($passTrados == false)
			{			
				echo "<tr><td>",$taskService->lingTasks->sourceLang, " to ", $taskService->lingTasks->targLang, "</td>";
				echo "<td>";
				if ($taskService->lingTasks->wordCounts->wordCount > 0)
					echo $taskService->lingTasks->wordCounts->wordCount," words";
				else
					echo "&nbsp;";
				echo "</td>";
				switch($taskService->lingTasks->ltask->type)
				{
					case 'PR': $name = 'Proofreading'; break;
					case 'TR': $name = 'Translation'; break;
					case 'TR+CE': $name = 'Translation & Editing'; break;
				}
				echo "<td>",$name, "</td><td>", $taskPrice, "</td></tr>";
			}
			else
			{
				$rates = getTradosRates($theTask, $clientScheme);
				echo "<tr><td>",$taskService->lingTasks->sourceLang, " to ", $taskService->lingTasks->targLang, "</td>";
				echo "<td>";
				echo $taskService->lingTasks->wordCounts->newWords," words";
				echo "</td>";
				switch($taskService->lingTasks->ltask->type)
				{
					case 'PR': $name = 'Proofreading'; break;
					case 'TR': $name = 'Translation'; break;
					case 'TR+CE': $name = 'Translation & Editing'; break;
				}
				echo "<td>",$name, " - New Text</td><td>", $rates['New_Text'], "</td></tr>";
				
				echo "<tr><td>",$taskService->lingTasks->sourceLang, " to ", $taskService->lingTasks->targLang, "</td>";
				echo "<td>";
				echo $taskService->lingTasks->wordCounts->fuzzyWords," words";
				echo "</td>";
				switch($taskService->lingTasks->ltask->type)
				{
					case 'PR': $name = 'Proofreading'; break;
					case 'TR': $name = 'Translation'; break;
					case 'TR+CE': $name = 'Translation & Editing'; break;
				}
				echo "<td>",$name, " - Fuzzy Text</td><td>", $rates['Fuzzy_Text'], "</td></tr>";
				
				echo "<tr><td>",$taskService->lingTasks->sourceLang, " to ", $taskService->lingTasks->targLang, "</td>";
				echo "<td>";
				echo $taskService->lingTasks->wordCounts->matchRepsWords," words";
				echo "</td>";
				switch($taskService->lingTasks->ltask->type)
				{
					case 'PR': $name = 'Proofreading'; break;
					case 'TR': $name = 'Translation'; break;
					case 'TR+CE': $name = 'Translation & Editing'; break;
				}
				echo "<td>",$name, " - Repetitions/100% Match</td><td>", $rates['Match_Text'], "</td></tr>";
			}
			if ($dtpPrice != 0)
			{
				echo "<tr><td>",$taskService->lingTasks->sourceLang, " to ", $taskService->lingTasks->targLang, "</td><td>";
				echo "&nbsp;</td><td>";
				echo "Formatting</td><td>";
				echo $dtpPrice,"</td></tr>";
			}
		}
		else
		{
			foreach($taskService->lingTasks as $lingTask)
			{
				$dtpPrice = 0;
				$taskPrice = $lingTask->ltask->price;
		
				if ($lingTask->wordCounts->formattingHours != 0)
				{
					$dtpPrice = $dtpData[$lingTask->ltask->id];
					$taskPrice = $lingTask->ltask->price - $dtpPrice;
				}
				
				if (($passTrados == false) || ($lingTask->ltask->type == 'PR'))
				{
					echo "<tr><td>",$lingTask->sourceLang, " to ", $lingTask->targLang, "</td>";
					echo "<td>";
					if ($lingTask->wordCounts->wordCount > 0)
						echo $lingTask->wordCounts->wordCount," words";
					else
						echo "&nbsp;";
					echo "</td>";
					switch($lingTask->ltask->type)
					{
						case 'PR': $name = 'Proofreading'; break;
						case 'TR': $name = 'Translation'; break;
						case 'TR+CE': $name = 'Translation & Editing'; break;
					}
					echo "<td>",$name, "</td><td>", $taskPrice, "</td></tr>";
				}
				else
				{
					$rates = getTradosRates($theTask, $clientScheme);
					echo "<tr><td>",$lingTask->sourceLang, " to ", $lingTask->targLang, "</td>";
					echo "<td>";
					echo $lingTask->wordCounts->newWords," words";
					echo "</td>";
					switch($lingTask->ltask->type)
					{
						case 'PR': $name = 'Proofreading'; break;
						case 'TR': $name = 'Translation'; break;
						case 'TR+CE': $name = 'Translation & Editing'; break;
					}
					echo "<td>",$name, " - New Text</td><td>", $rates['New_Text'], "</td></tr>";
					
					echo "<tr><td>",$lingTask->sourceLang, " to ", $lingTask->targLang, "</td>";
					echo "<td>";
					echo $lingTask->wordCounts->fuzzyWords," words";
					echo "</td>";
					switch($lingTask->ltask->type)
					{
						case 'PR': $name = 'Proofreading'; break;
						case 'TR': $name = 'Translation'; break;
						case 'TR+CE': $name = 'Translation & Editing'; break;
					}
					echo "<td>",$name, " - Fuzzy Text</td><td>", $rates['Fuzzy_Text'], "</td></tr>";
					
					echo "<tr><td>",$lingTask->sourceLang, " to ", $lingTask->targLang, "</td>";
					echo "<td>";
					echo $lingTask->wordCounts->matchRepsWords," words";
					echo "</td>";
					switch($lingTask->ltask->type)
					{
						case 'PR': $name = 'Proofreading'; break;
						case 'TR': $name = 'Translation'; break;
						case 'TR+CE': $name = 'Translation & Editing'; break;
					}
					echo "<td>",$name, " - Repetitions/100% Match</td><td>", $rates['Match_Text'], "</td></tr>";
				}
				if ($dtpPrice != 0)
				{
					echo "<tr><td>",$lingTask->sourceLang, " to ", $lingTask->targLang, "</td><td>";
					echo "&nbsp;</td><td>";
					echo "Formatting</td><td>";
					echo $dtpPrice,"</td></tr>";
				}
			}
		}
		if (count($taskService->billableTasks) <= 1)
		{
			echo "<tr><td>N/A</td><td>&nbsp;</td><td>",$taskService->billableTasks->btask->name,"</td><td>",$taskService->billableTasks->btask->price,"</td></tr>";
		}
		else
		{
			foreach ($taskService->billableTasks as $billTask)
			{
				echo "<tr><td>N/A</td><td>&nbsp;</td><td>",$billTask->btask->name,"</td><td>",$billTask->btask->price,"</td></tr>";
			}
		}
	}
	echo "</table>";
	if ($taskService->rushFee != 0)
		echo "Rushfee: ",$taskService->rushFee,"<br>";
	echo "Total Price: ", $totalPrice,"</br>";
}


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
