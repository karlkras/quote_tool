<?PHP

function getClientID($clientName)
{
	$ID = 0;
	
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	
	$query = "select ID from clients where Name = '$clientName'";
	$result = $myDBConn->query($query) or die($myDBConn->error);
	
	if ($result >= 1)
	{
		$res =  $result->fetch_assoc();
		$ID = $res['ID'];
	}
	
	$result->free();
	$myDBConn->close();
	return $ID;
}

function getSourceLangID($sourceLang)
{
	$ID = 0;
	
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	
	$query = "select ID from sourcelang where Language = '$sourceLang'";
	$result = $myDBConn->query($query) or die($myDBConn->error);
		
	if ($result->num_rows >= 1)
	{
		$res =  $result->fetch_assoc();
		$ID = $res['ID'];
	}
	
	$result->free();
	$myDBConn->close();
	return $ID;
}

function getTargetLangID($sourceLang)
{
	$ID = 0;
	
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	
	$query = "select ID from targetlang where Language = '$sourceLang'";
	$result = $myDBConn->query($query) or die($myDBConn->error);
		
	if ($result >= 1)
	{
		$res =  $result->fetch_assoc();
		$ID = $res['ID'];
	}
	
	$result->free();
	$myDBConn->close();
	return $ID;
}


function doBallpark(&$languageTbl)
{
	
	//get data from the Post Data
	if ($_POST['wordCountStyle'] == 'total')
	{
		$totalText = (isset($_POST['totalText'])) ? $_POST['totalText'] : 0;
		$percentLeverage = (isset($_POST['percentLeverage'])) ? $_POST['percentLeverage'] : 0;
		$newText = $totalText * (1 - ($percentLeverage / 100));
		$fuzzyText = 0;
		$matchText = $totalText - $newText;
	
	}
	else
	{
		$newText = (isset($_POST['new'])) ? $_POST['new'] : 0;
		$fuzzyText = (isset($_POST['fuzzy'])) ? $_POST['fuzzy'] : 0;
		$matchText = (isset($_POST['100'])) ? $_POST['100'] : 0;
	}
			
		
		
		$numberPages = (isset($_POST['pageNumber'])) ? $_POST['pageNumber'] : 0;
		$fmt_pagePerHour = (isset($_POST['pageHour'])) ? $_POST['pageHour'] : 0;
		
		$engTM = (isset($_POST['engTM'])) ? $_POST['engTM'] : 0;
		$engineer = (isset($_POST['engineer'])) ? $_POST['engineer'] : 0;
		$engScap = (isset($_POST['engScap'])) ? $_POST['engScap'] : 0;
		$engScapHour = (isset($_POST['engScapHour'])) ? $_POST['engScapHour'] : 0;
		$engGraphNum = (isset($_POST['engGraphNum'])) ? $_POST['engGraphNum'] : 0;
		$engGraphHour = (isset($_POST['engGraphHour'])) ? $_POST['engGraphHour'] : 0;
		
		$qaPagesHour = (isset($_POST['qaPagesHour'])) ? $_POST['qaPagesHour'] : 0;
		
		$pmHours = (isset($_POST['pmHours'])) ? $_POST['pmHours'] : 0;
		$pmPercentage = (isset($_POST['pmPercentage'])) ? $_POST['pmPercentage'] : 0;
		
		$addTask1 = (isset($_POST['addTask1'])) ? $_POST['addTask1'] : 0;
		$addTask2 = (isset($_POST['addTask2'])) ? $_POST['addTask2'] : 0;
		$addTask3 = (isset($_POST['addTask3'])) ? $_POST['addTask3'] : 0;
		
		$numLanguages = (isset($_POST['langNumber'])) ? $_POST['langNumber'] :0;
		$targetLangs = (isset($_POST['targetL'])) ? $_POST['targetL'] : NULL;

		$clientName = (isset($_POST['cusName'])) ? $_POST['cusName'] : "default";
		$prospectName = (isset($_POST['prosName'])) ? $_POST['prosName'] : "default";
		
		if (cusType == "cusProspect") //if this is a prospect, copy the prospect name into the client name
		{
			$clientName = $prospectName;
		}
		$sourceLang = (isset($_POST['sourceL'])) ? $_POST['sourceL'] : "English (US)";
		
		
		if (isset($_POST['rushFees']))
		{
			switch($_POST['rushFees'])
			{
				case 'rf0': $rushFee = 0;
							break;
				case 'rf25':	$rushFee = 1.25;
								break;
				case 'rf50':	$rushFee = 1.5;
								break;
				default:	$rushFee = 0;
			}
		}
		
		
		
		
		if ($numLanguages < 1)
		{
			return $numLanguages;
		}
		
		//create the language table
		for ($lcv=0; $lcv < $numLanguages; $lcv++)
		{
		
			$currLang = $targetLangs[$lcv];
			
			$DB_ClientID = getClientID($clientName);
			$DB_SrcLangID = getSourceLangID($sourceLang);
			$DB_TgtLangID = getTargetLangID($currLang);
		
		
			//get data from the database
			$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
			if ($myDBConn->connect_errno)
			{
				echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
				exit;
			}
			
			$query = "select * from pricing where ClientID='$DB_ClientID' AND SrcLang='$DB_SrcLangID' AND TgtLang='$DB_TgtLangID'";
			$result = $myDBConn->query($query) or die($myDBConn->error);
			
			
			if ($result->num_rows == 0)
			{
				//there was no match for the query, so get the default data for that language
				$result->free();
				$query = "select * from pricing where ClientID=0 AND SrcLang=0 AND TgtLang='$DB_TgtLangID'";
				$result = $myDBConn->query($query) or die($myDBConn->error);
				
				if($result->num_rows == 0)
				{
					//if we're here then there is no default data for that language, so just use French
					$result->free();
					$query = "select * from pricing where ClientID=0 AND SrcLang=0 AND TgtLang=0";
					$result = $myDBConn->query($query) or die($myDBConn->error);
					
				}
			}
			$res =  $result->fetch_assoc();
			
			$perWordRate = $res['perWordRate'];
			$perWordRate = round(($perWordRate/100),2);
			$dtpHourly = $res['dtpHourly'];
			$dtpHourly = round(($dtpHourly/100),2);
			$engHourly = $res['EngHourly'];
			$engHourly = round(($engHourly/100),2);
			$defaultMarkup = $res['markup'];
			$pmHourly = $res['PM_hourly'];
			$pmHourly = round(($pmHourly/100),2);
			$qaHourly = $res['QA_hourly'];
			$qaHourly = round(($qaHourly/100),2);
			
			$result->free();
			$myDBConn->close();
			
			$sumSP = 0;
			$sumCalcSell = 0;
			$sumActSell = 0;
			$sumCost = 0;
			
			$langNewTextSP[$lcv] = 0;
			$langFuzzySP[$lcv] = 0;
			$langMatchSP[$lcv] = 0;
			$langFormatSP[$lcv] = 0;
			$langTMWorkSP[$lcv] = 0;
			$langEngineerSP[$lcv] = 0;
			$langFlashSP[$lcv] = 0;
			$langScapsSP[$lcv] = 0;
			$langGraphicsSP[$lcv] = 0;
			$langQASP[$lcv] = 0;
			$langOther1SP[$lcv] = 0;
			$langOther2SP[$lcv] = 0;
			$langOther3SP[$lcv] = 0;
			$langPMSP[$lcv] = 0;
			$langActSell[$lcv] = 0;
			
			$langNewTextCost[$lcv] = 0;
			$langFuzzyCost[$lcv] = 0;
			$langMatchCost[$lcv] = 0;
			$langFormatCost[$lcv] = 0;
			$langTMWorkCost[$lcv] = 0;
			$langEngineerCost[$lcv] = 0;
			$langFlashCost[$lcv] = 0;
			$langScapsCost[$lcv] = 0;
			$langGraphicsCost[$lcv] = 0;
			$langQACost[$lcv] = 0;
			$langOther1Cost[$lcv] = 0;
			$langOther2Cost[$lcv] = 0;
			$langOther3Cost[$lcv] = 0;
			$langPMCost[$lcv] = 0;
			$langCost[$lcv] = 0;
	
			//determine calculated fields for new text
			$cost = $newText * $perWordRate;
			$langNewTextCost[$lcv] = $cost;
			$sumCost += $cost;
			$calcSellPrice = ($cost/(1-($defaultMarkup/100)))/$newText;
			$sumCalcSell += $calcSellPrice;
			$sellPricePer = round($calcSellPrice,2);
			$actualSellPrice = ($sellPricePer * $newText);
			$sumActSell += $actualSellPrice;
			$langNewTextSP[$lcv] = $actualSellPrice;
			
			$languageTbl[$lcv][NEWTEXT][NUM_UNITS] = $newText;
			$languageTbl[$lcv][NEWTEXT][COST_PER] = $perWordRate;
			$languageTbl[$lcv][NEWTEXT][COST] = $cost;
			$languageTbl[$lcv][NEWTEXT][MARKUP] = $defaultMarkup;
			$languageTbl[$lcv][NEWTEXT][CALC_SELL] = $calcSellPrice;
			$languageTbl[$lcv][NEWTEXT][SELL_PER] = $sellPricePer;
			$languageTbl[$lcv][NEWTEXT][ACT_SELL] = round($actualSellPrice,2);
			$languageTbl[$lcv][NEWTEXT][GM] = round(($actualSellPrice - $cost)/$actualSellPrice,2)*100;
			
			
			//determine calculated fields for fuzzy text
			$cost = $fuzzyText * $perWordRate;
			$langFuzzyCost[$lcv] = $cost;
			$sumCost += $cost;
			$calcSellPrice = ($cost/(1-($defaultMarkup/100)))/$fuzzyText;
			$sumCalcSell += $calcSellPrice;
			$sellPricePer = round($calcSellPrice,2);
			$actualSellPrice = ($sellPricePer * $fuzzyText);
			$sumActSell += $actualSellPrice;
			$langFuzzySP[$lcv] = $actualSellPrice;
			
			$languageTbl[$lcv][FUZZY][NUM_UNITS] = $fuzzyText;
			$languageTbl[$lcv][FUZZY][COST_PER] = $perWordRate;
			$languageTbl[$lcv][FUZZY][COST] = $cost;
			$languageTbl[$lcv][FUZZY][MARKUP] = $defaultMarkup;
			$languageTbl[$lcv][FUZZY][CALC_SELL] = $calcSellPrice;
			$languageTbl[$lcv][FUZZY][SELL_PER] = $sellPricePer;
			$languageTbl[$lcv][FUZZY][ACT_SELL] = round($actualSellPrice,2);
			$languageTbl[$lcv][FUZZY][GM] = round(($actualSellPrice - $cost)/$actualSellPrice,2)*100;
			
			
			//determine calculated fields for 100% text
			$cost = $matchText * $perWordRate;
			$langMatchCost[$lcv] = $cost;
			$sumCost += $cost;
			$calcSellPrice = ($cost/(1-($defaultMarkup/100)))/$matchText;
			$sumCalcSell += $calcSellPrice;
			$sellPricePer = round($calcSellPrice,2);
			$actualSellPrice = ($sellPricePer * $matchText);
			$sumActSell += $actualSellPrice;
			$langMatchSP[$lcv] = $actualSellPrice;
			
			$languageTbl[$lcv][MATCHES][NUM_UNITS] = $matchText;
			$languageTbl[$lcv][MATCHES][COST_PER] = $perWordRate;
			$languageTbl[$lcv][MATCHES][COST] = $cost;
			$languageTbl[$lcv][MATCHES][MARKUP] = $defaultMarkup;
			$languageTbl[$lcv][MATCHES][CALC_SELL] = $calcSellPrice;
			$languageTbl[$lcv][MATCHES][SELL_PER] = $sellPricePer;
			$languageTbl[$lcv][MATCHES][ACT_SELL] = round($actualSellPrice,2);
			$languageTbl[$lcv][MATCHES][GM] = round(($actualSellPrice - $cost)/$actualSellPrice,2)*100;
			
			
			//determine calculated fields for dtp
			$units = number_format(round(($numberPages / $fmt_pagePerHour)*4)/4,2);
			$cost = $units * $dtpHourly;
			$langFormatCost[$lcv] = $cost;
			$sumCost += $cost;
			$calcSellPrice = ($cost/(1-($defaultMarkup/100)))/$units;
			$sumCalcSell += $calcSellPrice;
			$sellPricePer = round($calcSellPrice,2);
			$actualSellPrice = ($sellPricePer * $units);
			$sumActSell += $actualSellPrice;
			$langFormatSP[$lcv] = $actualSellPrice;
			
			$languageTbl[$lcv][FORMAT][NUM_UNITS] = $units;
			$languageTbl[$lcv][FORMAT][COST_PER] = $dtpHourly;
			$languageTbl[$lcv][FORMAT][COST] = $cost;
			$languageTbl[$lcv][FORMAT][MARKUP] = $defaultMarkup;
			$languageTbl[$lcv][FORMAT][CALC_SELL] = $calcSellPrice;
			$languageTbl[$lcv][FORMAT][SELL_PER] = $sellPricePer;
			$languageTbl[$lcv][FORMAT][ACT_SELL] = round($actualSellPrice,2);
			$languageTbl[$lcv][FORMAT][GM] = round(($actualSellPrice - $cost)/$actualSellPrice,2)*100;
			
			
			//determine calculated fields for tm work
			$cost = $engTM * $engHourly;
			$langTMWorkCost[$lcv] = $cost;
			$sumCost += $cost;
			$calcSellPrice = ($cost/(1-($defaultMarkup/100)))/$engTM;
			$sumCalcSell += $calcSellPrice;
			$sellPricePer = round($calcSellPrice,2);
			$actualSellPrice = ($sellPricePer * $engTM);
			$sumActSell += $actualSellPrice;
			$langTMWorkSP[$lcv] = $actualSellPrice;
			
			$languageTbl[$lcv][TMWORK][NUM_UNITS] = $engTM;
			$languageTbl[$lcv][TMWORK][COST_PER] = $engHourly;
			$languageTbl[$lcv][TMWORK][COST] = $cost;
			$languageTbl[$lcv][TMWORK][MARKUP] = $defaultMarkup;
			$languageTbl[$lcv][TMWORK][CALC_SELL] = $calcSellPrice;
			$languageTbl[$lcv][TMWORK][SELL_PER] = $sellPricePer;
			$languageTbl[$lcv][TMWORK][ACT_SELL] = round($actualSellPrice,2);
			$languageTbl[$lcv][TMWORK][GM] = round(($actualSellPrice - $cost)/$actualSellPrice,2)*100;
			
			
			//determine calculated fields for engineernig
			$cost = $engineer * $engHourly;
			$langEngineerCost[$lcv] = $cost;
			$sumCost += $cost;
			$calcSellPrice = ($cost/(1-($defaultMarkup/100)))/$engineer;
			$sumCalcSell += $calcSellPrice;
			$sellPricePer = round($calcSellPrice,2);
			$actualSellPrice = ($sellPricePer * $engineer);
			$sumActSell += $actualSellPrice;
			$langEngineerSP[$lcv] = $actualSellPrice;
			
			$languageTbl[$lcv][ENGINEERING][NUM_UNITS] = $engTM;
			$languageTbl[$lcv][ENGINEERING][COST_PER] = $engHourly;
			$languageTbl[$lcv][ENGINEERING][COST] = $cost;
			$languageTbl[$lcv][ENGINEERING][MARKUP] = $defaultMarkup;
			$languageTbl[$lcv][ENGINEERING][CALC_SELL] = $calcSellPrice;
			$languageTbl[$lcv][ENGINEERING][SELL_PER] = $sellPricePer;
			$languageTbl[$lcv][ENGINEERING][ACT_SELL] = round($actualSellPrice,2);
			$languageTbl[$lcv][ENGINEERING][GM] = round(($actualSellPrice - $cost)/$actualSellPrice,2)*100;
			
			
			//determine calculated fields for scaps
			$units = number_format(round(($engScap / $engScapHour)*4)/4,2);
			
			$cost = $units * $engHourly;
			$langScapsCost[$lcv] = $cost;
			$sumCost += $cost;
			$calcSellPrice = ($cost/(1-($defaultMarkup/100)))/$units;
			$sumCalcSell += $calcSellPrice;
			$sellPricePer = round($calcSellPrice,2);
			$actualSellPrice = ($sellPricePer * $units);
			$sumActSell += $actualSellPrice;
			$langScapsSP[$lcv] = $actualSellPrice;
			
			$languageTbl[$lcv][SCAPS][NUM_UNITS] = $units;
			$languageTbl[$lcv][SCAPS][COST_PER] = $engHourly;
			$languageTbl[$lcv][SCAPS][COST] = $cost;
			$languageTbl[$lcv][SCAPS][MARKUP] = $defaultMarkup;
			$languageTbl[$lcv][SCAPS][CALC_SELL] = $calcSellPrice;
			$languageTbl[$lcv][SCAPS][SELL_PER] = $sellPricePer;
			$languageTbl[$lcv][SCAPS][ACT_SELL] = round($actualSellPrice,2);
			$languageTbl[$lcv][SCAPS][GM] = round(($actualSellPrice - $cost)/$actualSellPrice,2)*100;
			
			
			//determine calculated fields for graphics
			$units = number_format(round(($engGraphNum / $engGraphHour)*4)/4,2);
			$cost = $units * $engHourly;
			$langGraphicsCost[$lcv] = $cost;
			$sumCost += $cost;
			$calcSellPrice = ($cost/(1-($defaultMarkup/100)))/$units;
			$sumCalcSell += $calcSellPrice;
			$sellPricePer = round($calcSellPrice,2);
			$actualSellPrice = ($sellPricePer * $units);
			$sumActSell += $actualSellPrice;
			$langGraphicsSP[$lcv] = $actualSellPrice;
			
			$languageTbl[$lcv][GRAPHICS][NUM_UNITS] = $units;
			$languageTbl[$lcv][GRAPHICS][COST_PER] = $engHourly;
			$languageTbl[$lcv][GRAPHICS][COST] = $cost;
			$languageTbl[$lcv][GRAPHICS][MARKUP] = $defaultMarkup;
			$languageTbl[$lcv][GRAPHICS][CALC_SELL] = $calcSellPrice;
			$languageTbl[$lcv][GRAPHICS][SELL_PER] = $sellPricePer;
			$languageTbl[$lcv][GRAPHICS][ACT_SELL] = round($actualSellPrice,2);
			$languageTbl[$lcv][GRAPHICS][GM] = round(($actualSellPrice - $cost)/$actualSellPrice,2)*100;
			
			
			//determine calculated fields qa
			$units = number_format(round(($numberPages / $qaPagesHour)*4)/4,2);
			$cost = $units * $qaHourly;   
			$langQACost[$lcv] = $cost;
			$sumCost += $cost;
			$calcSellPrice = ($cost/(1-($defaultMarkup/100)))/$units;
			$sumCalcSell += $calcSellPrice;
			$sellPricePer = round($calcSellPrice,2);
			$actualSellPrice = ($sellPricePer * $units);
			$sumActSell += $actualSellPrice;
			$langQASP[$lcv] = $actualSellPrice;
			
			$languageTbl[$lcv][QA][NUM_UNITS] = $units;
			$languageTbl[$lcv][QA][COST_PER] = $qaHourly;
			$languageTbl[$lcv][QA][COST] = $cost;
			$languageTbl[$lcv][QA][MARKUP] = $defaultMarkup;
			$languageTbl[$lcv][QA][CALC_SELL] = $calcSellPrice;
			$languageTbl[$lcv][QA][SELL_PER] = $sellPricePer;
			$languageTbl[$lcv][QA][ACT_SELL] = round($actualSellPrice,2);
			$languageTbl[$lcv][QA][GM] = round(($actualSellPrice - $cost)/$actualSellPrice,2)*100;
			
			
			//determine calculated fields for additional tasks
			$calcSellPrice = $addTask1/(1-($defaultMarkup/100));
			
			$sellPricePer = round($calcSellPrice,2);
			$sumCalcSell += $calcSellPrice;
			$actualSellPrice = ($sellPricePer * 1);
			$sumCost += $addTask1;
			$sumActSell += $actualSellPrice;
			$langOther1SP[$lcv] = $actualSellPrice;
			$langOther1Cost[$lcv] = $addTask1;
			
			$languageTbl[$lcv][ADD1][NUM_UNITS] = 1;
			$languageTbl[$lcv][ADD1][COST_PER] = 0;
			$languageTbl[$lcv][ADD1][COST] = $addTask1;
			$languageTbl[$lcv][ADD1][MARKUP] = $defaultMarkup;
			$languageTbl[$lcv][ADD1][CALC_SELL] = $calcSellPrice;
			$languageTbl[$lcv][ADD1][SELL_PER] = $sellPricePer;
			$languageTbl[$lcv][ADD1][ACT_SELL] = round($actualSellPrice,2);
			$languageTbl[$lcv][ADD1][GM] = round(($actualSellPrice - $addTask1)/$actualSellPrice,2)*100;
			
			
			//determine calculated fields for additional tasks
			$calcSellPrice = $addTask2/(1-($defaultMarkup/100));
			$sumCalcSell += $calcSellPrice;
			$sellPricePer = round($calcSellPrice,2);
			$actualSellPrice = ($sellPricePer * 1);
			$sumCost += $addTask2;
			$sumActSell += $actualSellPrice;
			$langOther2SP[$lcv] = $actualSellPrice;
			$langOther2Cost[$lcv] = $addTask2;
			
			$languageTbl[$lcv][ADD2][NUM_UNITS] = 1;
			$languageTbl[$lcv][ADD2][COST_PER] = 0;
			$languageTbl[$lcv][ADD2][COST] = $addTask2;
			$languageTbl[$lcv][ADD2][MARKUP] = $defaultMarkup;
			$languageTbl[$lcv][ADD2][CALC_SELL] = $calcSellPrice;
			$languageTbl[$lcv][ADD2][SELL_PER] = $sellPricePer;
			$languageTbl[$lcv][ADD2][ACT_SELL] = round($actualSellPrice,2);
			$languageTbl[$lcv][ADD2][GM] = round(($actualSellPrice - $addTask2)/$actualSellPrice,2)*100;
			
		
			//determine calculated fields for additional tasks
			$calcSellPrice = $addTask3 /(1-($defaultMarkup/100));
			$sumCalcSell += $calcSellPrice;
			$sellPricePer = round($calcSellPrice,2);
			$actualSellPrice = ($sellPricePer * 1);
			$sumCost += $addTask3;
			$sumActSell += $actualSellPrice;
			$langOther3SP[$lcv] = $actualSellPrice;
			$langOther3Cost[$lcv] = $addTask3;
			
			$languageTbl[$lcv][ADD3][NUM_UNITS] = 1;
			$languageTbl[$lcv][ADD3][COST_PER] = 0;
			$languageTbl[$lcv][ADD3][COST] = $addTask3;
			$languageTbl[$lcv][ADD3][MARKUP] = $defaultMarkup;
			$languageTbl[$lcv][ADD3][CALC_SELL] = $calcSellPrice;
			$languageTbl[$lcv][ADD3][SELL_PER] = $sellPricePer;
			$languageTbl[$lcv][ADD3][ACT_SELL] = round($actualSellPrice,2);
			$languageTbl[$lcv][ADD3][GM] = round(($actualSellPrice - $addTask3)/$actualSellPrice,2)*100;
			
			
			
			//determine calculated fields for pm
			$cost = $pmHours * $pmHourly; 
			$langPMCost[$lcv] = $cost;
			$calcSellPrice = $sumCalcSell * ($pmPercentage/100);
			$sellPricePer = round($calcSellPrice,2);
			$actualSellPrice = round(($sumActSell / (1-($pmPercentage/100))) * ($pmPercentage/100),2);
			$langPMSP[$lcv] = $actualSellPrice;
			
			$languageTbl[$lcv][PM][NUM_UNITS] = $pmHours;
			$languageTbl[$lcv][PM][COST_PER] = $pmHourly;
			$languageTbl[$lcv][PM][COST] = $cost;
			$languageTbl[$lcv][PM][MARKUP] = $pmPercentage;
			$languageTbl[$lcv][PM][CALC_SELL] = $calcSellPrice;
			$languageTbl[$lcv][PM][SELL_PER] = $sellPricePer;
			$languageTbl[$lcv][PM][ACT_SELL] = round($actualSellPrice,2);
			$languageTbl[$lcv][PM][GM] = round(($actualSellPrice - $cost)/$actualSellPrice,2)*100;
			
			
			$langActSell[$lcv] = $sumActSell;
			$langCost[$lcv] = $sumCost;
		
		}
		
		return $numLanguages;
	
	
}

?>