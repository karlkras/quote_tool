<?PHP
//error_reporting(E_ALL ^ E_DEPRECATED);
require_once("../attaskconn/LingoAtTaskService.php");

function clientSpecific(&$taskService, &$sellRates, $expedited, $clientScheme, $pmSurcharge, $passTrados) {
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
	if($expedited === 'custom25' ||$expedited === 'custom50') {
		$query = "SELECT rush_rate FROM ". $clientScheme. " WHERE task_name = 'Formatting_(DTP)'";
		$result = $myDBConn->query($query);
		$res1 =$result->fetch_assoc();
		if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
			$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = 'Formatting_(DTP)'";
			$result = $myDBConn->query($query) or die($myDBConn->error);
            if ($expedited == 'custom25') {
                $rateRush = 1.25;
            } else {
                $rateRush = 1.5;
            }
		}
	}
	else {
		$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = 'Formatting_(DTP)'";
		$result = $myDBConn->query($query) or die($myDBConn->error);
	}

   $result->data_seek(0);

	$res =  $result->fetch_array();
	if ($res != NULL)
	{
		$dtpHourly = ($res[0]/1000)* $rateRush;
	}
	else
	{
		$dtpHourly = -1;
	}

	$_SESSION['dtpHourly'] = $dtpHourly;

	//loop through the linguistic tasks and update the pricing
	if ( count($taskService->lingTasks) < 2)
	{
		$totalWordcount = $taskService->lingTasks->wordCounts->wordCount;
		if ($taskService->lingTasks->ltask->type == 'PR' && $_SESSION['proofReading'] == "no")
		{
			$taskName = "Proofreading_/_Linguistic_QA=" . str_replace(" ", "_", $taskService->lingTasks->sourceLang) . "=" . str_replace(" ","_", $taskService->lingTasks->targLang);
			if($expedited === 'custom25' ||$expedited === 'custom50') {
				$query = "SELECT rush_rate FROM ". $clientScheme. " WHERE task_name = '" . $taskName . "'";
				$result = $myDBConn->query($query);
				$res1 = $result->fetch_assoc();
				if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
					$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = '" . $taskName . "'";
					$result = $myDBConn->query($query) or die($myDBConn->error);
                    if ($expedited == 'custom25') {
                        $rateRush = 1.25;
                    } else {
                        $rateRush = 1.5;
                    }
				}
			}
			else {
				$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = '" . $taskName . "'";
				$result = $myDBConn->query($query) or die($myDBConn->error);
			}
            $result->data_seek(0);
			$res =  $result->fetch_array();
			if ($res != NULL)
			{
				$rate = ($res[0]/1000)* $rateRush;
			}
			else
			{
				//check for a default PR task in the DB
				$taskName = "Proofreading_/_Linguistic_QA";
				if($expedited === 'custom25' ||$expedited === 'custom50') {
					$query = "SELECT rush_rate FROM ". $clientScheme. " WHERE task_name = '" . $taskName . "'";
					$result = $myDBConn->query($query);
					$res1 = $result->fetch_assoc();
					if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
						$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = '" . $taskName . "'";
						$result = $myDBConn->query($query) or die($myDBConn->error);
                        if ($expedited == 'custom25') {
                            $rateRush = 1.25;
                        } else {
                            $rateRush = 1.5;
                        }
					}
				}
				else {
					$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = '" . $taskName . "'";
					$result = $myDBConn->query($query) or die($myDBConn->error);
				}
                $result->data_seek(0);
				$res =  $result->fetch_row();
				if ($res != NULL)
				{
					$rate = ($res[0]/1000)* $rateRush;
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
					$rate = ($hourlyRate / 0.5)* $rateRush;
				}
			}
			$sellRates[$taskService->lingTasks->ltask->id] = $rate;
			$price = round(($taskService->lingTasks->ltask->workRequired) * $rate,2);
			$totalCost += $taskService->lingTasks->ltask->workRequired * $taskService->lingTasks->wordRateDetails->hourly;
			
		}
		elseif (($taskService->lingTasks->ltask->type == 'TR') || ($taskService->lingTasks->ltask->type == 'TR+CE'))
		{
		
			//if we are passing on leveraging, then we need different rules
			if ($passTrados)
			{
				$taskRates = getTradosRates($taskService->lingTasks, $clientScheme, $useUSLinguists);
				$newWords = $taskService->lingTasks->WordCounts->newWords;
				$fuzzyWords = $taskService->lingTasks->WordCounts->fuzzyWords;
				$matchWords = $taskService->lingTasks->WordCounts->matchRepsWords;
				
				$sellRates[$taskService->lingTasks->ltask->id] = $taskRates;
				$price = round( ($newWords*$taskRates['New_Text']) + ($fuzzyWords*$taskRates['Fuzzy_Text']) + ($matchWords*$taskRates['Match_Text']),2);
				if ($taskService->lingTasks->ltask->type == 'TR')
				{
					if ($useUSLinguists)
					{
						$totalCost += $newWords * $taskService->lingTasks->wordRateDetails->UStr_based_new;
						$totalCost += $fuzzyWords * $taskService->lingTasks->wordRateDetails->UStr_based_fuzzy;
						$totalCost += $matchWords * $taskService->lingTasks->wordRateDetails->UStr_based_100Match;
					}
					else
					{
						$totalCost += $newWords * $taskService->lingTasks->wordRateDetails->tr_new;
						$totalCost += $fuzzyWords * $taskService->lingTasks->wordRateDetails->tr_fuzzy;
						$totalCost += $matchWords * $taskService->lingTasks->wordRateDetails->tr_100Match;
					}
				}
				else
				{
					if ($useUSLinguists)
					{
						$totalCost += $newWords * $taskService->lingTasks->wordRateDetails->US_based_new;
						$totalCost += $fuzzyWords * $taskService->lingTasks->wordRateDetails->US_based_fuzzy;
						$totalCost += $matchWords * $taskService->lingTasks->wordRateDetails->US_based_100Match;
					}
					else
					{
						$totalCost += $newWords * $taskService->lingTasks->wordRateDetails->trce_new;
						$totalCost += $fuzzyWords * $taskService->lingTasks->wordRateDetails->trce_fuzzy;
						$totalCost += $matchWords * $taskService->lingTasks->wordRateDetails->trce_100Match;
					}
				}
				
			}
			else
			{
				if (($taskService->lingTasks->wordCounts->newText > 0) || ($taskService->lingTasks->wordCounts->fuzzyText > 0) || 
					($taskService->lingTasks->wordCounts->matchRepsText > 0))
				{
					$taskRates = getTradosRates($taskService->lingTasks, $clientScheme, $useUSLinguists);
					$sellRates[$taskService->lingustTasks->ltask->id]= $taskRates['New_Text'];
					$newWords = $taskService->lingTasks->wordCounts->newWords;				
					$fuzzyWords = $taskService->lingTasks->wordCounts->fuzzyWords;
					$matchWords = $taskService->lingTasks->wordCounts->matchRepsWords;
					//$totalCost += round( ($newWords*$taskRates['New_Text']) + ($fuzzyWords*$taskRates['Fuzzy_Text']) + ($matchWords*$taskRates['Match_Text']),2);
					
					$totalWordcount= $newWords + $fuzzyWords + $matchWords;
					$taskService->lingTasks->wordCounts->wordCount = $totalWordcount;
					$price = round($totalWordcount * $sellRates[$lingTask->ltask->id],2);
					if ($taskService->lingTasks->ltask->type == 'TR')
					{
						if ($useUSLinguists)
						{
							$totalCost += $newWords * $taskService->lingTasks->wordRateDetails->UStr_based_new;
							$totalCost += fuzzyWords * $taskService->lingTasks->wordRateDetails->UStr_based_fuzzy;
							$totalCost += $matchWords * $taskService->lingTasks->wordRateDetails->UStr_based_100Match;
						}
						else
						{
							$totalCost += $newWords * $taskService->lingTasks->wordRateDetails->tr_new;
							$totalCost += fuzzyWords * $taskService->lingTasks->wordRateDetails->tr_fuzzy;
							$totalCost += $matchWords * $taskService->lingTasks->wordRateDetails->tr_100Match;
						}
					}
					else
					{
						if ($useUSLinguists)
						{
							$totalCost += $newWords * $taskService->lingTasks->wordRateDetails->US_based_new;
							$totalCost += fuzzyWords * $taskService->lingTasks->wordRateDetails->US_based_fuzzy;
							$totalCost += $matchWords * $taskService->lingTasks->wordRateDetails->US_based_100Match;
						}
						else
						{
							$totalCost += $newWords * $taskService->lingTasks->wordRateDetails->trce_new;
							$totalCost += fuzzyWords * $taskService->lingTasks->wordRateDetails->trce_fuzzy;
							$totalCost += $matchWords * $taskService->lingTasks->wordRateDetails->trce_100Match;
						}
					}
				}
				else
				{
					$rate = getNonTradosRates($taskService->lingTasks, $clientScheme, $useUSLinguists);
					$sellRates[$taskService->lingTasks->ltask->id] = $rate;
					$price = round( $rate * $taskService->lingTasks->wordCounts->wordCount,2);
					if ($taskService->lingTasks->ltask->type == 'TR')
					{
						if ($useUSLinguists)
						{
							$totalCost += $taskService->lingTasks->wordCounts->wordCount * $taskService->lingTasks->wordRateDetails->UStr_based_new;
						}
						else
						{
							$totalCost += $taskService->lingTasks->wordCounts->wordCount * $taskService->lingTasks->wordRateDetails->tr_new;
						}
					}
					else
					{
						if ($useUSLinguists)
						{
							$totalCost += $taskService->lingTasks->wordCounts->wordCount * $taskService->lingTasks->wordRateDetails->US_based_new;
						}
						else
						{
							$totalCost += $taskService->lingTasks->wordCounts->wordCount * $taskService->lingTasks->wordRateDetails->trce_new;
						}
					}
				}
			}
			
		}
		
		
		//get the DTP price (if any)
		$dtpData = unserialize($_SESSION['dtpData']);
		if ($dtpHourly == -1)
		{ 
			if ($useUSLinguists)
			{
				$hourlyRate = ($taskService->lingTasks->wordRateDetails->US_based_hourly > 0) ? $taskService->lingTasks->wordRateDetails->US_based_hourly : $taskService->lingTasks->wordRateDetails->hourly;
			}
			else
			{
				$hourlyRate = $taskService->lingTasks->wordRateDetails->hourly;
			}
			$dtpPrice = ($hourlyRate * $taskService->lingTasks->wordCounts->formattingHours) / 0.5;
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
		$totalCost += $hourlyRate * $taskService->lingTasks->wordCounts->formattingHours;
		
		
		$taskCount++;
	}
	else
	{
		foreach ($taskService->lingTasks as $lingTask)
		{
			$totalWordcount = $lingTask->wordCounts->wordCount;
			if ($lingTask->ltask->type == 'PR' && $_SESSION['proofReading'] == "no")
			{
				$taskName = "Proofreading_/_Linguistic_QA=" . str_replace(" ", "_", $lingTask->sourceLang) . "=" . str_replace(" ","_", $lingTask->targLang);
				if($expedited === 'custom25' ||$expedited === 'custom50') {
					$query = "SELECT rush_rate FROM ". $clientScheme. " WHERE task_name = '" . $taskName . "'";
					$result = $myDBConn->query($query);
					$res1 = $result->fetch_assoc();
					if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
						$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = '" . $taskName . "'";
						$result = $myDBConn->query($query) or die($myDBConn->error);
                        if ($expedited == 'custom25') {
                            $rateRush = 1.25;
                        } else {
                            $rateRush = 1.5;
                        }
					}
				}
				else {
					$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = '" . $taskName . "'";
					$result = $myDBConn->query($query) or die($myDBConn->error);
				}

                $result->data_seek(0);
				$res =  $result->fetch_row();
				
				if ($res != NULL) {
					$rate = ($res[0]/1000)* $rateRush;
				}
				else {
					//check for a default PR task in the DB
					$taskName = "Proofreading_/_Linguistic_QA";
					if($expedited === 'custom25' ||$expedited === 'custom50') {
                        $rateRush = 1;
						$query = "SELECT rush_rate FROM ". $clientScheme. " WHERE task_name = '" . $taskName . "'";
						$result = $myDBConn->query($query);
						$res1 = $result->fetch_assoc();
						if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
							$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = '" . $taskName . "'";
							$result = $myDBConn->query($query) or die($myDBConn->error);
                            if ($expedited == 'custom25') {
                                $rateRush = 1.25;
                            } else {
                                $rateRush = 1.5;
                            }
						}
					}
					else {
						$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = '" . $taskName . "'";
						$result = $myDBConn->query($query) or die($myDBConn->error);
					}

                    $result->data_seek(0);
					$res =  $result->fetch_row();
					
					if ($res != NULL) {
						$rate = ($res[0]/1000)* $rateRush;
					}
					else {
						//get the rate information from the api
						if ($useUSLinguist)
						{
							$hourlyRate = ($lingTask->wordRateDetails->US_based_hourly > 0) ? $lingTask->wordRateDetails->US_based_hourly : $lingTask->wordRateDetails->hourly;
						}
						else
						{
							$hourlyRate = $lingTask->wordRateDetails->hourly;
						}
						$rate = ($hourlyRate / 0.5)* $rateRush;
						
					}
				}
				
				$price = round(($lingTask->ltask->workRequired) * $rate,2);
				$sellRates[$lingTask->ltask->id] = $rate;
				$totalCost += $lingTask->ltask->workRequired * $lingTask->wordRateDetails->hourly;
				
			}
			elseif (($lingTask->ltask->type == 'TR') || ($lingTask->ltask->type == 'TR+CE'))
			{

				//if we are passing on leveraging, then we need different rules
				if ($passTrados)
				{
					$taskRates = getTradosRates($lingTask, $clientScheme, $useUSLinguists);
					$newWords = $lingTask->wordCounts->newWords;
					$fuzzyWords = $lingTask->wordCounts->fuzzyWords;
					$matchWords = $lingTask->wordCounts->matchRepsWords;
					$sellRates[$lingTask->ltask->id]=$taskRates;
					$price = round( ($newWords*$taskRates['New_Text']) + ($fuzzyWords*$taskRates['Fuzzy_Text']) + ($matchWords*$taskRates['Match_Text']),2);
					if ($lingTask->ltask->type == 'TR')
					{
						if ($useUSLinguists)
						{
							$totalCost += $newWords * $lingTask->wordRateDetails->UStr_based_new;
							$totalCost += $fuzzyWords * $lingTask->wordRateDetails->UStr_based_fuzzy;
							$totalCost += $matchWords * $lingTask->wordRateDetails->UStr_based_100Match;
						}
						else
						{
							$totalCost += $newWords * $lingTask->wordRateDetails->tr_new;
							$totalCost += $fuzzyWords * $lingTask->wordRateDetails->tr_fuzzy;
							$totalCost += $matchWords * $lingTask->wordRateDetails->tr_100Match;
						}
					}
					else
					{
						if ($useUSLinguists)
						{
							$totalCost += $newWords * $lingTask->wordRateDetails->US_based_new;
							$totalCost += $fuzzyWords * $lingTask->wordRateDetails->US_based_fuzzy;
							$totalCost += $matchWords * $lingTask->wordRateDetails->US_based_100Match;
						}
						else
						{
							$totalCost += $newWords * $lingTask->wordRateDetails->trce_new;
							$totalCost += $fuzzyWords * $lingTask->wordRateDetails->trce_fuzzy;
							$totalCost += $matchWords * $lingTask->wordRateDetails->trce_100Match;
						}
					}
				}
				else
				{
					if ($lingTask->wordCounts->wordCount != 0)
					{
						$rate = getNonTradosRates($lingTask, $clientScheme, $useUSLinguists);
						$sellRates[$lingTask->ltask->id]=$rate;
						$price = round( $rate * $lingTask->wordCounts->wordCount,2);
						
						if ($lingTask->ltask->type == 'TR')
						{
							if ($useUSLinguists)
							{
								$totalCost += $lingTask->wordCounts->wordCount * $lingTask->wordRateDetails->UStr_based_new;
							}
							else
							{
								$totalCost += $lingTask->wordCounts->wordCount * $lingTask->wordRateDetails->tr_new;
							}
						}
						else
						{
							if ($useUSLinguists)
							{
								$totalCost += $lingTask->wordCounts->wordCount * $lingTask->wordRateDetails->US_based_new;
							}
							else
							{
								$totalCost += $lingTask->wordCounts->wordCount * $lingTask->wordRateDetails->trce_new;
							}
						}
					}
					else
					{
						$taskRates = getTradosRates($lingTask, $clientScheme, $useUSLinguists);
						$sellRates[$lingTask->ltask->id]= $taskRates['New_Text'];
						$newWords = $lingTask->wordCounts->newWords;				
						$fuzzyWords = $lingTask->wordCounts->fuzzyWords;
						$matchWords = $lingTask->wordCounts->matchRepsWords;
						
						$totalWordcount= $newWords + $fuzzyWords + $matchWords;
						$lingTask->wordCounts->wordCount = $totalWordcount;
						$price = round($totalWordcount * $sellRates[$lingTask->ltask->id],2);
						

						if ($lingTask->ltask->type == 'TR')
						{
							if ($useUSLinguists)
							{
								$totalCost += $newWords * $lingTask->wordRateDetails->UStr_based_new;
								$totalCost += $fuzzyWords * $lingTask->wordRateDetails->UStr_based_fuzzy;
								$totalCost += $matchWords * $lingTask->wordRateDetails->UStr_based_100Match;
							}
							else
							{
								$totalCost += $newWords * $lingTask->wordRateDetails->tr_new;
								$totalCost += $fuzzyWords * $lingTask->wordRateDetails->tr_fuzzy;
								$totalCost += $matchWords * $lingTask->wordRateDetails->tr_100Match;
							}
						}
						else
						{
							if ($useUSLinguists)
							{
								$totalCost += $newWords * $lingTask->wordRateDetails->US_based_new;
								$totalCost += $fuzzyWords * $lingTask->wordRateDetails->US_based_fuzzy;
								$totalCost += $matchWords * $lingTask->wordRateDetails->US_based_100Match;
							}
							else
							{
								$totalCost += $newWords * $lingTask->wordRateDetails->trce_new;
								$totalCost += $fuzzyWords * $lingTask->wordRateDetails->trce_fuzzy;
								$totalCost += $matchWords * $lingTask->wordRateDetails->trce_100Match;
							}
						}
					}
				}
//				$lingTask->ltask->price = $price;
			}

			//get the DTP price (if any)
			$dtpData = unserialize($_SESSION['dtpData']);
			if ($dtpHourly == -1)
			{ 
				if ($useUSLinguists)
				{
					$hourlyRate = ($lingTask->wordRateDetails->US_based_hourly > 0) ? $lingTask->wordRateDetails->US_based_hourly : $lingTask->wordRateDetails->hourly;
				}
				else
				{
					$hourlyRate = $lingTask->wordRateDetails->hourly;
				}
//				$dtpPrice = ($lingTask->wordCounts->formattingHours * $hourlyRate) / 0.5;
                $dtpPrice = $lingTask->wordCounts->formattingHours * $hourlyRate;
                $dtpData[$lingTask->ltask->id]['rate'] = $hourlyRate;
				$totalCost += $hourlyRate * $lingTask->wordCounts->formattingHours;
			}
			else
			{
				$dtpPrice = $lingTask->wordCounts->formattingHours * $dtpHourly;
				$dtpData[$lingTask->ltask->id]['rate'] = $dtpHourly;
				$totalCost += $dtpHourly * $lingTask->wordCounts->formattingHours;
			}
			$dtpPrice = round($dtpPrice,2);
			
			$dtpData[$lingTask->ltask->id]['price'] = $dtpPrice;
			$_SESSION['dtpData'] = serialize($dtpData);
			
			$lingTask->ltask->price = $price + $dtpPrice;
			$totalPrice += $price + $dtpPrice;
			
			
			//echo $lingTask->ltask->name, ": $", number_format($lingTask->ltask->price,2), ": ", $lingTask->ltask->workRequired, " hours\n";
			//echo  "DTP: $", number_format($dtpPrice,2), ": ",$lingTask->wordCounts->formattingHours," hours: $totalPrice\n";
			
			$taskCount++;
		}
	}
	
	
	
	
	if ( count($taskService->billableTasks) < 2)
	{
		if ($taskService->billableTasks->btask->name != 'Project Management')
		{
			//look in database for custom price data
			
			if ($_SESSION['bundleInternal'])
			{
				if($expedited === 'custom25' ||$expedited === 'custom50') {
					$query = "SELECT rush_rate FROM ". $clientScheme. " WHERE task_name = 'Formatting_(DTP)'";
					$result = $myDBConn->query($query);
					$res1 = $result->fetch_assoc();
					if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
						$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = 'Formatting_(DTP)'";
						$result = $myDBConn->query($query) or die($myDBConn->error);
                        if ($expedited == 'custom25') {
                            $rateRush = 1.25;
                        } else {
                            $rateRush = 1.5;
                        }
					}
				}
				else {
					$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = 'Formatting_(DTP)'";
					$result = $myDBConn->query($query) or die($myDBConn->error);
				}

               $result->data_seek(0);
				$res =  $result->fetch_row();
				
				if ($res != NULL)
				{
					$rate = ($res[0]/1000)* $rateRush;
				}
				else
				{
					$rate = 60* $rateRush;
				}
			}
			else
			{
				if($expedited === 'custom25' ||$expedited === 'custom50') {
					$query = "SELECT rush_rate FROM ". $clientScheme. " WHERE task_name = 'Formatting_(DTP)'";
					$result = $myDBConn->query($query);
					$res1 = $result->fetch_assoc();
					if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
						$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = 'Formatting_(DTP)'";
						$result = $myDBConn->query($query) or die($myDBConn->error);
                        if ($expedited == 'custom25') {
                            $rateRush = 1.25;
                        } else {
                            $rateRush = 1.5;
                        }
					}
				}
				else {
					$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = 'Formatting_(DTP)'";
					$result = $myDBConn->query($query) or die($myDBConn->error);
				}

                $result->data_seek(0);
				$res =  $result->fetch_row();
				
				if ($res != NULL)
				{
					$rate = ($res[0]/1000)* $rateRush;
				}
				else
				{
					$rate = 60* $rateRush;
				}
				
			}
			$price = ($taskService->billableTasks->btask->workRequired) * $rate;
			$price = round($price,2);
			$sellRates[$taskService->billableTasks->btask->id] = $rate;
		
	
			$taskService->billableTasks->btask->price = $price;
			$totalPrice += $price;
			$totalCost += $taskService->billableTasks->btask->workRequired * $taskService->billableTasks->hourlyRate;
			//echo $taskService->billableTasks->btask->name, ": $", number_format($price,2),": $totalPrice<br>";		
			$taskCount++;
		}
	}
	else
	{
	
		//loop through the billable tasks
		foreach ($taskService->billableTasks as $billTask)
		{
			if ($billTask->btask->name != 'Project Management')
			{
				if ($_SESSION['bundleInternal'])
				{
					if($expedited === 'custom25' ||$expedited === 'custom50') {
						$query = "SELECT rush_rate FROM ". $clientScheme. " WHERE task_name = 'Formatting_(DTP)'";
						$result = $myDBConn->query($query);
						$res1 = $result->fetch_assoc();
						if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
							$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = 'Formatting_(DTP)'";
							$result = $myDBConn->query($query) or die($myDBConn->error);
                            if ($expedited == 'custom25') {
                                $rateRush = 1.25;
                            } else {
                                $rateRush = 1.5;
                            }
						}
					}
					else {
						$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = 'Formatting_(DTP)'";
						$result = $myDBConn->query($query) or die($myDBConn->error);
					}

                    $result->data_seek(0);
					$res =  $result->fetch_row();
					
					if ($res != NULL)
					{
						$rate = ($res[0]/1000)* $rateRush;
					}
					else
					{
						$rate = 60* $rateRush;
					}
				}
				else
				{
					//look in database for custom price data
					if($expedited === 'custom25' ||$expedited === 'custom50') {
						$query = "SELECT rush_rate FROM ". $clientScheme. " WHERE task_name = 'Formatting_(DTP)'";
						$result = $myDBConn->query($query);
						$res1 = $result->fetch_assoc();
						if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
							$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = 'Formatting_(DTP)'";
							$result = $myDBConn->query($query) or die($myDBConn->error);
                            if ($expedited == 'custom25') {
                                $rateRush = 1.25;
                            } else {
                                $rateRush = 1.5;
                            }
						}
					}
					else {
						$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = 'Formatting_(DTP)'";
						$result = $myDBConn->query($query) or die($myDBConn->error);
					}

                    $result->data_seek(0);
					$res =  $result->fetch_row();
					
					if ($res != NULL)
					{
						$rate = ($res[0]/1000)* $rateRush;
					}
					else
					{
						$rate = 60* $rateRush;
					}
				}
				
				$price = ($billTask->btask->workRequired) * $rate;
				$price = round($price,2);
				$sellRates[$billTask->btask->id] = $rate;
			
		
				$billTask->btask->price = $price;
				$totalPrice += $price;
				$totalCost += $billTask->hourlyRate * $billTask->btask->workRequired;
				//echo $billTask->btask->name, ": $", number_format($price,2),": $totalPrice<br>";		
				$taskCount++;
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
	
	if (count($taskService->billableTasks) < 2)
	{
		//look in database for custom price data
		if ($taskService->billableTasks->btask->name == 'Project Management')
		{
			if (($totalPrice > $minimum) && ($pmSurcharge == true))
			{
				if($expedited === 'custom25' ||$expedited === 'custom50') {
					$query = "SELECT rush_rate FROM ". $clientScheme. " WHERE task_name = 'Project_Management'";
					$result = $myDBConn->query($query);
					$res1 =$result->fetch_assoc();
					if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
						$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = 'Project_Management'";
						$result = $myDBConn->query($query) or die($myDBConn->error);
                        if ($expedited == 'custom25') {
                            $rateRush = 1.25;
                        } else {
                            $rateRush = 1.5;
                        }
					}
				}
				else {
					$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = 'Project_Management'";
					$result = $myDBConn->query($query) or die($myDBConn->error);
				}

                $result->data_seek(0);
				$res =  $result->fetch_row();
				
				if ($res != NULL)
				{
					$rate = ($res[0]/100000)* $rateRush;
				}
				else
				{
					$rate = 0.1* $rateRush;
				}
				$price = $totalPrice / (1-$rate) * $rate;
				$sellRates[$taskService->billableTasks->btask->id] = $rate;
				$_SESSION['realTotalPrice'] = $totalPrice;
                $taskService->billableTasks->btask->price = $price;
				$totalPrice += $price;
			}
			$totalCost += $taskService->billableTasks->hourlyRate * $taskService->billableTasks->btask->workRequired;
			//echo $taskService->billableTasks->btask->name, ": $", number_format($price,2),": $totalPrice<br>";		
			$taskCount++;
		}
	}
	else
	{
		foreach ($taskService->billableTasks as $billTask)
		{
			if ($billTask->btask->name == 'Project Management')
			{
				if (($totalPrice > $minimum) && ($pmSurcharge == true)) {
					//look in database for custom price data
					if($expedited === 'custom25' ||$expedited === 'custom50') {
						$query = "SELECT rush_rate FROM ". $clientScheme. " WHERE task_name = 'Project_Management'";
						$result = $myDBConn->query($query);
						$res1 = $result->fetch_assoc();
						if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
							$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = 'Project_Management'";
							$result = $myDBConn->query($query) or die($myDBConn->error);
                            if ($expedited == 'custom25') {
                                $rateRush = 1.25;
                            } else {
                                $rateRush = 1.5;
                            }
						}
					}
					else {
						$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = 'Project_Management'";
						$result = $myDBConn->query($query) or die($myDBConn->error);
					}

                    $result->data_seek(0);
					$res =  $result->fetch_row();

					if ($res != NULL)
					{
						$rate = ($res[0]/100000)* $rateRush;
					}
					else
					{
						$rate = 0.1* $rateRush;
					}
					$price = $totalPrice / (1-$rate) * $rate;

					$billTask->btask->price = $price;
					$sellRates[$billTask->btask->id] = $rate;
					$totalPrice += $price;
				}
				//echo $billTask->btask->name, ": $", number_format($price,2),": $totalPrice<br>";		
				$totalCost += $billTask->hourlyRate * $billTask->btask->workRequired;
				$taskCount++;
			}
		}
	}

	//check for discount
	$clientDiscountPercent = 0;
	$clientDiscountAmount = 0;
	$query = "SELECT discount FROM clients WHERE table_name = '". $clientScheme. "'";
	$result = $myDBConn->query($query);
	
	if ($result->num_rows > 0)
	{
		$res = $result->fetch_assoc();
		if (is_null($res['discount']))
		{
			$clientDiscountPercent = 0;
		}
		else
		{
			$clientDiscountPercent = $res['discount'];
		}
	}
	
	if ($clientDiscountPercent > 0)
	{
		$clientDiscountAmount = round($totalPrice * ($clientDiscountPercent/100),2);
	}
	$taskService->discount = 0 - $clientDiscountAmount;
	$totalPrice = $totalPrice - $clientDiscountAmount;
	
	
	//check for rush fees.
	if (count($taskService->lingTasks) < 2)
	{
		$wordCount = $taskService->lingTasks->wordCounts->wordCount;
	}
	else
	{
		$wordCount = $taskService->lingTasks[0]->wordCounts->wordCount;
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
	$myDBConn->close();
	return $totalPrice;
}

function getSchemeName($projectObj)
{
$client_id=$projectObj->company->id;
		
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	$query = "SELECT * FROM clients WHERE attask_id = ".$client_id;
	$result = $myDBConn->query($query);

	if ($result->num_rows > 0)
	{
		$res = $result->fetch_assoc();
		$schemeApplied = $res['table_name'];
		
	}
	else
	{
		$schemeApplied = 'none';

	}
	$myDBConn->close();
	return $schemeApplied;

}


?>