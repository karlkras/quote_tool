<?PHP

function getTradosRates($theTask, $clientScheme, $useUSLinguists) {
	$rateRush = 1;
	$rates = array();
	$wordTypes = array('New_Text', 'Fuzzy_Text', 'Match_Text');
	$rushFee = $_SESSION['rushFee'];
	
	
	
	//get the task name
	switch($theTask->ltask->type)
	{
		case 'TR+CE': 
				$name = 'Translate_+_Copyedit';
				break;
		case 'TR':
		default: 
				$name = 'Translate';
				break;
	}
	
	//only open a DB connection if there's a pricing scheme.
	if ($clientScheme != NULL)
	{
		$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
		if ($myDBConn->connect_errno)
		{
			echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
			exit;
		}
	}

	foreach($wordTypes as $wordType)
	{
	
		if ($clientScheme != NULL)
		{
			
			$taskName = $name."#".$wordType."#=" . str_replace(" ", "_", $theTask->sourceLang) . "=" . str_replace(" ","_", $theTask->targLang);
			
			if($rushFee === 'custom25' || $rushFee === 'custom50') {
				$query = "SELECT rush_rate FROM ". $clientScheme. " WHERE task_name = '" . $taskName . "'";
				$result = $myDBConn->query($query);
				$res1 = $result->fetch_assoc();
				if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
					$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = '" . $taskName . "'";
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
				$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = '" . $taskName . "'";
				$result = $myDBConn->query($query) or die($myDBConn->error);
			}
            $result->data_seek(0);
			$res =  $result->fetch_row();
			
			if ($res != NULL)
			{
				$rates[$wordType] = ($res[0]/1000)* $rateRush;
			}
			else
			{
				//check for a default TR task in the DB
				$taskName = $name."#".$wordType."#";
				if($rushFee === 'custom25' || $rushFee === 'custom50') {
					$query = "SELECT rush_rate FROM ". $clientScheme. " WHERE task_name = '" . $taskName . "'";
					$result = $myDBConn->query($query);
					$res1 = $result->fetch_assoc();
					if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
						$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = '" . $taskName . "'";
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
					$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = '" . $taskName . "'";
					$result = $myDBConn->query($query) or die($myDBConn->error);
//                    $rateRush = $rushFee + $rateRush;
				}

                $result->data_seek(0);
				$res =  $result->fetch_row();
				
				if ($res != NULL)
				{
					$rates[$wordType] = ($res[0]/1000)* $rateRush;
				}
				else
				{
					if ($useUSLinguists)
					{
						switch ($wordType)
						{
							case 'New_Text': 
								if ($theTask->ltask->type == 'TR')
								{	
									$hourlyRate = ($theTask->wordRateDetails->UStr_based_new > 0) ? $theTask->wordRateDetails->UStr_based_new : $hourlyRate = $theTask->wordRateDetails->tr_new;
								}
								else
								{
									$hourlyRate = ($theTask->wordRateDetails->US_based_new > 0) ? $theTask->wordRateDetails->US_based_new : $hourlyRate = $theTask->wordRateDetails->trce_new;
								}
								break;
							case 'Fuzzy_Text':
								if ($theTask->ltask->type == 'TR')
								{	
									$hourlyRate = ($theTask->wordRateDetails->UStr_based_fuzzy > 0) ? $theTask->wordRateDetails->UStr_based_fuzzy : $hourlyRate = $theTask->wordRateDetails->tr_fuzzy;
								}
								else
								{
									$hourlyRate = ($theTask->wordRateDetails->US_based_fuzzy > 0) ? $theTask->wordRateDetails->US_based_fuzzy : $hourlyRate = $theTask->wordRateDetails->trce_fuzzy;
								}
								break;
							case 'Match_Text':
								if ($theTask->ltask->type == 'TR')
								{	
									$hourlyRate = ($theTask->wordRateDetails->UStr_based_100Match > 0) ? $theTask->wordRateDetails->UStr_based_100Match : $hourlyRate = $theTask->wordRateDetails->tr_100Match;
								}
								else
								{
									$hourlyRate = ($theTask->wordRateDetails->US_based_100Match > 0) ? $theTask->wordRateDetails->US_based_100Match : $hourlyRate = $theTask->wordRateDetails->trce_100Match;
								}
								break;
						}
						
					}
					else
					{
						switch ($wordType)
						{
							case 'New_Text':
								if ($theTask->ltask->type == 'TR')
									$hourlyRate = $theTask->wordRateDetails->tr_new;
								else
									$hourlyRate = $theTask->wordRateDetails->trce_new;
								break;
							case 'Fuzzy_Text':
								if ($theTask->ltask->type == 'TR')
									$hourlyRate = $theTask->wordRateDetails->tr_fuzzy;
								else
									$hourlyRate = $theTask->wordRateDetails->trce_fuzzy;
								break;
							case 'Match_Text':
								if ($theTask->ltask->type == 'TR')
									$hourlyRate = $theTask->wordRateDetails->tr_100Match;
								else
									$hourlyRate = $theTask->wordRateDetails->trce_100Match;
								break;
						}
					}
					$rates[$wordType] = ($hourlyRate / 0.5)* $rateRush;
				}
			}
			$result->free();
			
		}
		else	//we only need the @task rates
		{
			if ($useUSLinguists)
			{
				switch ($wordType)
				{
					case 'New_Text': 
						if ($theTask->ltask->type == 'TR')
						{	
							$hourlyRate = ($theTask->wordRateDetails->UStr_based_new > 0) ? $theTask->wordRateDetails->UStr_based_new : $hourlyRate = $theTask->wordRateDetails->tr_new;
						}
						else
						{
							$hourlyRate = ($theTask->wordRateDetails->US_based_new > 0) ? $theTask->wordRateDetails->US_based_new : $hourlyRate = $theTask->wordRateDetails->trce_new;
						}
						break;
					case 'Fuzzy_Text':
						if ($theTask->ltask->type == 'TR')
						{	
							$hourlyRate = ($theTask->wordRateDetails->UStr_based_fuzzy > 0) ? $theTask->wordRateDetails->UStr_based_fuzzy : $hourlyRate = $theTask->wordRateDetails->tr_fuzzy;
						}
						else
						{
							$hourlyRate = ($theTask->wordRateDetails->US_based_fuzzy > 0) ? $theTask->wordRateDetails->US_based_fuzzy : $hourlyRate = $theTask->wordRateDetails->trce_fuzzy;
						}
						break;
					case 'Match_Text':
						if ($theTask->ltask->type == 'TR')
						{	
							$hourlyRate = ($theTask->wordRateDetails->UStr_based_100Match > 0) ? $theTask->wordRateDetails->UStr_based_100Match : $hourlyRate = $theTask->wordRateDetails->tr_100Match;
						}
						else
						{
							$hourlyRate = ($theTask->wordRateDetails->US_based_100Match > 0) ? $theTask->wordRateDetails->US_based_100Match : $hourlyRate = $theTask->wordRateDetails->trce_100Match;
						}
						break;
				}
				
			}
			else
			{
				switch ($wordType)
				{
					case 'New_Text':
						if ($theTask->ltask->type == 'TR')
							$hourlyRate = $theTask->wordRateDetails->tr_new;
						else
							$hourlyRate = $theTask->wordRateDetails->trce_new;
						break;
					case 'Fuzzy_Text':
						if ($theTask->ltask->type == 'TR')
							$hourlyRate = $theTask->wordRateDetails->tr_fuzzy;
						else
							$hourlyRate = $theTask->wordRateDetails->trce_fuzzy;
						break;
					case 'Match_Text':
						if ($theTask->ltask->type == 'TR')
							$hourlyRate = $theTask->wordRateDetails->tr_100Match;
						else
							$hourlyRate = $theTask->wordRateDetails->trce_100Match;
						break;
				}
			}
			$rates[$wordType] = $hourlyRate;
		}
	
	}
	
	//check to see if we opened a DB connection, and if so close it
	if (isset($myDBConn) && (is_a($myDBConn,'mysqli')))
	{
		$myDBConn->close();
	}
	return $rates;

}

