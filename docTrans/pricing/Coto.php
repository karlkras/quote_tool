<?PHP

include_once('words.php');


function Coto_Pricing(&$taskService, $expedited, $pmSurcharge, &$sellRates)
{
	$totalPrice = 0;
	$totalCost = 0;
	$rateRush = 1;

	$useUSLinguists = $_SESSION['useUSLinguists'];

	//get DTP pricing from the database
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	if($expedited === 'custom25' || $expedited === 'custom50') {
		$query = "SELECT rush_rate FROM client_coto_pricing WHERE task_name='Formatting_(DTP)'";
		$result = $myDBConn->query($query);
		$res1 =$result->fetch_assoc();
		if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
			$query = "SELECT rate FROM client_coto_pricing WHERE task_name='Formatting_(DTP)'";
			$result = $myDBConn->query($query) or die($myDBConn->error);
			if($expedited === 'custom25') {
				$rateRush = 1.25;
			}
			else{
				$rateRush = 1.5;
			}
		}
	}
	else {
		$query = "SELECT rate FROM client_coto_pricing WHERE task_name='Formatting_(DTP)'";
		$result = $myDBConn->query($query) or die($myDBConn->error);
	}

    $result->data_seek(0);
	$res = $result->fetch_row();
	if ($res != NULL)
	{
		$dtpHourly = ($res[0]/1000)* $rateRush;
	}
	else
	{
		$dtpHourly = -1;
	}
	
	$result->free();
	
	//loop through the linguistic tasks and update the pricing
	if (count($taskService->lingTasks) < 2)
	{	
		processLingTask($taskService->lingTasks, $myDBConn, $totalPrice, $totalCost, $useUSLinguists, $dtpHourly);
		
	}
	else
	{
		foreach ($taskService->lingTasks as $lingTask)
		{
			processLingTask($lingTask, $myDBConn, $totalPrice, $totalCost, $useUSLinguists, $dtpHourly);
		}
	}
	
	$myDBConn->close();
	
	//loop through the billable tasks, excluding PM
	if (count($taskService->billableTasks) < 2)
	{
		if ($taskService->billableTasks->btask->name != 'Project Management')
		{
			processBillableTask($taskService->billableTasks, $sellRates, $totalPrice, $totalCost);
		}
		
		
	}
	else
	{
		foreach ($taskService->billableTasks as $billTask)
		{
			if ($billTask->btask->name != 'Project Management')
			{
				processBillableTask($billTask, $sellRates, $totalPrice, $totalCost);
			}
		
			
		}
	}
	
	//now that all other tasks are priced, we can do the PM fee
	$minimum = calculateMinimum($taskService);
	if ($totalPrice < $minimum)
	{
		$totalPrice = $minimum;
		$_SESSION['hitMinimum'] = true;
	}
	else
	{
		$_SESSION['hitMinimum'] = false;
	}
	
	if (($totalPrice > $minimum) && ($pmSurcharge == true))
	{
		if (count($taskService->billableTasks) < 2)
		{
			if ($taskService->billableTasks->btask->name == 'Project Management')
			{
				processPMTask($taskService->billableTasks, $sellRates, $totalPrice, $totalCost);
			}
		}
		else
		{
			foreach($taskService->billableTasks as $billTask)
			{
				if ($billTask->btask->name == 'Project Management')
				{
					processPMTask($billTask, $sellRates, $totalPrice, $totalCost);	
				}
			}
		}
	}
	
	//determine if rushfees are needed
	$wordCount = 0;
	if (count($taskService->lingTasks) < 2)
		$wordCount = $taskService->lingTasks->wordCounts->wordCount;
	else
	{
		foreach ($taskService->lingTasks as $lt)
		{
			$wordCount += $lt->wordCounts->wordCount;
		}
	}
	$rushFee = 0;
	if($expedited != 0 && $expedited != 'custom25' && $expedited != 'custom50') {
		if ($expedited === 0.25) {
			$rushFee = round($totalPrice * 0.25,2);
		}
		else {
			$rushFee = round($totalPrice*0.5,2);
		}
	

		//echo "Expedited Turnaround Surcharge: $rushFee<br>";
	}
	$taskService->rushFee = $rushFee;
	$totalPrice += $rushFee;
	return $totalPrice;
}

