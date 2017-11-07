<?PHP
require_once("../attaskconn/LingoAtTaskService.php");
include_once("words.php");

function NetworkOmni_Pricing(&$taskService, $expedited, $pmSurcharge, &$sellRates) {
	$taskCount = 0;
	$totalPrice = 0;
	$totalCost = 0;
	$rateRush = 1;

	$useUSLinguists = $_SESSION['useUSLinguists'];

	//get the DTP pricing from the database
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	if($expedited === 'custom25' || $expedited === 'custom50') {
		$query = "SELECT rush_rate FROM client_networkomni_pricing WHERE task_name = 'Formatting_(DTP)'";
		$result = $myDBConn->query($query);
		$res1 = $result->fetch_assoc();
		if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
			$query = "SELECT rate FROM client_networkomni_pricing WHERE task_name = 'Formatting_(DTP)'";
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
		$query = "SELECT rate FROM client_networkomni_pricing WHERE task_name = 'Formatting_(DTP)'";
		$result = $myDBConn->query($query) or die($myDBConn->error);
	}

    $result->data_seek(0);
	$res =  $result->fetch_row();
	if ($res != NULL)
	{
		$dtpHourly = ($res[0]/1000)* $rateRush;
	}
	else
	{
		$dtpHourly = -1;
	}
	$result->free();
	
	//determine the language count, by looping through the linguistic tasks and count the target langs.
	//language count is used to determine PM fee.
	$langCount = 0;
	$langArray = array();
	if (count($taskService->lingTasks) < 2)
	{
		$langArray[$taskService->lingTasks->targLang] = true;
	}
	else
	{
		foreach ($taskService->lingTasks as $l)
		{
			$langArray[$l->targLang] = true;
		}
	}
	$langCount = count($langArray);
	//echo "lc: $langCount<br>";
	
	

	//loop through the linguistic tasks and update the pricing
	if (count($taskService->lingTasks) < 2) //there's only 1 linguist task, so no need for a loop
	{
		$totalWordcount = getTotalWords($taskService->lingTasks);
		if ($totalWordcount != 0)
		{
			$taskName = "Translate_+_Copyedit#New_Text#=" . str_replace(" ", "_", $taskService->lingTasks->sourceLang) . "=" . str_replace(" ","_", $taskService->lingTasks->targLang);
			if($expedited === 'custom25' || $expedited === 'custom50') {
				$query = "SELECT rush_rate from client_networkomni_pricing WHERE task_name = '" . $taskName . "'";
				$result = $myDBConn->query($query);
				$res1 = $result->fetch_assoc();
				if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
					$query = "SELECT rate from client_networkomni_pricing WHERE task_name = '" . $taskName . "'";
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
				$query = "SELECT rate from client_networkomni_pricing WHERE task_name = '" . $taskName . "'";
				$result = $myDBConn->query($query) or die($myDBConn->error);
			}

            $result->data_seek(0);
			$res =  $result->fetch_row();
			
			if ($useUSLinguists)
			{
				$tempRate = ($taskService->lingTasks->wordRateDetails->US_based_new > 0) ? $taskService->lingTasks->wordRateDetails->US_based_new : $taskService->lingTasks->wordRateDetails->trce_new;
			}
			else
			{
				$tempRate = $taskService->lingTasks->wordRateDetails->trce_new;
			}
			$costRate = $tempRate;
			
			if ($res != NULL)
			{
				$wordRate = ($res[0]/1000)* $rateRush;
			}
			else
			{
				$wordRate = round($tempRate/0.5,2);
			}
			$result->free();
			
			$price = round($totalWordcount * $wordRate,2);
			$sellRates[$taskService->lingTasks->ltask->id] = $wordRate;
			$totalCost += round($totalWordcount * $costRate,2);
			
		}
		else
		{
			if ($useUSLinguists)
			{
				$hourlyRate = ($taskService->lingTasks->wordRateDetails->US_based_hourly > 0) ? $taskService->lingTasks->wordRateDetails->US_based_hourly : $taskService->lingTasks->wordRateDetails->hourly;
			}
			else
			{
				$hourlyRate = $taskService->lingTasks->wordRateDetails->hourly;
			}
			$costRate = $hourlyRate;
			$hourlyRate = round($hourlyRate / .5,2);
			
			$price = $taskService->lingTasks->ltask->workRequired * $hourlyRate;
			$price = round($price,2);
			$sellRates[$taskService->lingTasks->ltask->id] = $hourlyRate;
			$totalCost += round($costRate * $taskService->lingTasks->ltask->workRequired,2);
			
			
		}
		
		//get the DTP price (if any)
		if($useUSLinguists)
		{
			$hourlyRate = ($taskService->lingTasks->wordRateDetails->US_based_hourly > 0) ? $taskService->lingTasks->wordRateDetails->US_based_hourly : $taskService->lingTasks->wordRateDetails->hourly;
		}
		else
		{
			$hourlyRate = $taskService->lingTasks->wordRateDetails->hourly;
		}
		$costRate = $hourlyRate;
		$hourlyRate = round($hourlyRate/.5,2);
		$totalCost += round($costRate * $taskService->lingTasks->wordCounts->formattingHours,2);
		
		if ($dtpHourly == -1)
		{
			$dtpPrice = $taskService->lingTasks->wordCounts->formattingHours * $hourlyRate;
			$dtpData[$taskService->lingTasks->ltask->id]['rate'] = $hourlyRate;
			
		}
		else
		{
			$dtpPrice = $taskService->lingTasks->wordCounts->formattingHours * $dtpHourly;
			$dtpData[$taskService->lingTasks->ltask->id]['rate'] = $dtpHourly;
		}
		$dtpPrice = round($dtpPrice,2);
		
		$dtpData[$taskService->lingTasks->ltask->id]['price'] = $dtpPrice;
		$_SESSION['dtpData'] = serialize($dtpData);
		
		$taskService->lingTasks->ltask->price = $price + $dtpPrice;
		$totalPrice += $price + $dtpPrice;
			
		
		$taskCount++;
	}
	else
	{
		foreach ($taskService->lingTasks as $lingTask)
		{
			$totalWordcount = getTotalWords($lingTask);
			if ($lingTask->ltask->type == 'TR+CE') 
			{
				if ($useUSLinguists)
				{
					$tempRate = ($lingTask->wordRateDetails->US_based_new > 0) ? $lingTask->wordRateDetails->US_based_new : $lingTask->wordRateDetails->trce_new;
				}
				else
				{
					$tempRate = $lingTask->wordRateDetails->trce_new;
				}
			
			
				$taskName = "Translate_+_Copyedit#New_Text#=" . str_replace(" ", "_", $lingTask->sourceLang) . "=" . str_replace(" ","_", $lingTask->targLang);
				if($expedited === 'custom25' || $expedited === 'custom50') {
					$query = "SELECT rush_rate from client_networkomni_pricing WHERE task_name = '" . $taskName . "'";
					$result = $myDBConn->query($query);
					$res1 = $result->fetch_assoc();
					if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
						$query = "SELECT rate from client_networkomni_pricing WHERE task_name = '" . $taskName . "'";
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
					$query = "SELECT rate from client_networkomni_pricing WHERE task_name = '" . $taskName . "'";
					$result = $myDBConn->query($query) or die($myDBConn->error);
				}

                $result->data_seek(0);
				$res =  $result->fetch_row();
				
				if ($res != NULL)
				{
					$wordRate = ($res[0]/1000)* $rateRush;
				}
				else
				{
					
					$wordRate = round($tempRate/0.5,2);
				}
				$result->free();
				$costRate = $tempRate;
				$price = round($totalWordcount * $wordRate,2);
				$sellRates[$lingTask->ltask->id] = $wordRate;
				$totalCost += round($costRate * $totalWordcount,2);
			}
			elseif ($lingTask->ltask->type == 'TR')
			{
				if ($useUSLinguists)
				{
					$tempRate = ($lingTask->wordRateDetails->UStr_based_new > 0) ? $lingTask->wordRateDetails->UStr_based_new : $lingTask->wordRateDetails->tr_new;
				}
				else
				{
					$tempRate = $lingTask->wordRateDetails->tr_new;
				}
			
			
				$taskName = "Translate#New_Text#=" . str_replace(" ", "_", $lingTask->sourceLang) . "=" . str_replace(" ","_", $lingTask->targLang);
				if($expedited === 'custom25' || $expedited === 'custom50') {
					$query = "SELECT rush_rate from client_networkomni_pricing WHERE task_name = '" . $taskName . "'";
					$result = $myDBConn->query($query);
					$res1 = $result->fetch_assoc();
					if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
						$query = "SELECT rate from client_networkomni_pricing WHERE task_name = '" . $taskName . "'";
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
					$query = "SELECT rate from client_networkomni_pricing WHERE task_name = '" . $taskName . "'";
					$result = $myDBConn->query($query) or die($myDBConn->error);
				}

                $result->data_seek(0);
				$res =  $result->fetch_row();
				
				if ($res != NULL)
				{
					$wordRate = ($res[0]/1000)* $rateRush;
				}
				else
				{
					$costRate = $tempRate;
					$wordRate = round($tempRate/0.5,2);
				}
				$result->free();
				
				$price = round($totalWordcount * $wordRate,2);
				$sellRates[$lingTask->ltask->id] = $wordRate;
				$totalCost += round($costRate * $totalWordcount,2);

			}
			else
			{
				if ($useUSLinguists)
				{
					$hourlyRate = ($lingTask->wordRateDetails->US_based_hourly > 0) ? $lingTask->wordRateDetails->US_based_hourly : $lingTask->wordRateDetails->hourly;
				}
				else
				{
				
					$hourlyRate = $lingTask->wordRateDetails->hourly;
				}
				$costRate = $hourlyRate;
				$hourlyRate = round($hourlyRate/0.5,2);
				
				$price = (($lingTask->ltask->workRequired) * $hourlyRate);
				$price = round($price,2);
				$sellRates[$lingTask->ltask->id] = $hourlyRate;
				$totalCost += round($costRate * $lingTask->ltask->workRequired,2);
				
				
			}
			
			//get the DTP price (if any)
			$dtpPrice = 0;
			if ($useUSLinguists)
			{
				$tempRate = ($lingTask->wordRateDetails->US_based_hourly > 0) ? $lingTask->wordRateDetails->US_based_hourly : $lingTask->wordRateDetails->hourly;
			}
			else
			{
				$tempRate = $lingTask->wordRateDetails->hourly;
			}
			
			if ($dtpHourly == -1)
			{
				
				$hourlyRate = round($tempRate/0.5,2);
				$dtpPrice = $lingTask->wordCounts->formattingHours * $hourlyRate;
				$dtpData[$lingTask->ltask->id]['rate'] = $hourlyRate;
			}
			else
			{
				$dtpPrice = $lingTask->wordCounts->formattingHours * $dtpHourly;
				$dtpData[$lingTask->ltask->id]['rate'] = $dtpHourly;
			}
			$dtpPrice = round($dtpPrice,2);
			
			$dtpData[$lingTask->ltask->id]['price'] = $dtpPrice;
			$_SESSION['dtpData'] = serialize($dtpData);
			
			$lingTask->ltask->price = $price + $dtpPrice;
			$totalPrice += $price + $dtpPrice;
			$totalCost += round($tempRate * $lingTask->wordCounts->formattingHours,2);
				
			
			$taskCount++;
		}
	}
	
	
	$myDBConn->close();
	
	
	//now do the billable tasks, except the PM, since we need
	//all the other tasks to be up-to-date so we can calculate it
	if ( count($taskService->billableTasks) < 2)
	{
		if ($taskService->billableTasks->btask->name != 'Project Management')
		{
			$price = ($taskService->billableTasks->btask->workRequired);
			$rate = round($taskService->billableTasks->hourlyRate / .5,2);
			$price = round($price*$rate,2);
			$taskService->billableTasks->btask->price = $price;
			$totalPrice += $price;
			$taskCount++;
			$sellRates[$taskService->billableTasks->btask->id] = $rate;
			$totalCost += round($taskService->billableTasks->btask->workRequired * $taskService->billableTasks->hourlyRate,2);
		}
	
		
	}
	else
	{
	
		//loop through the billable tasks
		foreach ($taskService->billableTasks as $billTask)
		{
			if ($billTask->btask->name != 'Project Management')
			{
				$price = ($billTask->btask->workRequired);
				$rate = round($billTask->hourlyRate/0.5,2);
				$price = round($price*$rate,2);
				$billTask->btask->price = $price;
				$totalPrice += $price;
				$taskCount++;
				$sellRates[$billTask->btask->id] = $rate;
				$totalCost += round($billTask->btask->workRequired * $billTask->hourlyRate,2);
			}
		
			
		}
	}
	
	//now we can go back through and find the PM task and update it.
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
		
	if (($_SESSION['hitMinimum'] == false) && ($pmSurcharge == true))
	{
		$price = 0;
		if ( count($taskService->billableTasks) < 2)
		{
			if ($taskService->billableTasks->btask->name == 'Project Management')
			{
				$price = round($totalPrice / 9,2);
				$totalCost += round($taskService->billableTasks->btask->workRequired * $taskService->billableTasks->hourlyRate,2);
				$taskService->billableTasks->btask->price = $price;
				
			}
		}
		else
		{
			foreach ($taskService->billableTasks as $billTask)
			{
				if ($billTask->btask->name == 'Project Management')
				{
					$price = round($totalPrice / 9,2);
					$totalCost += round($billTask->btask->workRequired * $billTask->hourlyRate,2);
					$billTask->btask->price = $price;

				}
			}
			
		}
		$totalPrice += $price;
		$taskCount++;
		
	}
	else
	{
		if ( count($taskService->billableTasks) < 2)
		{
			if ($taskService->billableTasks->btask->name == 'Project Management')
			{
				$taskService->billableTasks->btask->price = 0;
				
			}
		}
		else
		{
			foreach ($taskService->billableTasks as $billTask)
			{
				if ($billTask->btask->name == 'Project Management')
				{
					$billTask->btask->price = 0;

				}
			}
			
		}
	}
	
	//check for rush fees.
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
	if ($expedited != 0 && $expedited != 'custom25' && $expedited != 'custom50') {
		if ($expedited === 0.25) {
			$rushFee = round($totalPrice * 0.25,2);
		}
		else {
			$rushFee = round($totalPrice * 0.5,2);
		}

		//echo "Expedited Turnaround Surcharge: $rushFee<br>";
	}
	$taskService->rushFee = $rushFee;
	$totalPrice += $rushFee;
	
	$_SESSION['totalCost'] = $totalCost;

	return $totalPrice;
}


?>