<?PHP
require_once('db_functions.php');

function parse_language_table(&$languageTbl, $root)
{
	$output = 0;
	foreach ($root->getElementsByTagName('languagepair') as $language)
	{
		$languageTbl[$output] = new lingo_languagePair;
		#print foobar
		
		$languageTbl[$output]->set_sourceLang( $language->getElementsByTagName('srclang')->item(0)->nodeValue );
		$languageTbl[$output]->set_targetLang( $language->getElementsByTagName('tgtlang')->item(0)->nodeValue );
		$languageTbl[$output]->set_newTextRate( $language->getElementsByTagName('newtextrate')->item(0)->nodeValue );
		$languageTbl[$output]->set_fuzzyTextRate( $language->getElementsByTagName('fuzzytextrate')->item(0)->nodeValue );
		$languageTbl[$output]->set_matchTextRate( $language->getElementsByTagName('matchtextrate')->item(0)->nodeValue );
		$languageTbl[$output]->set_transHourly( $language->getElementsByTagName('transhourly')->item(0)->nodeValue );
		$languageTbl[$output]->set_prHourly( $language->getElementsByTagName('prhourly')->item(0)->nodeValue );
		if ($language->getElementsByTagName('errors')->item(0)->nodeValue == '1')
			$languageTbl[$output]->set_error( TRUE );
		else
			$languageTbl[$output]->set_error( FALSE );
			
		$languageTbl[$output]->set_newText( $language->getElementsByTagName('newtext')->item(0)->nodeValue );
		$languageTbl[$output]->set_fuzzyText( $language->getElementsByTagName('fuzzytext')->item(0)->nodeValue );
		$languageTbl[$output]->set_matchText( $language->getElementsByTagName('matchtext')->item(0)->nodeValue );
		$languageTbl[$output]->set_dtpHourly( $language->getElementsByTagName('dtphourly')->item(0)->nodeValue );
		$languageTbl[$output]->set_engHourly( $language->getElementsByTagName('enghourly')->item(0)->nodeValue );
		
		$taskList = $language->getElementsByTagName('tasklist')->item(0);
		foreach ($taskList->getElementsByTagName('task') as $task)
		{
			$taskType = $task->getAttribute('type');
			switch ($taskType)
			{
				case 'splitTask':
					$tempTask = new splitTask;
					$tempTask->set_sellUnitType( $task->getElementsByTagName('sellunittype')->item(0)->nodeValue );
					$tempTask->set_sellUnits( $task->getElementsByTagName('sellunits')->item(0)->nodeValue );
					$tempTask->set_sellPerUnit( $task->getElementsByTagName('sellperunit')->item(0)->nodeValue );

					$utl = $task->getElementsByTagName('unittypelist')->item(0);
					foreach ($utl->getElementsByTagName('unittype') as $ut)
					{
						$tempTask->add_unitType($ut->getAttribute('name'), $ut->nodeValue );
					}
					break;
				case 'pmTask':
					$tempTask = new pmTask;
					$tempTask->set_pmPercent( $task->getElementsByTagName('pmpercent')->item(0)->nodeValue );
					break;
				case 'customTask':
					$tempTask = new customTask;
					$tempTask->set_xyz( $task->getElementsByTagName('xyz')->item(0)->nodeValue );
					break;
				default:
					$tempTask = new lingo_task;
			}
			$tempTask->set_name( $task->getElementsByTagName('name')->item(0)->nodeValue) ;
			$tempTask->set_costUnits( $task->getElementsByTagName('costunits')->item(0)->nodeValue );
			$tempTask->set_costUnitType( $task->getElementsByTagName('costunittype')->item(0)->nodeValue );
			$tempTask->set_costPerUnit( $task->getElementsByTagName('costperunit')->item(0)->nodeValue );
			$tempTask->set_markup( $task->getElementsByTagName('markuppercent')->item(0)->nodeValue );
			if ($task->getElementsByTagName('usescustomprice')->item(0)->nodeValue == '1')
				$tempTask->set_customPrice( $task->getElementsByTagName('customsellprice')->item(0)->nodeValue );
			else
				$tempTask->set_customPrice( 0 );
			
			if ($task->getElementsByTagName('unitslocked')->item(0)->nodeValue == '1')
				$tempTask->set_unitsLocked( TRUE );
			else
				$tempTask->set_unitsLocked( FALSE );
				
			$tempTask->set_isSplit( $task->getElementsByTagName('issplit')->item(0)->nodeValue );
			
			if ($task->getElementsByTagName('printable')->item(0)->nodeValue == '1')
				$tempTask->set_printable( TRUE );
			else
				$tempTask->set_printable( FALSE );
				
			$languageTbl[$output]->add_task($tempTask);
		}
		
		$rolled = $language->getElementsByTagName('rolledtasks')->item(0);
		foreach($rolled->getElementsByTagName('task') as $task)
		{
			$languageTbl[$output]->set_rolledUpTask($task->getAttribute('name'), $task->nodeValue);
		}

		
		
		$output++;
	}
	
	
	return $output;
}


