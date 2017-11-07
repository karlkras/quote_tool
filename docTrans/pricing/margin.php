<?PHP



function margin($margin, $passTrados, $pmSurcharge, &$sellRates)
{
$taskCount = 0;
$totalPrice = 0;
$totalCost = 0;

$api = new lingoAtTaskService();

$taskService = unserialize($_SESSION['taskService']);
$useUSLinguists = $_SESSION['useUSLinguists'];

	//loop through the linguistic tasks and update the pricing
	if (count($taskService->lingTasks) < 2)
	{
		$lingTask = $taskService->lingTasks;
		$totalWordcount = $lingTask->wordCounts->wordCount;	
		if ($lingTask->ltask->type != 'PR' || $_SESSION['proofReading'] == "yes")
		{
			$rate = 1;	
			if ($passTrados)
			{
				$taskRates = getTradosRates($lingTask, NULL, $useUSLinguists); 
				$newWords = $lingTask->wordCounts->newWords;				
				$fuzzyWords = $lingTask->wordCounts->fuzzyWords;
				$matchWords = $lingTask->wordCounts->matchRepsWords;
				$totalCost += round( ($newWords*$taskRates['New_Text']) + ($fuzzyWords*$taskRates['Fuzzy_Text']) + ($matchWords*$taskRates['Match_Text']),2);
				$sellRates[$lingTask->ltask->id]['New_Text'] = roundQuarter($taskRates['New_Text'] / (1-($margin/100)));
				$sellRates[$lingTask->ltask->id]['Fuzzy_Text'] = roundQuarter($taskRates['Fuzzy_Text'] / (1-($margin/100)));
				$sellRates[$lingTask->ltask->id]['Match_Text'] = roundQuarter($taskRates['Match_Text'] / (1-($margin/100)));
				$price = ( ($newWords*$sellRates[$lingTask->ltask->id]['New_Text']) + ($fuzzyWords*$sellRates[$lingTask->ltask->id]['Fuzzy_Text']) + ($matchWords*$sellRates[$lingTask->ltask->id]['Match_Text']) );
			}
			else
			{
				if ($lingTask->wordCounts->wordCount != 0)
				{
					$rate = getNonTradosRates($lingTask, NULL, $useUSLinguists);
					$totalCost += $totalWordcount * $rate;
					$sellRates[$lingTask->ltask->id] = roundQuarter($rate / (1-($margin/100)));
					$price = round($totalWordcount * $sellRates[$lingTask->ltask->id],2);
				}
				else
				{
					$taskRates = getTradosRates($lingTask, NULL, $useUSLinguists);
					$newWords = $lingTask->wordCounts->newWords;				
					$fuzzyWords = $lingTask->wordCounts->fuzzyWords;
					$matchWords = $lingTask->wordCounts->matchRepsWords;
					$totalCost += round( ($newWords*$taskRates['New_Text']) + ($fuzzyWords*$taskRates['Fuzzy_Text']) + ($matchWords*$taskRates['Match_Text']),2);
					$sellRates[$lingTask->ltask->id] = roundQuarter($taskRates['New_Text'] / (1-($margin/100)));
					$totalWordcount= $newWords + $fuzzyWords + $matchWords;
					$lingTask->wordCounts->wordCount = $totalWordcount;
					$price = round($totalWordcount * $sellRates[$lingTask->ltask->id],2);
				}
				
			
				
			}
			
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
			$totalCost += round($lingTask->ltask->workRequired * $hourlyRate,2);
			$hourlyRate = roundQuarter($hourlyRate / (1-($margin/100)));
			$price = round($lingTask->ltask->workRequired * $hourlyRate,2);
			$sellRates[$lingTask->ltask->id] = $hourlyRate;
		}
		
		if ($useUSLinguists)
		{
			$hourlyRate = ($lingTask->wordRateDetails->US_based_hourly > 0) ? $lingTask->wordRateDetails->US_based_hourly : $lingTask->wordRateDetails->hourly;
		}
		else
		{
			$hourlyRate = $lingTask->wordRateDetails->hourly;
		}
		
		$dtpData[$lingTask->ltask->id]['rate'] = roundQuarter($hourlyRate / (1-($margin/100)));
		$dtpPrice = $lingTask->wordCounts->formattingHours * $dtpData[$lingTask->ltask->id]['rate'];
		$dtpPrice = round($dtpPrice ,2);

		$dtpData[$lingTask->ltask->id]['price'] = $dtpPrice;
		

		$lingTask->ltask->price = $price + $dtpPrice;
		$totalPrice += $price + $dtpPrice;
		$totalCost += round($lingTask->wordCounts->formattingHours * $hourlyRate,2);
		
		$taskCount++;
		$taskService->lingTasks = $lingTask;

	}
	else
	{
		foreach ($taskService->lingTasks as $lingTask)
		{
			
			$totalWordcount = $lingTask->wordCounts->wordCount;	
			if ($lingTask->ltask->type != 'PR' || $_SESSION['proofReading'] == "yes")
			{
				$rate = 1;	
				if ($passTrados)
				{
					$taskRates = getTradosRates($lingTask, NULL, $useUSLinguists); 
					$newWords = $lingTask->wordCounts->newWords;				
					$fuzzyWords = $lingTask->wordCounts->fuzzyWords;
					$matchWords = $lingTask->wordCounts->matchRepsWords;
					$totalCost += round( ($newWords*$taskRates['New_Text']) + ($fuzzyWords*$taskRates['Fuzzy_Text']) + ($matchWords*$taskRates['Match_Text']),2);
					$sellRates[$lingTask->ltask->id]['New_Text'] = roundQuarter($taskRates['New_Text'] / (1-($margin/100)));
					$sellRates[$lingTask->ltask->id]['Fuzzy_Text'] = roundQuarter($taskRates['Fuzzy_Text'] / (1-($margin/100)));
					$sellRates[$lingTask->ltask->id]['Match_Text'] = roundQuarter($taskRates['Match_Text'] / (1-($margin/100)));
					$price = round( ($newWords*$sellRates[$lingTask->ltask->id]['New_Text']) + ($fuzzyWords*$sellRates[$lingTask->ltask->id]['Fuzzy_Text']) + ($matchWords*$sellRates[$lingTask->ltask->id]['Match_Text']),2);
				}
				else
				{
					if ($lingTask->wordCounts->wordCount != 0)
					{
						$rate = getNonTradosRates($lingTask, NULL, $useUSLinguists);
						$totalCost += $totalWordcount * $rate;
						$sellRates[$lingTask->ltask->id] = roundQuarter($rate / (1-($margin/100)));
						$price = round($totalWordcount * $sellRates[$lingTask->ltask->id],2);
					}
					else
					{
						$taskRates = getTradosRates($lingTask, NULL, $useUSLinguists);
						$newWords = $lingTask->wordCounts->newWords;				
						$fuzzyWords = $lingTask->wordCounts->fuzzyWords;
						$matchWords = $lingTask->wordCounts->matchRepsWords;
						$totalCost += round( ($newWords*$taskRates['New_Text']) + ($fuzzyWords*$taskRates['Fuzzy_Text']) + ($matchWords*$taskRates['Match_Text']),2);
						$sellRates[$lingTask->ltask->id] = roundQuarter($taskRates['New_Text'] / (1-($margin/100)));
						$totalWordcount = $newWords + $fuzzyWords + $matchWords;
						$lingTask->wordCounts->wordCount = $totalWordcount;
						$price = round($totalWordcount * $sellRates[$lingTask->ltask->id],2);
					}
				
					
				}
				
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
				$totalCost += round($lingTask->ltask->workRequired * $hourlyRate,2);
				$hourlyRate = $hourlyRate / (1-($margin/100));
				$price = round($lingTask->ltask->workRequired * $hourlyRate,2);
				$sellRates[$lingTask->ltask->id] = $hourlyRate;
			}
			
			if ($useUSLinguists)
			{
				$hourlyRate = ($lingTask->wordRateDetails->US_based_hourly > 0) ? $lingTask->wordRateDetails->US_based_hourly : $lingTask->wordRateDetails->hourly;
			}
			else
			{
				$hourlyRate = $lingTask->wordRateDetails->hourly;
			}
			
			$dtpData[$lingTask->ltask->id]['rate'] = roundQuarter($hourlyRate / (1-($margin/100)));
			$dtpPrice = $lingTask->wordCounts->formattingHours * $dtpData[$lingTask->ltask->id]['rate'];
			$dtpPrice = round($dtpPrice ,2);
	
			$dtpData[$lingTask->ltask->id]['price'] = $dtpPrice;
			
			
			$lingTask->ltask->price = $price + $dtpPrice;
			$totalPrice += $price + $dtpPrice;
			$totalCost += round($lingTask->wordCounts->formattingHours * $hourlyRate,2);
	
			
			$taskCount++;
		}
	}
	//loop through the billable tasks
	if ( count($taskService->billableTasks) < 2)
	{
		if ($taskService->billableTasks->btask->name != 'Project Management')
		{
			$totalCost += round($taskService->billableTasks->btask->workRequired * $taskService->billableTasks->hourlyRate,2);
			$price = round($taskService->billableTasks->btask->workRequired * ($taskService->billableTasks->hourlyRate / (1-($margin/100))),2);
			$taskService->billableTasks->btask->price = $price;
			$totalPrice += $price;
			$taskCount++;
			$sellRates[$taskService->billableTasks->btask->id] = round($taskService->billableTasks->hourlyRate / (1-($margin/100)),2);
		}
		
		
	}
	else
	{
		foreach ($taskService->billableTasks as $billTask)
		{
			if ($billTask->btask->name != 'Project Management')
			{
				$totalCost += round($billTask->btask->workRequired * $billTask->hourlyRate,2);
				$price = round($billTask->btask->workRequired * ($billTask->hourlyRate / (1-($margin/100))),2);
				$billTask->btask->price = $price;
				$totalPrice += $price;
				$taskCount++;
				$sellRates[$billTask->btask->id] = $billTask->hourlyRate / (1-($margin/100));
			}
		
			
		}
	}
	
	//calculate the PM fee
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
			//look in database for custom price data
			if ($taskService->billableTasks->btask->name == 'Project Management')
			{
				
				$price = round($totalPrice / 9,2);
				
				$taskService->billableTasks->btask->price = $price;
				$totalPrice += $price;
				$taskCount++;
				$totalCost += round($taskService->billableTasks->btask->workRequired * $taskService->billableTasks->hourlyRate,2);
			}
		}
		else
		{
			foreach ($taskService->billableTasks as $billTask)
			{
				if ($billTask->btask->name == 'Project Management')
				{
					
					$price = round($totalPrice / 9,2);
					
					$billTask->btask->price = $price;
					$totalPrice += $price;
					$taskCount++;
					$totalCost += round($billTask->btask->workRequired * $billTask->hourlyRate,2);
				}
			}
		}
	}
	
	//determine if rushfees are needed
	if (count($taskService->lingTasks) < 2)
		$wordCount = $taskService->lingTasks->wordCounts->wordCount;
	else
	{
		foreach($taskService->lingTasks as $lt)
		{
			if (($lt->ltask->type == 'TR') || ($lt->ltask->type == 'TR+CE'))
			{
				$wordCount = $lt->wordCounts->wordCount;
			}
		}
	}
	$rushFee = 0;
	$expedited = $_SESSION['rushFee'];
	if($expedited !== 0) {
		if ($expedited === 0.25 || $expedited === 'custom25') {
			$rushFee = round($totalPrice * 0.25,2);
		}
		else {
			$rushFee = round($totalPrice*0.5,2);
		}

//echo "Expedited Turnaround Surcharge: $rushFee<br>";
	}
	$taskService->rushFee = $rushFee;
	$totalPrice += $rushFee;

	$_SESSION['taskService'] = serialize($taskService);
	$_SESSION['dtpData'] = serialize($dtpData);
	$_SESSION['totalCost'] = $totalCost;
	return $totalPrice;
}




?>