function getNonTradosRates($theTask, $clientScheme, $useUSLinguists){
	$rateRush = 1;
	$rate = 0;
	$rushFee = $_SESSION['rushFee'];

	if ($clientScheme != NULL)
	{
		//get the task name
		switch($theTask->ltask->type)
		{
			case 'TR+CE': 
					$name = 'Translate_+_Copyedit';
					break;
			case 'TR':
			default: 
					$name = 'Translate';
					break;
		}
		
		$taskName = $name."#New_Text#=" . str_replace(" ", "_", $theTask->sourceLang) . "=" . str_replace(" ","_", $theTask->targLang);
		
		$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
		if ($myDBConn->connect_errno)
		{
			echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
			exit;
		}
		
		if($rushFee === 'custom25' || $rushFee === 'custom50') {
			$query = "SELECT rush_rate FROM ". $clientScheme. " WHERE task_name = '" . $taskName . "'";
			$result = $myDBConn->query($query);
			$res1 = $result->fetch_assoc();
			if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
				$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = '" . $taskName . "'";
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
			//check for a default TR task in the DB
			$taskName = $name."#New_Text#";
			if($rushFee === 'custom25' || $rushFee === 'custom50') {
				$query = "SELECT rush_rate FROM ". $clientScheme. " WHERE task_name = '" . $taskName . "'";
				$result = $myDBConn->query($query);
				$res1 = $result->fetch_assoc();
				if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
					$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = '" . $taskName . "'";
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
					
					if ($theTask->ltask->type == 'TR')
					{	
						$hourlyRate = ($theTask->wordRateDetails->UStr_based_new > 0) ? $theTask->wordRateDetails->UStr_based_new : $hourlyRate = $theTask->wordRateDetails->tr_new;
					}
					else
					{
						$hourlyRate = ($theTask->wordRateDetails->US_based_new > 0) ? $theTask->wordRateDetails->US_based_new : $hourlyRate = $theTask->wordRateDetails->trce_new;
					}
							
					
				}
				else
				{
					
					if ($theTask->ltask->type == 'TR')
						$hourlyRate = $theTask->wordRateDetails->tr_new;
					else
						$hourlyRate = $theTask->wordRateDetails->trce_new;
						
				}
				$rate = ($hourlyRate / 0.5)* $rateRush;
			}
		}
		if (isset($result) && (is_a($result,'mysqli_result')))
		{
			$result->free();
		}
		$myDBConn->close();
	}
	else	//we only need the @task rates
	{
		if ($useUSLinguists)
		{
			
			if ($theTask->ltask->type == 'TR')
			{	
				$hourlyRate = ($theTask->wordRateDetails->UStr_based_new > 0) ? $theTask->wordRateDetails->UStr_based_new : $hourlyRate = $theTask->wordRateDetails->tr_new;
			}
			else
			{
				$hourlyRate = ($theTask->wordRateDetails->US_based_new > 0) ? $theTask->wordRateDetails->US_based_new : $hourlyRate = $theTask->wordRateDetails->trce_new;
			}
					
			
		}
		else
		{
			
			if ($theTask->ltask->type == 'TR')
				$hourlyRate = $theTask->wordRateDetails->tr_new;
			else
				$hourlyRate = $theTask->wordRateDetails->trce_new;
				
		}
		$rate = $hourlyRate;
	}
	
	return $rate;

}

function roundQuarter($input)
{
//rounds decimal values to the nearest hundreth, on the quarter
// e.g.:	>= 0.0025 rounds up to 0.01
//			< 0.0024 rounds down to 0.00

$output = 0;
	
	$temp = $input * 100;
	
	$floor = floor($temp);
	$decimal = $temp - $floor;
	
	if ($decimal >= 0.25)
		$output = $floor+1;
	else
		$output = $floor;
		
	return $output/100;

}


?>