function build_language_table(&$languageTbl)
{

	
	//get data from the Post Data
	if ($_POST['wordCountStyle'] == 'total')
	{
		$totalText = (isset($_POST['totalText'])) ? $_POST['totalText'] : 0;
		$percentLeverage = (isset($_POST['percentLeverage'])) ? $_POST['percentLeverage'] : 0;
		$newText = round($totalText * (1 - ($percentLeverage / 100)),0);
		$fuzzyText = 0;
		$matchText = $totalText - $newText;
	
	}
	else
	{
		$newText = (isset($_POST['new'])) ? $_POST['new'] : 0;
		$fuzzyText = (isset($_POST['fuzzy'])) ? $_POST['fuzzy'] : 0;
		$matchText = (isset($_POST['100'])) ? $_POST['100'] : 0;
	}
	
		$linguisticCostType = (isset($_POST['linguisticCostType'])) ? $_POST['linguisticCostType'] : "Words";
		$linguisticSellType = (isset($_POST['linguisticSellType'])) ? $_POST['linguisticSellType'] : "Words";
		
		$proofread = (isset($_POST['proofreading'])&&$_POST['proofreading']!="") ? $_POST['proofreading'] : 0;
		
		
					
		$rushFees = (isset($_POST['rushFees'])) ? $_POST['rushFees'] : 'undefined';			
		
		$numberPages = (isset($_POST['pageNumber'])) ? $_POST['pageNumber'] : 0;
		$fmt_pagePerHour = (isset($_POST['pageHour'])) ? $_POST['pageHour'] : 1 ;
		$dtp_coord_units = (isset($_POST['fmtCoord'])) ? $_POST['fmtCoord'] : 10;
		$dtp_coord_type = (isset($_POST['dtpCoordType'])) ? $_POST['dtpCoordType'] : 'Percent';
		$dtp_cost_units = $_POST['DTPCostunits'];
		$dtp_sell_units = $_POST['DTPSellunits'];
		$dtp_hours = $_POST['fmtHours'];
		$dtp_costPerPage = (isset($_POST['fmtCostPer'])) ? $_POST['fmtCostPer'] : 0;
		
		$engTM = (isset($_POST['engTM'])) ? $_POST['engTM'] : 0;
		$engineer = (isset($_POST['engineer'])) ? $_POST['engineer'] : 0;
		$engScap = (isset($_POST['engScap'])) ? $_POST['engScap'] : 0;
		$engScapHour = (isset($_POST['engScapHour'])) ? $_POST['engScapHour'] : 0;
		$engGraphNum = (isset($_POST['engGraphNum'])) ? $_POST['engGraphNum'] : 0;
		$engGraphHour = (isset($_POST['engGraphHour'])) ? $_POST['engGraphHour'] : 0;
		
		$qaPagesHour = (isset($_POST['qaPagesHour'])) ? $_POST['qaPagesHour'] : 0;
		$qaHours = (isset($_POST['qaHours'])) ? $_POST['qaHours'] : 0;
		$qa_coord_percent = (isset($_POST['qaCoord'])) ? $_POST['qaCoord'] : 10;
		
		$pmHours = (isset($_POST['pmHours'])) ? $_POST['pmHours'] : 0;
		$pmPercentage = (isset($_POST['pmPercentage'])) ? $_POST['pmPercentage'] : 0;
		
		$addTask1 = (isset($_POST['addTask1'])&&$_POST['addTask1']!="") ? $_POST['addTask1'] : 0;
		$addTask2 = (isset($_POST['addTask2'])&&$_POST['addTask2']!="") ? $_POST['addTask2'] : 0;
		$addTask3 = (isset($_POST['addTask3'])&&$_POST['addTask3']!="") ? $_POST['addTask3'] : 0;
			
		$numLanguages = (isset($_POST['langNumber'])) ? $_POST['langNumber'] :0;
		$targetLangs = (isset($_POST['targetL'])) ? $_POST['targetL'] : NULL;

		$clientName = (isset($_POST['cusName'])) ? $_POST['cusName'] : "default";
		$prospectName = (isset($_POST['prosName'])) ? $_POST['prosName'] : "default";
		
		if ($_POST['cusType'] == "cusProspect") //if this is a prospect, copy the prospect name into the client name
		{
			$clientName = $prospectName;
		}
		$sourceLang = (isset($_POST['sourceL'])) ? $_POST['sourceL'] : "English (US)";
		
		if (isset($_POST['lockunits']) && ($_POST['lockunits'] == 'lockunits'))
		{
			$unitsLocked = TRUE;
		}
		else
		{
			$unitsLocked = FALSE;
		}
		
		
		
		
		if ($numLanguages < 1)
		{
			return $numLanguages;
		}
		
		//get data from the database
		$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
		if ($myDBConn->connect_errno)
		{
			echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
			exit;
		}
		
		$query = "select * from internalefforts";
		$result = $myDBConn->query($query) or die($myDBConn->error);
		
		while ($res =  $result->fetch_assoc())
		{
			switch($res['Name'])
			{
				case 'Formatting': 
						$dtpHourly = $res['HourlyRate'];
						$dtpHourly = round(($dtpHourly/100),2);
						break;
				case 'Engineering':
						$engHourly = $res['HourlyRate'];
						$engHourly = round(($engHourly/100),2);
						break;
				case 'Project Management':
						$pmHourly = $res['HourlyRate'];
						$pmHourly = round(($pmHourly/100),2);
						break;
				case 'Quality Assurance':
						$qaHourly = $res['HourlyRate'];
						$qaHourly = round(($qaHourly/100),2);
						break;
			}
		}
		
		$result->free();
		
		$myDBConn->close();
		
		
		//create the language table
		for ($lcv=0; $lcv < $numLanguages; $lcv++)
		{
			$languageTbl[$lcv] = new lingo_languagePair();
			$languageTbl[$lcv]->set_sourceLang($sourceLang);
			if ($targetLangs[$lcv] == 'Other')
			{	//we have an unlisted language
				$languageTbl[$lcv]->set_targetLang($_POST['otherLangName']);
			}
			else
			{
				$languageTbl[$lcv]->set_targetLang($targetLangs[$lcv]);
			}
			$languageTbl[$lcv]->set_newText($newText);
			$languageTbl[$lcv]->set_fuzzyText($fuzzyText);
			$languageTbl[$lcv]->set_matchText($matchText);
			$languageTbl[$lcv]->set_dtpHourly($dtpHourly);
			$languageTbl[$lcv]->set_engHourly($engHourly);
				
			$currLang = $targetLangs[$lcv];
			
			$DB_ClientID = getClientID($clientName);
			$DB_SrcLangID = getSourceLangID($sourceLang);
			
			if ($currLang != 'Other')
			{
				$DB_TgtLangID = getTargetLangID($currLang);
			
			
				//get data from the database
				$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
				if ($myDBConn->connect_errno)
				{
					echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
					exit;
				}
				
				
				$query = "select * from linguisticcost where srcLang='".str_replace(" ","_",$sourceLang)."' AND targetLang='".str_replace(" ","_",$currLang)."'";

				$result = $myDBConn->query($query) or die($myDBConn->error);
				
				if ($result->num_rows == 0)
				{
					//there was no matching language data in the database, flag error
					$languageTbl[$lcv]->set_error(TRUE);
					$languageTbl[$lcv]->set_newTextRate(0.000);
					$languageTbl[$lcv]->set_fuzzyTextRate(0.000);
					$languageTbl[$lcv]->set_matchTextRate(0.000);
					$languageTbl[$lcv]->set_transHourly(0);
					$languageTbl[$lcv]->set_prHourly(0);
				}
				else
				{
					$res = $result->fetch_assoc();
					$languageTbl[$lcv]->set_error(FALSE);				
					$languageTbl[$lcv]->set_newTextRate(round(($res['newTextRate']/1000),3));
					$languageTbl[$lcv]->set_fuzzyTextRate(round(($res['fuzzyTextRate']/1000),3));
					$languageTbl[$lcv]->set_matchTextRate(round(($res['matchTextRate']/1000),3));
					$languageTbl[$lcv]->set_transHourly(round(($res['transHourly']/1000),3));
					$languageTbl[$lcv]->set_prHourly(round(($res['prHourly']/1000),3));
				}
				
				$result->free();
				$myDBConn->close();
			}
			else
			{
				$languageTbl[$lcv]->set_error(FALSE);
				$languageTbl[$lcv]->set_newTextRate($_POST['otherLangNewTextCost']);
				$languageTbl[$lcv]->set_fuzzyTextRate($_POST['otherLangFuzzyTextCost']);
				$languageTbl[$lcv]->set_matchTextRate($_POST['otherLangMatchTextCost']);
				$languageTbl[$lcv]->set_transHourly($_POST['otherLangTransHourly']);
				$languageTbl[$lcv]->set_prHourly($_POST['otherLangPRHourly']);
			}
				
			
		
			
			//create a new text task object						
			$tempTask = new splitTask();			
			$tempTask->set_name("New Text");
			//add units to the unit list
			$tempTask->add_unitType('Words', $languageTbl[$lcv]->get_newText());
			$units = $languageTbl[$lcv]->get_newText() / 250;
			$units += $languageTbl[$lcv]->get_newText() / 1000;
			$units = round( ($units*4))/4;
			$tempTask->add_unitType('Hours', $units);


			//check what units of measure to use for cost
			if ($linguisticCostType == "Words")
			{
				$tempTask->set_costUnits( $tempTask->get_unitType_at('Words') );
				$tempTask->set_costUnitType("Words");
				$tempTask->set_costPerUnit( $languageTbl[$lcv]->get_newTextRate() );
			}
			else
			{				
				$tempTask->set_costUnits($tempTask->get_unitType_at('Hours'));
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit( $languageTbl[$lcv]->get_transHourly() );
			}
			//check what units of measure to use for sell
			if ($linguisticSellType == "Words")
			{
				$tempTask->set_sellUnits($tempTask->get_unitType_at('Words'));
				$tempTask->set_sellUnitType("Words");
				$tempTask->set_sellPerUnit( $languageTbl[$lcv]->get_newTextRate() );
			}
			else
			{
				$tempTask->set_sellUnits($tempTask->get_unitType_at('Hours'));
				$tempTask->set_sellUnitType("Hours");
				$tempTask->set_sellPerUnit( $languageTbl[$lcv]->get_transHourly() );
			}
			if ($linguisticCostType != $linguisticSellType)
				$tempTask->set_isSplit(TRUE);
			else
				$tempTask->set_isSplit(FALSE);
			
			$tempTask->set_sellUnitType($linguisticSellType);
			$tempTask->set_markup(40);
			$tempTask->set_unitsLocked($unitsLocked);
			
			
			//check for custom sell price data
			if ($tempTask->isSplit() == 'false')
			{
				if ($tempTask->get_costUnitType() == 'Hours')
				{
					$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, "transHourly");
				}
				else
				{
					$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, "NEWTEXT");
				}					
			}
			else
			{
				if ($tempTask->get_sellUnitType() == 'Hours')
					$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, 'transHourly');
				else
					$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, "NEWTEXT");
			}
			$customPrice /= 1000;
			$tempTask->set_customPrice($customPrice);
			
			//add task to the languages task list
			$offset = $languageTbl[$lcv]->add_task($tempTask);
				
				
				
			//create a fuzzy text task object						
			$tempTask = new splitTask();			
			$tempTask->set_name("Fuzzy Text");
			$tempTask->add_unitType('Words', $languageTbl[$lcv]->get_fuzzyText() );
			$units = $languageTbl[$lcv]->get_fuzzyText() / 250;
			$units += $languageTbl[$lcv]->get_fuzzyText() / 1000;
			$units = round($units*4)/4;
			$tempTask->add_unitType('Hours', $units);
			//check what units of measure to use for cost
			if ($linguisticCostType == "Words")
			{
				$tempTask->set_costUnits( $tempTask->get_unitType_at('Words') );
				$tempTask->set_costUnitType("Words");
				$tempTask->set_costPerUnit( $languageTbl[$lcv]->get_fuzzyTextRate() );
			}
			else
			{
				$tempTask->set_costUnits( $tempTask->get_unitType_at('Hours') );
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit( $languageTbl[$lcv]->get_transHourly() );
			}	
						//check what units of measure to use for sell
			if ($linguisticSellType == "Words")
			{
				$tempTask->set_sellUnits($tempTask->get_unitType_at('Words'));
				$tempTask->set_sellUnitType("Words");
				$tempTask->set_sellPerUnit( $languageTbl[$lcv]->get_fuzzyTextRate() );
			}
			else
			{
				$tempTask->set_sellUnits( $tempTask->get_unitType_at('Hours') );
				$tempTask->set_sellUnitType("Hours");
				$tempTask->set_sellPerUnit( $languageTbl[$lcv]->get_transHourly() );
			}
			if ($linguisticCostType != $linguisticSellType)
				$tempTask->set_isSplit(TRUE);
			else
				$tempTask->set_isSplit(FALSE);
				
			$tempTask->set_sellUnitType($linguisticSellType);					
			$tempTask->set_markup(40);
			$tempTask->set_unitsLocked($unitsLocked);
			
			//check for custom sell price data
			if ($tempTask->isSplit() == 'false')
			{
				if ($tempTask->get_costUnitType() == 'Hours')
					$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, 'transHourly');
				else
					$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, FUZZY);
			}
			else
			{
				if ($tempTask->get_sellUnitType() == 'Hours')
					$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, 'transHourly');
				else
					$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, FUZZY);
			}
			$customPrice /= 1000;
			$tempTask->set_customPrice($customPrice);
			
			//add task to the languages task list
			$offset = $languageTbl[$lcv]->add_task($tempTask);
			
			
			//create a match text task object						
			$tempTask = new splitTask();			
			$tempTask->set_name("Repetitions/100% Matches");
			$tempTask->add_unitType('Words', $languageTbl[$lcv]->get_matchText() );
			$units = $languageTbl[$lcv]->get_matchText() / 250;
			$units += $languageTbl[$lcv]->get_matchText() / 1000;		
			$units = round($units*4)/4;
			$tempTask->add_unitType('Hours', $units);	
			
			//check what units of measure to use for cost
			if ($linguisticCostType == "Words")
			{
				$tempTask->set_costUnits($tempTask->get_unitType_at('Words'));
				$tempTask->set_costUnitType("Words");
				$tempTask->set_costPerUnit( $languageTbl[$lcv]->get_matchTextRate() );
			}
			else
			{
				$tempTask->set_costUnits($tempTask->get_unitType_at('Hours'));
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit( $languageTbl[$lcv]->get_transHourly() );
			}	
			if ($linguisticSellType == "Words")
			{
				$tempTask->set_sellUnits($tempTask->get_unitType_at('Words'));
				$tempTask->set_sellUnitType("Words");
				$tempTask->set_sellPerUnit( $languageTbl[$lcv]->get_matchTextRate() );
			}
			else
			{
				$tempTask->set_sellUnits($tempTask->get_unitType_at('Hours'));
				$tempTask->set_sellUnitType("Hours");
				$tempTask->set_sellPerUnit( $languageTbl[$lcv]->get_transHourly() );
			}
			if ($linguisticCostType != $linguisticSellType)
				$tempTask->set_isSplit(TRUE);
			else
				$tempTask->set_isSplit(FALSE);
				
			$tempTask->set_sellUnitType($linguisticSellType);					
			$tempTask->set_markup(40);
			$tempTask->set_unitsLocked($unitsLocked);
			
			
			//check for custom sell price data
			if ($tempTask->isSplit() == 'false')
			{
				if ($tempTask->get_costUnitType() == 'Hours')
					$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, 'transHourly');
				else
					$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, MATCHES);
			}
			else
			{
				if ($tempTask->get_sellUnitType() == 'Hours')
					$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, 'transHourly');
				else
					$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, MATCHES);
			}
			$customPrice /= 1000;
			$tempTask->set_customPrice($customPrice);
			
			//add task to the languages task list
			$offset = $languageTbl[$lcv]->add_task($tempTask);
			
			//check for 1 hour minimum for linguistic
			$hourMinimum = FALSE;
			if ( ($languageTbl[$lcv]->get_task(NEWTEXT)->get_costUnitType() == "Hours") &&
				 ($languageTbl[$lcv]->get_task(FUZZY)->get_costUnitType() == "Hours") &&
				 ($languageTbl[$lcv]->get_task(MATCHES)->get_costUnitType() == "Hours")) 
			{
				if ( ($languageTbl[$lcv]->get_task(NEWTEXT)->get_cost()+$languageTbl[$lcv]->get_task(FUZZY)->get_cost()+$languageTbl[$lcv]->get_task(MATCHES)->get_cost()) < $languageTbl[$lcv]->get_transHourly())
				{
					$languageTbl[$lcv]->get_task(NEWTEXT)->set_costUnits(1);
					$languageTbl[$lcv]->get_task(NEWTEXT)->set_CostUnitType("Hours");
					$languageTbl[$lcv]->get_task(NEWTEXT)->set_costPerUnit($languageTbl[$lcv]->get_transHourly());
					$languageTbl[$lcv]->get_task(FUZZY)->set_costUnits(0);
					$languageTbl[$lcv]->get_task(FUZZY)->set_CostUnitType("Hours");
					$languageTbl[$lcv]->get_task(FUZZY)->set_costPerUnit($languageTbl[$lcv]->get_transHourly());
					$languageTbl[$lcv]->get_task(MATCHES)->set_costUnits(0);
					$languageTbl[$lcv]->get_task(MATCHES)->set_CostUnitType("Hours");
					$languageTbl[$lcv]->get_task(MATCHES)->set_costPerUnit($languageTbl[$lcv]->get_transHourly());
				}
			}
			
			//create a proofread task object						
			$tempTask = new lingo_task();			
			$tempTask->set_name("Proofread");
			$tempTask->set_costUnits($proofread);
			$tempTask->set_costUnitType("Hours");
			$tempTask->set_costPerUnit($languageTbl[$lcv]->get_prHourly());
			$tempTask->set_markup(40);
			$tempTask->set_unitsLocked($unitsLocked);
			
			//check for custom sell price data
			$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, PROOF);
			$customPrice /= 1000;
			$tempTask->set_customPrice($customPrice);
			
			//add task to the languages task list
			$offset = $languageTbl[$lcv]->add_task($tempTask);
			
			
			//create a formatting task object						
			$tempTask = new splitTask();			
			$tempTask->set_name("Formatting");
			$tempTask->add_unitType('Hours', $dtp_hours);
			$tempTask->add_unitType('Pages', $numberPages);
			$tempTask->set_printable(FALSE);

			if ($dtp_cost_units == "hours")
			{
				$tempTask->set_costUnits( $tempTask->get_unitType_at('Hours') );
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($dtpHourly);
			}
			else
			{
				$tempTask->set_costUnits( $tempTask->get_unitType_at('Pages') );
				$tempTask->set_costUnitType("Pages");
				$tempTask->set_costPerUnit($dtp_costPerPage);
			}
			if ($dtp_sell_units == "hours")
			{
				$tempTask->set_sellUnits( $tempTask->get_unitType_at('Hours') );
				$tempTask->set_sellUnitType("Hours");
				$tempTask->set_sellPerUnit($dtpHourly);
			}
			else
			{
				$tempTask->set_sellUnits( $tempTask->get_unitType_at('Pages') );
				$tempTask->set_sellUnitType("Pages");
				$tempTask->set_sellPerUnit($dtp_costPerPage);
			}
			
			$tempTask->set_markup(50);
			$tempTask->set_unitsLocked($unitsLocked);
			
			if ($dtp_sell_units != $dtp_cost_units)
				$tempTask->set_isSplit(TRUE);
			else
				$tempTask->set_isSplit(FALSE);
			
		
			//check for custom sell price data
			$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, FORMAT);
			$customPrice /= 1000;
		
			$tempTask->set_customPrice($customPrice);
			if ($tempTask->usesCustomPrice() == TRUE)
			{
				$tempTask->set_sellUnitType("Hours");
				$tempTask->set_sellUnits( number_format(round(($numberPages / $fmt_pagePerHour)*4)/4,2) );
				$tempTask->set_sellPerUnit($customPrice);
			}
		/*	else
			{
				$tempTask->set_sellUnitType( $tempTask->get_costUnitType() );
				$tempTask->set_sellUnits( $tempTask->get_costUnits() );
				$tempTask->set_sellPerUnit( $tempTask->get_costPerUnit() );
			}*/
		
			//add task to the languages task list
			$offset = $languageTbl[$lcv]->add_task($tempTask);
			
			
			
			//create a Graphic Design task object						
			$tempTask = new lingo_task();			
			$tempTask->set_name("Graphic Design");
			$tempTask->set_printable(FALSE);
			$units = round(($engGraphNum / $engGraphHour)*4)/4;
			$tempTask->set_costUnits($units);
			$tempTask->set_costUnitType("Hours");
			$tempTask->set_costPerUnit($dtpHourly);
			$tempTask->set_markup(50);
			$tempTask->set_unitsLocked($unitsLocked);
			
			//check for custom sell price data
			$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, GRAPHICS);
			$customPrice /= 1000;
			$tempTask->set_customPrice($customPrice);
			
			//add task to the languages task list
			$offset = $languageTbl[$lcv]->add_task($tempTask);
			
			
			//create a dtp coord task object						
			$tempTask = new lingo_task();		
			$tempTask->set_printable(FALSE);	
			$tempTask->set_name("Formatting Coordination");
			$units = $languageTbl[$lcv]->get_task($offset-1)->get_costUnits(); //get the units from the formatting task
			$units = round($units * ($dtp_coord_units / 100) * 4) / 4;
			if (($units <=0 ) && ($languageTbl[$lcv]->get_task($offset-1)->get_costUnits() > 0))
				$units = 0.25;
			$tempTask->set_costUnits($units);
			$tempTask->set_costUnitType('Hours');
			$tempTask->set_costPerUnit($dtpHourly);
			$tempTask->set_markup(50);
			$tempTask->set_unitsLocked($unitsLocked);
			
			//check for custom sell price data
			$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, FORMAT);
			$customPrice /= 1000;
			$tempTask->set_customPrice($customPrice);
			
			//add task to the languages task list
			$offset = $languageTbl[$lcv]->add_task($tempTask);
			
			
			
			//create a TM work task object						
			$tempTask = new lingo_task();			
			$tempTask->set_name("TM Work");
			$tempTask->set_costUnits($engTM);
			$tempTask->set_costUnitType("Hours");
			$tempTask->set_costPerUnit($engHourly);
			$tempTask->set_markup(50);
			$tempTask->set_unitsLocked($unitsLocked);
			
			//check for custom sell price data
			$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, TMWORK);
			$customPrice /= 1000;
			$tempTask->set_customPrice($customPrice);
			
			//add task to the languages task list
			$offset = $languageTbl[$lcv]->add_task($tempTask);
			
			
			
			//create a Engineering task object						
			$tempTask = new lingo_task();			
			$tempTask->set_name("File Treatment");
			$tempTask->set_costUnits($engineer);
			$tempTask->set_costUnitType("Hours");
			$tempTask->set_costPerUnit($engHourly);
			$tempTask->set_markup(50);
			$tempTask->set_unitsLocked($unitsLocked);
			
			//check for custom sell price data
			$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, ENGINEERING);
			$customPrice /= 1000;
			$tempTask->set_customPrice($customPrice);
			
			//add task to the languages task list
			$offset = $languageTbl[$lcv]->add_task($tempTask);
			
			
			
			//create a Screen Capturing task object						
			$tempTask = new lingo_task();			
			$tempTask->set_name("Screen Capturing");
			$units = round(($engScap / $engScapHour)*4)/4;
			$tempTask->set_costUnits($units);
			$tempTask->set_costUnitType("Hours");
			$tempTask->set_costPerUnit($engHourly);
			$tempTask->set_markup(50);
			$tempTask->set_unitsLocked($unitsLocked);
			
			//check for custom sell price data
			$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, SCAPS);
			$customPrice /= 1000;
			$tempTask->set_customPrice($customPrice);
			
			//add task to the languages task list
			$offset = $languageTbl[$lcv]->add_task($tempTask);
			
			
			
			//create a QA task object						
			$tempTask = new lingo_task();			
			$tempTask->set_printable(FALSE);
			$tempTask->set_name("Quality Assurance");
			$units = $qaHours;
			$tempTask->set_costUnits($units);
			$tempTask->set_costUnitType("Hours");
			$tempTask->set_costPerUnit($qaHourly);
			$tempTask->set_markup(50);
			$tempTask->set_unitsLocked($unitsLocked);
			
			//check for custom sell price data
			$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, QA);
			$customPrice /= 1000;
			$tempTask->set_customPrice($customPrice);
			
			//add task to the languages task list
			$offset = $languageTbl[$lcv]->add_task($tempTask);
			
			
			
			
			//create a qa coord task object						
			$tempTask = new lingo_task();	
			$tempTask->set_printable(FALSE);		
			$tempTask->set_name("QA Coordination");
			$units = $languageTbl[$lcv]->get_task($offset)->get_costUnits();
			$units = round($units * ($qa_coord_percent / 100) * 4)/4;
			if (($units <=0 ) && ($languageTbl[$lcv]->get_task($offset)->get_costUnits() > 0))
				$units = 0.25;
			$tempTask->set_costUnits($units);
			$tempTask->set_costUnitType("Hours");
			$tempTask->set_costPerUnit($qaHourly);
			$tempTask->set_markup(50);
			$tempTask->set_unitsLocked($unitsLocked);
			
			//check for custom sell price data
			$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, QA);
			$customPrice /= 1000;
			$tempTask->set_customPrice($customPrice);
			
			//add task to the languages task list
			$offset = $languageTbl[$lcv]->add_task($tempTask);
			
			
			
			//create first undefined additional task object						
			$tempTask = new lingo_task();			
			$tempTask->set_name($_POST['addDesc1']);
			$tempTask->set_costUnits(1);
			$tempTask->set_costUnitType("Hours");
			$tempTask->set_costPerUnit($addTask1);
			$tempTask->set_markup(50);
			$tempTask->set_unitsLocked($unitsLocked);
			
			//add task to the languages task list
			$offset = $languageTbl[$lcv]->add_task($tempTask);
			
			//create second additional task object						
			$tempTask = new lingo_task();			
			$tempTask->set_name($_POST['addDesc2']);
			$tempTask->set_costUnits(1);
			$tempTask->set_costUnitType("Hours");
			$tempTask->set_costPerUnit($addTask2);
			$tempTask->set_markup(50);
			$tempTask->set_unitsLocked($unitsLocked);
			
			//add task to the languages task list
			$offset = $languageTbl[$lcv]->add_task($tempTask);
			
			//create third additional task object						
			$tempTask = new lingo_task();			
			$tempTask->set_name($_POST['addDesc3']);
			$tempTask->set_costUnits(1);
			$tempTask->set_costUnitType("Hours");
			$tempTask->set_costPerUnit($addTask3);
			$tempTask->set_markup(50);
			$tempTask->set_unitsLocked($unitsLocked);
			
			//add task to the languages task list
			$offset = $languageTbl[$lcv]->add_task($tempTask);
			
			
			
			//create a project management task object						
			$tempTask = new pmTask();			
			$tempTask->set_name("Project Management");
			//$tempTask->set_costUnits($pmHours); //can't figure PM hours yet since it's based on sell price
			$tempTask->add_unitType('Hours', 1);
			$tempTask->add_unitType('Percent', 10);
			
			$tempTask->set_costUnitType("Hours");
			$tempTask->set_costPerUnit($pmHourly);
			$tempTask->set_pmPercent($pmPercentage);
			$tempTask->set_markup(40);
			$tempTask->set_unitsLocked(FALSE);
			
			//set sell units/price
			$tempTask->set_sellUnitType('Percent');
			$tempTask->set_sellUnits($pmPercentage);
			
			//check for custom sell price data
			$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, PM);
			//$customPrice /= 1000;
			$tempTask->set_customPrice($customPrice);
			
			//add task to the languages task list
			$offset = $languageTbl[$lcv]->add_task($tempTask);
			
			
			//start checking for additional tasks
			if (isset($_POST['OnlineReview']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("Online Review");
				$tempTask->set_costUnits($_POST['OnlineReview']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_prHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			
			}
			
			if (isset($_POST['Glossary_Development']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("Glossary Development");
				$tempTask->set_costUnits($_POST['Glossary_Development']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_transHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			
			}
			
			if (isset($_POST['Review_Leveraged_Text']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("Review Leveraged Text");
				$tempTask->set_costUnits($_POST['Review_Leveraged_Text']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_transHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			
			}
			if (isset($_POST['EM_Cleanup']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("EM Cleanup");
				$tempTask->set_costUnits($_POST['EM_Cleanup']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_dtpHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			
			}
			if (isset($_POST['PDF_Creation']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("PDF Creation");
				$tempTask->set_costUnits($_POST['PDF_Creation']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_dtpHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			
			}
			if (isset($_POST['Voiceover_Talent']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("Voiceover Talent");
				$tempTask->set_costUnits($_POST['Voiceover_Talent']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_transHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			
			}
			if (isset($_POST['Voiceover_Recording']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("Voiceover Recording");
				$tempTask->set_costUnits($_POST['Voiceover_Recording']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_transHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			}
			if (isset($_POST['Voiceover_Editing/Mixing']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("Voiceover Editing/Mixing");
				$tempTask->set_costUnits($_POST['Voiceover_Editing/Mixing']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_transHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			}
			if (isset($_POST['Voiceover_Archiving']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("Voiceover Archiving");
				$tempTask->set_costUnits($_POST['Voiceover_Archiving']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_transHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			}
			if (isset($_POST['Voiceover_Shipping']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("Voiceover Shipping");
				$tempTask->set_costUnits($_POST['Voiceover_Shipping']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_transHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			}
			if (isset($_POST['Voiceover_Director']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("Voiceover Director");
				$tempTask->set_costUnits($_POST['Voiceover_Director']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_transHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			}
			if (isset($_POST['Senior_Engineering']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("Senior Engineering");
				$tempTask->set_costUnits($_POST['Senior_Engineering']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_engHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			}
			if (isset($_POST['UI_Engineering']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("UI Engineering");
				$tempTask->set_costUnits($_POST['UI_Engineering']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_engHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			}
			if (isset($_POST['Website_Engineering']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("Website Engineering");
				$tempTask->set_costUnits($_POST['Website_Engineering']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_engHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			}
			if (isset($_POST['Help_Engineering']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("Help Engineering");
				$tempTask->set_costUnits($_POST['Help_Engineering']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_engHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			}
			if (isset($_POST['Flash_Engineering']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("Flash Engineering");
				$tempTask->set_costUnits($_POST['Flash_Engineering']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_engHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			}
			if (isset($_POST['Troubleshooting']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("Troubleshooting");
				$tempTask->set_costUnits($_POST['Troubleshooting']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_engHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			}
			if (isset($_POST['Functional_QA']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("Functional Quality Assurance");
				$tempTask->set_costUnits($_POST['Functional_QA']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_engHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			}
			if (isset($_POST['CD/DVD_Burning']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("CD/DVD Burning");
				$tempTask->set_costUnits($_POST['CD/DVD_Burning']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_engHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			}
			if (isset($_POST['Test_Script_Development']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("Test Script Development");
				$tempTask->set_costUnits($_POST['Test_Script_Development']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_engHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			}
			if (isset($_POST['OLR_-_Lab']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("OLR - Lab");
				$tempTask->set_costUnits($_POST['OLR_-_Lab']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_engHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			}
			if (isset($_POST['Graphic_Editing']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("Graphic Editing");
				$tempTask->set_costUnits($_POST['Graphic_Editing']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_engHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			}
			if (isset($_POST['PDF_Engineering']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("PDF Engineering");
				$tempTask->set_costUnits($_POST['PDF_Engineering']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_engHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			}
			if (isset($_POST['PDF_Annotation']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("PDF Annotation");
				$tempTask->set_costUnits($_POST['PDF_Annotation']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_dtpHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			}
			if (isset($_POST['Internationalization_Consulting']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("Internationalization Consulting");
				$tempTask->set_costUnits($_POST['Internationalization_Consulting']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_engHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			}
			if (isset($_POST['CMS_Consulting']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("CMS Consulting");
				$tempTask->set_costUnits($_POST['CMS_Consulting']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_engHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			}
			if (isset($_POST['Testing_Services']))
			{
				$tempTask = new customTask();
				$tempTask->set_printable(TRUE);
				$tempTask->set_name("Testing Services");
				$tempTask->set_costUnits($_POST['Testing_Services']);
				$tempTask->set_costUnitType("Hours");
				$tempTask->set_costPerUnit($languageTbl[$lcv]->get_engHourly());
				$tempTask->set_markup(50);
				$tempTask->set_unitsLocked($unitsLocked);
				$offset = $languageTbl[$lcv]->add_task($tempTask);
			}
			
			//now we can set the PM cost units, since we have all tasks entered.
			$asp = $languageTbl[$lcv]->get_task(PM)->get_actualSellPrice($languageTbl[$lcv]);
			$cost = $asp - (0.42 * $asp);
			$units = $cost / $languageTbl[$lcv]->get_task(PM)->get_costPerUnit();
			$units = (round($units*4))/4;
			if ($units < 1)
				$units = 1;
			$languageTbl[$lcv]->get_task(PM)->set_costUnits($units);
			
			
		}
		
		return $numLanguages;
	
	
}

function parse_estimate(&$estimateObj, $root)
{
	$estimateObj->set_estimateDate( $root->getElementsByTagName('estdate')->item(0)->nodeValue );
	$estimateObj->set_clientName( $root->getElementsByTagName('clientname')->item(0)->nodeValue );
	$estimateObj->set_projectName( $root->getElementsByTagName('projectname')->item(0)->nodeValue );
	$estimateObj->set_fileType( $root->getElementsByTagName('filetype')->item(0)->nodeValue );
	$estimateObj->set_deliverable( $root->getElementsByTagName('deliverable')->item(0)->nodeValue );
	$estimateObj->set_deliveryDate( $root->getElementsByTagName('deliverydate')->item(0)->nodeValue );
	if ($root->getElementsByTagName('rushfee')->item(0)->nodeValue == '1')
	{
		$estimateObj->set_rushFee( TRUE );
		$estimateObj->set_rushFeeMultiplier( $root->getElementsByTagName('rushfeemultiplier')->item(0)->nodeValue );
	}
	else
	{
		$estimateObj->set_rushFee( FALSE );
		$estimateObj->set_rushFeeMultiplier(0);
	}
	$estimateObj->set_notes( $root->getElementsByTagName('notes')->item(0)->nodeValue );
	$estimateObj->set_pages( $root->getElementsByTagName('pages')->item(0)->nodeValue );
	$estimateObj->set_pagesPerHour( $root->getElementsByTagName('pagesperhour')->item(0)->nodeValue );
	$estimateObj->set_projectID( $root->getElementsByTagName('projectID')->item(0)->nodeValue );
	
	$reqServs = $root->getElementsByTagName('requestedservices')->item(0);
	foreach($reqServs->getElementsByTagName('service') as $service)
	{
		$estimateObj->add_service( $service->nodeValue );
	}
	
	$estimateObj->set_discountType( $root->getElementsByTagName('discounttype')->item(0)->nodeValue );
	$estimateObj->set_discountPercent( $root->getElementsByTagName('discountpercent')->item(0)->nodeValue );
	$estimateObj->set_discountAmount( $root->getElementsByTagName('discountamount')->item(0)->nodeValue );
	$estimateObj->set_dtpCoordPercent( $root->getElementsByTagName('dtpcoordpercent')->item(0)->nodeValue );
	$estimateObj->set_qaCoordPercent( $root->getElementsByTagName('qacoordpercent')->item(0)->nodeValue );
	
	
	$estimateObj->set_projDesc( $root->getElementsByTagName('projdesc')->item(0)->nodeValue );
	$estimateObj->set_billingTerms( $root->getElementsByTagName('billingterms')->item(0)->nodeValue );
	$estimateObj->set_billingCycle( $root->getElementsByTagName('billingcycle')->item(0)->nodeValue );
}

function build_estimate(&$estimateObj)
{
	//fill in the estimate object data
	$targetLangs = $_POST['targetL'];
	
	$estDate = (isset($_POST['estDate'])) ? $_POST['estDate'] : 'Jan 1, 1980';
	$estimateObj->set_estimateDate($estDate);
	
	$estimateObj->set_clientType($_POST['cusType']);
	$clientName = (isset($_POST['cusName'])) ? $_POST['cusName'] : 'undefined';
	$prospectName = (isset($_POST['prosName'])) ? $_POST['prosName'] : 'undefined';
	if ($_POST['cusType'] == 'cusClient')
		$estimateObj->set_clientName($clientName);
	else
		$estimateObj->set_clientName($prospectName);
	
	$projName = (isset($_POST['projName'])) ? $_POST['projName'] : 'undefined';
	$estimateObj->set_projectName($projName);
	
	$projType = (isset($_POST['projType'])) ? $_POST['projType'] : 'undefined';
	switch($projType)
	{
		case "ptWeb": $temp = "Website"; break;
		case "ptUI": $temp = "User Interface"; break;
		case "ptHelp": $temp = "Help System"; break;
		case "ptSAP": $temp = "SAP"; break;
		case "ptDoc": $temp = "Documentation"; break;
		case "ptAudio": $temp = "Audio"; break;
		case "ptXML": $temp = "XML"; break;
		case "ptOther": $temp = $_POST['ptOtherText']; break;
		default: $temp="undefined";
	}
	$estimateObj->set_projectType($temp);
	
	$fileType = (isset($_POST['fileType'])) ? $_POST['fileType'] : 'undefined';
	switch($fileType)
	{
		case "ftAcrobat": $temp="Acrobat"; break;
		case "ftCD": $temp="CorelDraw"; break;
		case "ftEmail": $temp="Email"; break;
		case "ftExcel": $temp="Excel"; break;
		case "ftFlash": $temp="Flash"; break;
		case "ftFM": $temp="FrameMaker"; break;
		case "ftFree": $temp="FreeHand"; break;
		case "ftHTML": $temp="HTML"; break;
		case "ftIllustrator": $temp="Illustrator"; break;
		case "ftInDesign": $temp="InDesign"; break;
		case "ftPM": $temp="PageMaker"; break;
		case "ftPages": $temp="Pages"; break;
		case "ftPDF": $temp="PDF"; break;
		case "ftPhotoshop": $temp="Photoshop"; break;
		case "ftPPT": $temp="PowerPoint"; break;
		case "ftPub": $temp="Publisher"; break;
		case "ftQuark": $temp="QuarkXPress"; break;
		case "ftRoboHelp": $temp="Robo Help"; break;
		case "ftTxt": $temp="Text"; break;
		case "ftTM": $temp="Translation Memory"; break;
		case "ftResource": $temp="UI Resource File"; break;
		case "ftWebworks": $temp="WebWorks"; break;
		case "ftWord": $temp="Word"; break;
		case "ftXML": $temp="XML"; break;
		case "ftOther": $temp="Other"; break;
		default: $temp="undefined";

	}
	$estimateObj->set_fileType($temp);
	
	$deliverable = (isset($_POST['deliverable'])) ? $_POST['deliverable'] : 'undefined';
	$estimateObj->set_deliverable($deliverable);
		
	$estDeliveryDate = (isset($_POST['estDeliveryDate'])) ? $_POST['estDeliveryDate'] : 'undefined';
	$estimateObj->set_deliveryDate($estDeliveryDate);
	
	$projDesc = (isset($_POST['projDesc'])) ? $_POST['projDesc'] : 'none';	
	$estimateObj->set_projDesc($_POST['projDesc']);
	
	$general_notes = (isset($_POST['general_notes'])) ? $_POST['general_notes'] : 'none';	
	$estimateObj->set_notes($general_notes);
		
	$numberPages = (isset($_POST['pageNumber'])) ? $_POST['pageNumber'] : 0;
	$estimateObj->set_pages($numberPages);
	
	$numberGraphics = (isset($_POST['engGraphNum'])) ? $_POST['engGraphNum'] : 0;
	$estimateObj->set_numberOfGraphics($numberGraphics);
	
	$numberScaps = (isset($_POST['engScap'])) ? $_POST['engScap'] : 0;
	$estimateObj->set_numberOfScaps($numberScaps);
		
	$fmt_pagePerHour = (isset($_POST['pageHour'])) ? $_POST['pageHour'] : 0;
	$estimateObj->set_pagesPerHour($fmt_pagePerHour);
	
	$projID = (isset($_POST['projectid'])) ? $_POST['projectid'] : 0;
	$estimateObj->set_projectID( $projID );
	
	if (isset($_POST['terms']))
	{
		if ($_POST['terms'] == "Other")
			$estimateObj->set_billingTerms( $_POST['termsOther'] );
		
		$estimateObj->set_billingTerms( $_POST['terms']);
	}
	else
		$estimateObj->set_billingTerms('unknown');
		
	if (isset($_POST['cycle']))
	{	
		if ($_POST['cycle'] == "Progress")
			$estimateObj->set_billingCycle( $_POST['cycleOther'] );
		
		$estimateObj->set_billingCycle( $_POST['cycle']);
	}
	else
		$estimateObj->set_billingCycle('unknown');
	
	
	if  (isset($_POST['discountAmount']))
	{
		$estimateObj->set_discountType($_POST['discountType']);
		switch ($_POST['discountType'])
		{
			case 'percent':
				$estimateObj->set_discountPercent($_POST['discountAmount']); break;
			case 'fixed':
				$estimateObj->set_discountAmount($_POST['discountAmount']); break;
		}
	}
	
	if (isset($_POST['requestedServices'])) 
	{
		foreach ($_POST['requestedServices'] as $service)
		{
			$estimateObj->add_service($service);
		}
	}
	
	
	if (isset($_POST['rushFees']))
	{
		switch($_POST['rushFees'])
		{
			case 'rf0': $estimateObj->set_rushFeeMultiplier(0);
						break;
			case 'rf25':	$estimateObj->set_rushFeeMultiplier(0.25);
							break;
			case 'rf50':	$estimateObj->set_rushFeeMultiplier(0.5);
							break;
			default:	$estimateObj->set_rushFeeMultiplier(0);
		}
	}
	
	if (isset($_POST['quoteType']))
		$estimateObj->set_quoteType($_POST['quoteType']);
	
	if (isset($_POST['sourceL']))
		$estimateObj->set_sourceLanguage($_POST['sourceL']);
		
	//process the target languages array
	if (count($_POST['targetL'] > 0))
	{
		foreach($_POST['targetL'] as $language)
		{
			$estimateObj->addTargetLang($language);
		}
	}
	
	if (isset($_POST['otherLangName']))
		$estimateObj->set_otherLangName($_POST['otherLangName']);
	if (isset($_POST['otherLangNewTextCost']))
		$estimateObj->set_otherLangNewTextCost($_POST['otherLangNewTextCost']);
	if (isset($_POST['otherLangFuzzyTextCost']))
		$estimateObj->set_otherLangFuzzyTextCost($_POST['otherLangFuzzyTextCost']);
	if (isset($_POST['otherLangMatchTextCost']))
		$estimateObj->set_otherLangMatchTextCost($_POST['otherLangMatchTextCost']);
	if (isset($_POST['otherLangTransHourly']))
		$estimateObj->set_otherLangTransHourlyCost($_POST['otherLangTransHourly']);
	if (isset($_POST['otherLangPRHourly']))
		$estimateObj->set_otherLangPRHourlyCost($_POST['otherLangPRHourly']);
	
		
}

function parse_contact(&$contact, $root)
{
	$contact->set_name( $root->getElementsByTagName('name')->item(0)->nodeValue );
	$contact->set_title( $root->getElementsByTagName('title')->item(0)->nodeValue );
	$contact->set_email( $root->getElementsByTagName('email')->item(0)->nodeValue );
	$contact->set_phone( $root->getElementsByTagName('phone')->item(0)->nodeValue );
}

function build_contact(&$contact, PricingMySql $connection, $ID)
{
				
	$query = "select name,email,phone,title from lingocontacts where id = '". $ID ."'";
	$result = $connection->query($query);
		
	if ($result->num_rows >= 1)
	{
		$res =  $result->fetch_assoc();
		$contact->set_name($res['name']);
		$contact->set_title($res['title']);
		$contact->set_phone($res['phone']);
		$contact->set_email($res['email']);
	}
	
	$result->free();
}