function processLingTask(&$theTask, $conn, $totalPrice, $totalCost, $useUSLinguists, &$dtpHourly) {
	$totalWordcount = getTotalWords($theTask);
	$rushFee = $_SESSION['rushFee'];
	$rateRush = 1;
		
	if ($totalWordcount > 0)
	{
		if (($theTask->wordCounts->newWords > 0) ||
			($theTask->wordCounts->fuzzyWords > 0) ||
			($theTask->wordCounts->matchRepsWords > 0))
		{
			$wordRates = getTradosRates($theTask, 'client_coto_pricing', $useUSLinguists);
			$buyRates = getTradosRates($theTask, NULL, $useUSLinguists);
			
			$price = $totalWordcount * $wordRates['New_Text'];
			$sellRates[$theTask->ltask->id] = $wordRates['New_Text'];
			$totalCost += $theTask->wordCounts->newWords * $buyRates['New_Text'];
			$totalCost += $theTask->wordCounts->fuzzyWords * $buyRates['Fuzzy_Text'];
			$totalCost += $theTask->wordCounts->matchRepsWords * $buyRates['Match_Text'];
			
		}
		else
		{
			if ($useUSLinguists)
			{
				$wordRate = ($theTask->wordRateDetails->US_based_new >0) ? $theTask->wordRateDetails->US_based_new : $theTask->wordRateDetails->trce_new;
			}
			else
			{
				$wordRate = $theTask->wordRateDetails->trce_new;
			}
			$costRate = $wordRate;
		
			$taskName = "Translate_+_Copyedit#New_Text#=" . str_replace(" ", "_", $theTask->sourceLang) . "=" . str_replace(" ","_", $theTask->targLang);
			if($rushFee === 'custom25' || $rushFee === 'custom50') {
				$query = "SELECT rush_rate from client_coto_pricing WHERE task_name = '" . $taskName . "'";
				$result = $conn->query($query);
				$res1 = $result->fetch_assoc();
				if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
					$query = "SELECT rate from client_coto_pricing WHERE task_name = '" . $taskName . "'";
					$result = $conn->query($query) or die($conn->error);
					if($rushFee === 'custom25') {
						$rateRush = 1.25;
					}
					else{
						$rateRush = 1.5;
					}
				}
			}
			else {
				$query = "SELECT rate from client_coto_pricing WHERE task_name = '" . $taskName . "'";
				$result = $conn->query($query) or die($conn->error);
			}

            $result->data_seek(0);
			$res =  $result->fetch_row();
			
			if ($res != NULL)
			{
				$wordRate = ($res[0]/1000)* $rateRush;
			}
			else
			{
				$wordRate /= 0.5* $rateRush;
			}
			
			$result->free();
			
			$price = $totalWordcount * $wordRate;
			$sellRates[$theTask->ltask->id] = $wordRate;
			$totalCost += $costRate * $totalWordcount;
			
		}
		
		//check for minimum
		$taskName = "Minimum#New_Text#=" . str_replace(" ", "_", $theTask->sourceLang) . "=" . str_replace(" ","_", $theTask->targLang);
		if($rushFee === 'custom25' || $rushFee === 'custom50') {
			$query = "SELECT rush_rate from client_coto_pricing WHERE task_name = '" . $taskName . "'";
			$result = $conn->query($query);
			$res1 = $result->fetch_assoc();
			if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
				$query = "SELECT rate from client_coto_pricing WHERE task_name = '" . $taskName . "'";
				$result = $conn->query($query) or die($conn->error);
				if($rushFee === 'custom25') {
					$rateRush = 1.25;
				}
				else{
					$rateRush = 1.5;
				}
			}
		}
		else {
			$query = "SELECT rate from client_coto_pricing WHERE task_name = '" . $taskName . "'";
			$result = $conn->query($query) or die($conn->error);
		}

        $result->data_seek(0);
		$res =  $result->fetch_row();
		
		if ($res != NULL)
		{
			$minimum = ($res[0]/1000)* $rateRush;
		}
		else
		{
			$minimum = 0;
		}
		
		$result->free();
		
		if ($price < $minimum)
		{
			$price = $minimum;
		}
		$theTask->ltask->price = $price;
		
		//echo $theTask->ltask->name, ": $", number_format($price,2),"<br>";
	}
	else
	{
		if ($useUSLinguists)
		{
			$hourlyRate = ($theTask->wordRateDetails->US_based_hourly > 0) ? $theTask->wordRateDetails->US_based_hourly : $theTask->wordRateDetails->hourly;
		}
		else
		{
			$hourlyRate = $theTask->wordRateDetails->hourly;
		}
		
		$sellRates[$theTask->ltask->id] = $hourlyRate / .5;
		$price = (($theTask->ltask->workRequired) * $hourlyRate) / .5;
		
		//$theTask->ltask->price = $price;
		//echo $theTask->ltask->name, ": $", number_format($price,2),"<br>";
	}
	
	//get the DTP price (if any)
	if ($useUSLinguists)
	{
		$costRate = ($theTask->wordRateDetails->US_based_hourly >0) ? $theTask->wordRateDetails->US_based_hourly : $theTask->wordRateDetails->hourly;
	}

	else
	{
		$costRate = $theTask->wordRateDetails->hourly;
	}
	
	if ($dtpHourly == -1)
	{
		$hourlyRate = $costRate / 0.5;
		$dtpPrice = ($theTask->wordCounts->formattingHours * $hourlyRate);
		$dtpData[$theTask->ltask->id]['rate'] = $hourlyRate;
	}
	else
	{
		$dtpPrice = $theTask->wordCounts->formattingHours * $dtpHourly;
		$dtpData[$theTask->ltask->id]['rate'] = $dtpHourly;
	}
	$dtpPrice = round($dtpPrice,2);

	$dtpData[$theTask->ltask->id]['price'] = $dtpPrice;
	$_SESSION['dtpData'] = serialize($dtpData);
	
	$theTask->ltask->price = $price + $dtpPrice;
	$totalPrice += $price + $dtpPrice;
	$totalCost += $costRate * $theTask->wordCounts->formattingHours;
    $_SESSION['totalCost'] = $totalCost;
	
}


function processBillableTask(&$theTask, &$sellRates, $totalPrice, $totalCost)
{
	$costRate = $theTask->hourlyRate;
	$sellRate =  $costRate / 0.5;
	$price = round($theTask->btask->workRequired * $sellRate,2);

	$sellRates[$theTask->btask->id] = $sellRate;
	
	$theTask->btask->price = $price;
	$totalPrice += $price;
	$totalCost += round($costRate * $theTask->btask->workRequired,2);
    $_SESSION['totalCost'] = $totalCost;
}

function processPMTask(&$theTask, &$sellRates, $totalPrice, $totalCost)
{
	$rateRush = 1;
	$rushFee = $_SESSION['rushFee'];
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	
	
	//get PM data from database
	if($rushFee === 'custom25' || $rushFee === 'custom50') {
		$query = "SELECT  rush_rate from client_coto_pricing WHERE task_name = 'Project_Management'";
		$result = $myDBConn->query($query);
		$res1 = $result->fetch_assoc();
		if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
			$query = "SELECT rate from client_coto_pricing WHERE task_name = 'Project_Management'";
			$result = $myDBConn->query($query) or die($myDBConn->error);
			if($rushFee === 'custom25') {
				$rateRush = 1.25;
			}
			else{
				$rateRush = 1.5;
			}
		}
	}
	else {
		$query = "SELECT rate from client_coto_pricing WHERE task_name = 'Project_Management'";
		$result = $myDBConn->query($query) or die($myDBConn->error);
	}

    $result->data_seek(0);
	$res =  $result->fetch_row();
	
	if ($res != NULL)
	{
		$price = round($totalPrice / (1-($res[0]/1000)) * ($res[0]/1000),2)* $rateRush;
	}
	else
	{
		$price = round($totalPrice / 9,2) * $rateRush;
	}
	$result->free();
	
	$theTask->btask->price = $price;
	$totalPrice += $price;
	$totalCost += round($theTask->btask->wordRequired * $theTask->hourlyRate,2);
    $_SESSION['totalCost'] = $totalCost;
	
	$myDBConn->close();
}

?>