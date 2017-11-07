<?PHP
ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 1);
include_once('../attaskconn/LingoAtTaskService.php');
include_once('pricing/words.php');

//error_reporting(0);

function save_to_xml(&$taskService, $projectObj, $sellRates, $passTrados, $bundleInternal)
{



    $client_id = $projectObj->company->id;

    $projectTotalCost = 0;
	$dtpData = unserialize($_SESSION['dtpData']);
	
	if (count($taskService->lingTasks) < 2)
		$sourceLanguage = $taskService->lingTasks->sourceLang;
	else
		$sourceLanguage = $taskService->lingTasks[0]->sourceLang;
	
	//calculate the total project price
	$totPrice = 0;
	$pmPrice = 0;
	if (count($taskService->lingTasks) < 2)
		$totPrice = $taskService->lingTasks->ltask->price;
	else
	{
		foreach($taskService->lingTasks as $lt)
		{

            if ($lt->ltask->type == "PR" && $_SESSION['proofReading'] == "yes") {
                $totPrice += 0;
            } else {
                $totPrice += $lt->ltask->price;
            }

		}
	}
	if (count($taskService->billableTasks) < 2)
		$totPrice += $taskService->billableTasks->btask->price;
	else
	{
		foreach($taskService->billableTasks as $bt)
		{
            if ($bt->btask->type == "PR" && $_SESSION['proofReading'] == "yes"){
                $totPrice += 0;
            } else {
                $totPrice += $bt->btask->price;
            }
		}
	}
	
	$rushFee = $taskService->rushFee;

	
	
	//first thing, save the taskService back to the @task project 
	try{
		set_time_limit(60);
		$api = new LingoAtTaskService();
		$u = new updateProjectPricing();
		$u->projectObject = $projectObj;
		$u->tskService = $taskService;
		$api->updateProjectPricing($u);
	}
	catch(exception $e)
	{
		echo "<br>Error updating @task Project. <strong>$e->faultstring</strong><br>";
	
		echo "<hr>Debug Data: ",$e->detail->ProcessFault->message, "<br>";
		echo "Error:<pre>\n";
		var_dump($e);
		echo "</pre>";
		exit;
	}




	//create a DOM document to represent our XML data
	$doc = new DOMDocument();
	$doc->formatOutput = true;
	
	
	//create root element
	$root = $doc->createElement("form1");
	$doc->appendChild($root);
	
	
	//create root / header
	$header = $doc->createElement("Header");
	$root->appendChild($header);
	
	//create root / header / Table1
	$Table1 = $doc->createElement("Table1");
	$header->appendChild($Table1);
	
	//create root / header / Table1 / Row1
	$Row1 = $doc->createElement("Row1");
	$Table1->appendChild($Row1);
	
	
	//create root / header / Table1 / Row1 / QuoteCompany
	$QuoteCompany = $doc->createElement("QuoteCompany");
	$QuoteCompany->appendChild(
	$doc->createTextNode($projectObj->name));
	$Row1->appendChild($QuoteCompany);
	//end root / header / Table1 / Row1 / QuoteCompany
	//end root / header / Table1 / Row1 
	//end root / header / Table1 
	
	//create root / header / ServicesTable
	$ServicesTable = $doc->createElement("ServicesTable");
	$header->appendChild($ServicesTable);
	
	//create a language array for use later
	$langArray = array();
	if (count($taskService->lingTasks) < 2)
		$langArray[] = $taskService->lingTasks->targLang;
	else
	{
		foreach($taskService->lingTasks as $lingTask)
		{
			if (!in_array($lingTask->targLang, $langArray ))
			{
				$langArray[] = $lingTask->targLang;
			}
		}
	}
	
	//create root / header / ServicesTable / Row1(s)
	$langServs = array();
	if (count($taskService->lingTasks) < 2)
	{
		$langServs[] = $taskService->lingTasks->ltask->type;
		$Row1 = $doc->createElement("Row1");
		$ReqServ = $doc->createElement("ReqServ");
		switch($taskService->lingTasks->ltask->type)
		{
			case 'TR': $ReqServ->appendChild($doc->createTextNode('Translation')); break;
			case 'CE': $ReqServ->appendChild($doc->createTextNode('Copy Editing')); break;
			case 'TR+CE': $ReqServ->appendChild($doc->createTextNode('Translation and Copyediting')); break;
			case 'PR': $ReqServ->appendChild($doc->createTextNode('Proofreading')); break;
			case 'OLR': $ReqServ->appendChild($doc->createTextNode('Online Review')); break;
			default: $ReqServ->appendChild($doc->createTextNode($taskService->lingTasks->ltask->type));
		}
		
		$Row1->appendChild($ReqServ);
		$ServicesTable->appendChild($Row1);
	}
	else
	{
		foreach($taskService->lingTasks as $lingTask)
		{
			if (!in_array($lingTask->ltask->type, $langServs ))
			{	
					$langServs[] = $lingTask->ltask->type;
					$Row1 = $doc->createElement("Row1");
					$ReqServ = $doc->createElement("ReqServ");
					switch($lingTask->ltask->type)
					{
						case 'TR': $ReqServ->appendChild($doc->createTextNode('Translation')); break;
						case 'CE': $ReqServ->appendChild($doc->createTextNode('Copy Editing')); break;
						case 'TR+CE': $ReqServ->appendChild($doc->createTextNode('Translation and Copyediting')); break;
						case 'PR': $ReqServ->appendChild($doc->createTextNode('Proofreading')); break;
						case 'OLR': $ReqServ->appendChild($doc->createTextNode('Online Review')); break;
						default: $ReqServ->appendChild($doc->createTextNode($lingTask->ltask->type));
					}
					
					$Row1->appendChild($ReqServ);
					$ServicesTable->appendChild($Row1);
			}
		}
	}
	if (count($taskService->billableTasks) < 2)
	{
		if (!in_array($taskService->billableTasks->btask->name, $langServs))	//check to see if our task is not already in the array
		{
			if (($taskService->billableTasks->btask->name[strlen($taskService->billableTasks->btask->name)-1] == '0') ||
				($taskService->billableTasks->btask->name[strlen($taskService->billableTasks->btask->name)-1] == '1') ||
				($taskService->billableTasks->btask->name[strlen($taskService->billableTasks->btask->name)-1] == '2') ||
				($taskService->billableTasks->btask->name[strlen($taskService->billableTasks->btask->name)-1] == '3') ||
				($taskService->billableTasks->btask->name[strlen($taskService->billableTasks->btask->name)-1] == '4') ||
				($taskService->billableTasks->btask->name[strlen($taskService->billableTasks->btask->name)-1] == '5') ||
				($taskService->billableTasks->btask->name[strlen($taskService->billableTasks->btask->name)-1] == '6') ||
				($taskService->billableTasks->btask->name[strlen($taskService->billableTasks->btask->name)-1] == '7') ||
				($taskService->billableTasks->btask->name[strlen($taskService->billableTasks->btask->name)-1] == '8') ||
				($taskService->billableTasks->btask->name[strlen($taskService->billableTasks->btask->name)-1] == '9'))	//if the task ends in a digit then we need to strip it off and check again
			{
				if (!in_array(substr($taskService->billableTasks->btask->name,0,strlen($taskService->billableTasks->btask->name)-2),$langServs))
				{
					$langServs[] = substr($taskService->billableTasks->btask->name,0,strlen($taskService->billableTasks->btask->name)-2);	//add it to the array and the xml
					$Row1 = $doc->createElement("Row1");
					$ReqServ = $doc->createElement("ReqServ");
					switch (substr($taskService->billableTasks->btask->name,0,strlen($taskService->billableTasks->btask->name)-2))
					{
						case 'Format': $ReqServ->appendChild($doc->createTextNode('Formatting')); break;
						case 'QA': $ReqServ->appendChild($doc->createTextNode('Quality Assurance Review')); break;
						default: $ReqServ->appendChild($doc->createTextNode(substr($taskService->billableTasks->btask->name,0,strlen($taskService->billableTasks->btask->name)-2)));
					}
					$Row1->appendChild($ReqServ);
					$ServicesTable->appendChild($Row1);
				}
				
			}
			else
			{
				//don't want final format or any coordination task to show up, so check for those
				if (($taskService->billableTasks->btask->name != 'Final Format') && (stristr($taskService->billableTasks->btask->name, 'coord') == FALSE)) 
				{
					$langServs[] = $taskService->billableTasks->btask->name;
					$Row1 = $doc->createElement("Row1");
					$ReqServ = $doc->createElement("ReqServ");
					switch($taskService->billableTasks->btask->name)
					{
						case 'QA': $ReqServ->appendChild($doc->createTextNode('Quality Assurance Review')); break;
						case 'TM Work': $ReqServ->appendChild($doc->createTextNode('Translation Memory Maintenance')); break;
						default: $ReqServ->appendChild($doc->createTextNode($taskService->billableTasks->btask->name));
					}
					$Row1->appendChild($ReqServ);
					$ServicesTable->appendChild($Row1);
				}
			}
		}
	}
	else
	{
		foreach($taskService->billableTasks as $billTask)
		{
			if (!in_array($billTask->btask->name, $langServs))	//check to see if our task is not already in the array
			{
				if (($billTask->btask->name[strlen($billTask->btask->name)-1] == '0') ||
					($billTask->btask->name[strlen($billTask->btask->name)-1] == '1') ||
					($billTask->btask->name[strlen($billTask->btask->name)-1] == '2') ||
					($billTask->btask->name[strlen($billTask->btask->name)-1] == '3') ||
					($billTask->btask->name[strlen($billTask->btask->name)-1] == '4') ||
					($billTask->btask->name[strlen($billTask->btask->name)-1] == '5') ||
					($billTask->btask->name[strlen($billTask->btask->name)-1] == '6') ||
					($billTask->btask->name[strlen($billTask->btask->name)-1] == '7') ||
					($billTask->btask->name[strlen($billTask->btask->name)-1] == '8') ||
					($billTask->btask->name[strlen($billTask->btask->name)-1] == '9'))	//if the task ends in a digit then we need to strip it off and check again
				{
					if (!in_array(substr($billTask->btask->name,0,strlen($billTask->btask->name)-2),$langServs))
					{
						$langServs[] = substr($billTask->btask->name,0,strlen($billTask->btask->name)-2);	//add it to the array and the xml
						$Row1 = $doc->createElement("Row1");
						$ReqServ = $doc->createElement("ReqServ");
						switch (substr($billTask->btask->name,0,strlen($billTask->btask->name)-2))
						{
							case 'Format': $ReqServ->appendChild($doc->createTextNode('Formatting')); break;
							case 'QA': $ReqServ->appendChild($doc->createTextNode('Quality Assurance Review')); break;
							default: $ReqServ->appendChild($doc->createTextNode(substr($billTask->btask->name,0,strlen($billTask->btask->name)-2)));
						}
						$Row1->appendChild($ReqServ);
						$ServicesTable->appendChild($Row1);
					}
					
				}
				else
				{
					//don't want final format or any coordination task to show up, so check for those
					if (($billTask->btask->name != 'Final Format') && (stristr($billTask->btask->name, 'coord') == FALSE)) 
					{
						$langServs[] = $billTask->btask->name;
						$Row1 = $doc->createElement("Row1");
						$ReqServ = $doc->createElement("ReqServ");
						switch($billTask->btask->name)
						{
							case 'QA': $ReqServ->appendChild($doc->createTextNode('Quality Assurance Review')); break;
							case 'TM Work': $ReqServ->appendChild($doc->createTextNode('Translation Memory Maintenance')); break;
							default: $ReqServ->appendChild($doc->createTextNode($billTask->btask->name));
						}
						$Row1->appendChild($ReqServ);
						$ServicesTable->appendChild($Row1);
					}
				}
			}
		}
	}
	//end root / header / ServicesTable / Row1
	//end root / header / ServicesTable
	
	//create root / header / ProjectDescription
	$ProjectDescription = $doc->createElement("ProjectDescription");
	$ProjectDescription->appendChild($doc->createTextNode("Thank you for the opportunity to provide you with an estimate for localization services. We have estimated the scope of work based on the files provided by you as listed below, and the services requested:"));
	$header->appendChild($ProjectDescription);
	//end root / header / ProjectDescription
	
	//create root / header / DescriptionTable
	$DescriptionTable = $doc->createElement("DescriptionTable");
	$header->appendChild($DescriptionTable);
	
	//create root / header / DescriptionTable / Row1
	$Row1 = $doc->createElement("Row1");
	$DescriptionTable->appendChild($Row1);
	
	//create root / header / DescriptionTable / Row1 / Company
	$Company = $doc->createElement("Company");	
	$Company->appendChild($doc->createTextNode($projectObj->company->name));
	$Row1->appendChild($Company);
	//end root / header / DescriptionTable / Row1 / Company
	
	//create root / header / DescriptionTable / Row1 / InvoiceDate
	$InvoiceDate = $doc->createElement("InvoiceDate");
	date_default_timezone_set('America/Los_Angeles');
	$dateFormat = "M d, Y";
	$todayDate = date($dateFormat);	
	$InvoiceDate->appendChild($doc->createTextNode($todayDate));
	$Row1->appendChild($InvoiceDate);
	//end root / header / DescriptionTable / Row1 / InvoiceDate
	//end root / header / DescriptionTable / Row1 
	
	//create root / header / DescriptionTable / Row2
	$Row2 = $doc->createElement("Row2");
	$DescriptionTable->appendChild($Row2);
	
	//create root / header / DescriptionTable / Row2 / ContactName
	$ContactName = $doc->createElement("ContactName");
	$cn = $projectObj->contact->firstName . " " . $projectObj->contact->lastName."\n";
	$cn .= $projectObj->contact->phone ."\n".$projectObj->contact->email;
	$ContactName->appendChild($doc->createTextNode($cn));
	$Row2->appendChild($ContactName);
	//end root / header / DescriptionTable / Row2 / ContactName
	
	//create root / header / DescriptionTable / Row2 / lingoContact
	$lingoContact = $doc->createElement("lingoContact");
	$lc = $projectObj->sponsor->firstName;
	$lc .= " " . $projectObj->sponsor->lastName."\n";
	$lc .= $projectObj->sponsor->phone ."\n".$projectObj->sponsor->email;
	$lingoContact->appendChild($doc->createTextNode($lc));
	$Row2->appendChild($lingoContact);
	//end root / header / DescriptionTable / Row2 / lingoContact
	//end root / header / DescriptionTable / Row2
	
	//create root / header / DescriptionTable / Row3
	$Row3 = $doc->createElement("Row3");
	$DescriptionTable->appendChild($Row3);
	
	//create root / header / DescriptionTable / Row3 / SrcLang
	$SrcLang = $doc->createElement("SrcLang");
	
	$SrcLang->appendChild($doc->createTextNode($sourceLanguage));
	$Row3->appendChild($SrcLang);
	//end root / header / DescriptionTable / Row3 / SrcLang
	
	//create root / header / DescriptionTable / Row3 / ProjectNumber
	$ProjectNumber = $doc->createElement("ProjectNumber");
	$ProjectNumber->appendChild($doc->createTextNode($projectObj->id));
	$Row3->appendChild($ProjectNumber);
	//end root / header / DescriptionTable / Row3 / ProjectNumber
	//end root / header / DescriptionTable / Row3 
	
	//create root / header / DescriptionTable / Row4
	$Row4 = $doc->createElement("Row4");
	$DescriptionTable->appendChild($Row4);
	
	//create root / header / DescriptionTable / Row4 / TgtLanguages
	$TgtLanguages = $doc->createElement("TgtLanguages");
	$l = "";
	foreach ($langArray as $language)
	{
		$l .= $language . ", ";
	}
	$l = substr($l,0,strlen($l)-2);   //remove the trailing return from the string
	$TgtLanguages->appendChild($doc->createTextNode($l));
	$Row4->appendChild($TgtLanguages);
	//end root / header / DescriptionTable / Row4 / TgtLanguages
	
	//create root / header / DescriptionTable / Row5
	$Row5 = $doc->createElement("Row5");
	$DescriptionTable->appendChild($Row5);
	
	//create root / header / DescriptionTable / Row5 / file name(s)
	$File_names = $doc->createElement("File_names");
	$File_names->appendChild($doc->createTextNode($projectObj->fileInventory));
	$Row5->appendChild($File_names);
	
	//for the next step we need to get the total words
	$totalWordcount = 0;
	if (count($taskService->lingTasks) < 2)
	{
		$totalWordCount = getTotalWords($taskService->lingTasks);
	}
	else
	{
		foreach ($taskService->lingTasks as $lingTask)
		{
			if(($lingTask->ltask->type == 'TR+CE') || ($lingTask->ltask->type == 'TR'))
			{
				$totalWordcount += getTotalWords($lingTask);
			}
		}
	}
	
	//create root / DescriptionTable / Row5 / details
	$File_details = $doc->createElement("File_details");
	$inv_str = "Number of files: " . $projectObj->fileCount ."\n";
	$inv_str .= "Page count: " . $projectObj->pageCount . "\n";
	$inv_str .= "Total word count: " . $totalWordcount;
	$File_details->appendChild($doc->createTextNode($inv_str));
	$Row5->appendChild($File_details);

	
	//end root / header / DescriptionTable 
	//end root / header 
	
	//create root / Body
	$Body = $doc->createElement("Body");
	$root->appendChild($Body);
	
	//create root / Body / totalProjectCost
	$totalProjectCost = $doc->createElement("totalProjectCost");
/*	$cost = 0;
	foreach($taskService->lingTasks as $lt)
	{
		$cost += $lt->ltask->price;
	}
	foreach($taskService->billableTasks as $bt)
	{
		$cost += $bt->btask->price;
	}
*/
	$cost = $_SESSION['totalprice'];
    $realTotalPrice = $_SESSION['realTotalPrice'];
    $realDisTotal = $realTotalPrice / 9;
    $altPrice = $realTotalPrice + $realDisTotal;

    if ($_SESSION['rushFee'] === "custom50" || $_SESSION['rushFee'] === "custom25") {
        $totalProjectCost->appendChild($doc->createTextNode("$" . number_format(round($altPrice, 2), 2)));
        $Body->appendChild($totalProjectCost);
    } else {
        $totalProjectCost->appendChild($doc->createTextNode("$" . number_format(round($cost, 2), 2)));
        $Body->appendChild($totalProjectCost);
    }
    //	end root / Body / totalProjectCost
	
	//create root / Body / projectTimeline
	$projectTimeline = $doc->createElement("projectTimeline");
	$projectTimeline->appendChild($doc->createTextNode("TBD"));
	$Body->appendChild($projectTimeline);
	//end root / Body / projectTimeline
	
	//create root / Body / paymentTerms
	$paymentTerms = $doc->createElement("paymentTerms");
	if ($projectObj->company->paymentTerms == "")
		$paymentTerms->appendChild($doc->createTextNode("30 days from invoice date"));
	else
		$paymentTerms->appendChild($doc->createTextNode($projectObj->company->paymentTerms." days from invoice date"));
	$Body->appendChild($paymentTerms);
	//end root / Body / paymentTerms
	
	//ceate root / Body / billingCycle
	$billingCycle = $doc->createElement("billingCycle");
	$billingCycle->appendChild($doc->createTextNode(" "));
	$Body->appendChild($billingCycle);
	
	
	
	
	//create root / Body / projects
	$projects = $doc->createElement("projects");
	$Body->appendChild($projects);
	
	//create root / Body / projects / projectSubForm
	$projectSubForm = $doc->createElement("projectSubform");
	$projects->appendChild($projectSubForm);
	
	//create root / Body / projects / projectSubForm / ProjectName
	$ProjectName = $doc->createElement("ProjectName");
	$ProjectName->appendChild($doc->createTextNode($projectObj->name));
	$projectSubForm->appendChild($ProjectName);
	//end root / Body / projects / projectSubForm / ProjectName
	
	//sort the languages
	$sortedLangs = array();
	if (count($taskService->lingTasks) < 2)
	{
		$sortedLangs[$taskService->lingTasks->sourceLang][$taskService->lingTasks->targLang][] = $taskService->lingTasks;
	}
	else
	{
		foreach ($taskService->lingTasks as $lt)
		{
			$sortedLangs[$lt->sourceLang][$lt->targLang][] = $lt;
		}
	}
	
	
	//here we start looping through the project to create language tables/tasks/etc
	foreach ($sortedLangs as $srcLng => $bySource)
	{
		foreach($bySource as $tgtLng => $byTarget)
		{
		
			//create root / Body / projects / projectSubForm / LanguageTable
			$LanguageTable = $doc->createElement("LanguageTable");
			$projectSubForm->appendChild($LanguageTable);
			
			//create root / Body / projects / projectSubForm / LanguageTable / langTitle
			$langTitle = $doc->createElement("langTitle");
			$tableLanguage = $doc->createElement("tableLanguage");
			$tableLanguage->appendChild($doc->createTextNode($srcLng." to ".$tgtLng));
			$langTitle->appendChild($tableLanguage);
			$LanguageTable->appendChild($langTitle);
			//end root / Body / projects / projectSubForm / LanguageTable/ langTitle
			
			//create root / Body / projects / projectSubForm / LanguageTable / transHeader
			$transHeader = $doc->createElement("transHeader");
			$LanguageTable->appendChild($transHeader);
			//end root / Body / projects / projectSubForm / LanguageTable / transHeader
			
			
			//do the individual linguistic tasks (i.e. printable = true)
			$subTotal =0;
			$catTotal =0;
			
			
			
			foreach($byTarget as $task)
			{

				//check for DTP task
				$dtpPrice = 0;
				$taskPrice = $task->ltask->price;
				if ($task->wordCounts->formattingHours != 0)
				{
					$dtpPrice = $dtpData[$task->ltask->id]['price'];
					$taskPrice = $task->ltask->price - $dtpPrice;
				}
			
				if ($taskPrice > 0)
				{
							
							
					//create r / B / p / pSF / LT / tR / transTaskName
					$transTaskName = $doc->createElement("transTaskName");
					switch($task->ltask->type)
					{
						case "TR+CE": $taskName = "Translation and Copyediting"; break;
						case "TR": $taskName = "Translation"; break;
						case "CE": $taskName = "Copyediting"; break;
						case 'PR': $taskName = 'Proofreading'; break;
						case "OLR": $taskName = "Online Review"; break;
						default: $taskName = $task->ltask->type;
					}
					if (($taskName != 'Proofreading') && ($taskName != "Online Review"))
					{

						if ($_SESSION['hitMinimum'] == true)
						{
							//create root / Body / projects / projectSubForm / LanguageTable / transRow
							$transRow = $doc->createElement("transRow");
							$LanguageTable->appendChild($transRow);
							
							$transTaskLng = $doc->createElement('tableLanguage');
							$transTaskLng->appendChild($doc->createTextNode($srcLng." to ".$tgtLng));
							$transRow->appendChild($transTaskLng);
							
							$transTaskName = $doc->createElement('transTaskName');
							$transTaskName->appendChild($doc->createTextNode($taskName));
							$transRow->appendChild($transTaskName);
							//end r / B / p / pSF / lT / tR / transTaskName
							
							//create r / B / p / pSF / LT / tR / transUnits
							$transUnits = $doc->createElement("transUnits"); 
							$transUnits->appendChild($doc->createTextNode("1 hour"));
							$transRow->appendChild($transUnits);
							//end r / B / p / pSF / lT / tR / transUnits
							
							//create r / B / p / pSF / lT / tR / transRate
							$transRate = $doc->createElement("transRate");
							$transRate->appendChild($doc->createTextNode("---"));
							$transRow->appendChild($transRate);
							//end r / B / p / pSF / lT / tR / transRate
							
							//create r / B / p / pSF / lT / tR / transCost
							$transCost = $doc->createElement("transCost");
							$transCost->appendChild($doc->createTextNode("$".number_format($_SESSION['totalprice'],2)));
							$transRow->appendChild($transCost);
							//end r / B / p / pSF / lT / tR / transCost
							//end r / B / p / pSF / lT / transRow
							
							$subTotal += $_SESSION['totalprice'];
							$catTotal += $_SESSION['totalprice'];
						}
						else
						{
							if ($passTrados)
							{
								if ($task->wordCounts->newWords > 0)
								{
									//create root / Body / projects / projectSubForm / LanguageTable / transRow
									$transRow = $doc->createElement("transRow");
									$LanguageTable->appendChild($transRow);
									
									$transTaskLng = $doc->createElement('tableLanguage');
									$transTaskLng->appendChild($doc->createTextNode($srcLng." to ".$tgtLng));
									$transRow->appendChild($transTaskLng);
									
//									$transTaskLng = $doc->createElement('tableLanguage');
//									$transTaskLng->appendChild($doc->createTextNode($srcLng." to ".$tgtLng));
//									$transRow->appendChild($transTaskLng);
									
									$transTaskName = $doc->createElement('transTaskName');
									$transTaskName->appendChild($doc->createTextNode($taskName ."- New Text"));
									$transRow->appendChild($transTaskName);
									//end r / B / p / pSF / lT / tR / transTaskName
									
									//create r / B / p / pSF / LT / tR / transUnits
									$transUnits = $doc->createElement("transUnits"); 
									$transUnits->appendChild($doc->createTextNode(number_format($task->wordCounts->newWords) . " words"));
									$transRow->appendChild($transUnits);
									//end r / B / p / pSF / lT / tR / transUnits
									
									//create r / B / p / pSF / lT / tR / transRate
									$transRate = $doc->createElement("transRate");
									$transRate->appendChild($doc->createTextNode("$".number_format($sellRates[$task->ltask->id]['New_Text'],2) . "/word"));
									$transRow->appendChild($transRate);
									//end r / B / p / pSF / lT / tR / transRate
									
									//create r / B / p / pSF / lT / tR / transCost
									$transCost = $doc->createElement("transCost");
									$x = $task->wordCounts->newWords * $sellRates[$task->ltask->id]['New_Text'];
									$transCost->appendChild($doc->createTextNode("$".number_format($x,2)));
									$transRow->appendChild($transCost);
									//end r / B / p / pSF / lT / tR / transCost
									//end r / B / p / pSF / lT / transRow
								}
								if ($task->wordCounts->fuzzyWords > 0)
								{
									//create root / Body / projects / projectSubForm / LanguageTable / transRow
									$transRow = $doc->createElement("transRow");
									$LanguageTable->appendChild($transRow);
									
									$transTaskLng = $doc->createElement('tableLanguage');
									$transTaskLng->appendChild($doc->createTextNode($srcLng." to ".$tgtLng));
									$transRow->appendChild($transTaskLng);
									
//									$transTaskLng = $doc->createElement('tableLanguage');
//									$transTaskLng->appendChild($doc->createTextNode($srcLng." to ".$tgtLng));
//									$transRow->appendChild($transTaskLng);
									
									$transTaskName = $doc->createElement('transTaskName');
									$transTaskName->appendChild($doc->createTextNode($taskName ."- Fuzzy Text"));
									$transRow->appendChild($transTaskName);
									//end r / B / p / pSF / lT / tR / transTaskName
									
									//create r / B / p / pSF / LT / tR / transUnits
									$transUnits = $doc->createElement("transUnits"); 
									$transUnits->appendChild($doc->createTextNode(number_format($task->wordCounts->fuzzyWords) . " words"));
									$transRow->appendChild($transUnits);
									//end r / B / p / pSF / lT / tR / transUnits
									
									//create r / B / p / pSF / lT / tR / transRate
									$transRate = $doc->createElement("transRate");
									$transRate->appendChild($doc->createTextNode("$".number_format($sellRates[$task->ltask->id]['Fuzzy_Text'],2) . "/word"));
									$transRow->appendChild($transRate);
									//end r / B / p / pSF / lT / tR / transRate
									
									//create r / B / p / pSF / lT / tR / transCost
									$transCost = $doc->createElement("transCost");
									$x = $task->wordCounts->fuzzyWords * $sellRates[$task->ltask->id]['Fuzzy_Text'];
									$transCost->appendChild($doc->createTextNode("$".number_format($x,2)));
									$transRow->appendChild($transCost);
									//end r / B / p / pSF / lT / tR / transCost
									//end r / B / p / pSF / lT / transRow
								}
								
								if ($task->wordCounts->matchRepsWords > 0)
								{
									//create root / Body / projects / projectSubForm / LanguageTable / transRow
									$transRow = $doc->createElement("transRow");
									$LanguageTable->appendChild($transRow);
									
									$transTaskLng = $doc->createElement('tableLanguage');
									$transTaskLng->appendChild($doc->createTextNode($srcLng." to ".$tgtLng));
									$transRow->appendChild($transTaskLng);
									
//									$transTaskLng = $doc->createElement('tableLanguage');
//									$transTaskLng->appendChild($doc->createTextNode($srcLng." to ".$tgtLng));
//									$transRow->appendChild($transTaskLng);
									
									$transTaskName = $doc->createElement('transTaskName');
									$transTaskName->appendChild($doc->createTextNode($taskName ."- Repetition & 100% Matches"));
									$transRow->appendChild($transTaskName);
									//end r / B / p / pSF / lT / tR / transTaskName
									
									//create r / B / p / pSF / LT / tR / transUnits
									$transUnits = $doc->createElement("transUnits"); 
									$transUnits->appendChild($doc->createTextNode(number_format($task->wordCounts->matchRepsWords) . " words"));
									$transRow->appendChild($transUnits);
									//end r / B / p / pSF / lT / tR / transUnits
									
									//create r / B / p / pSF / lT / tR / transRate
									$transRate = $doc->createElement("transRate");
									$transRate->appendChild($doc->createTextNode("$".number_format($sellRates[$task->ltask->id]['Match_Text'],2) . "/word"));
									$transRow->appendChild($transRate);
									//end r / B / p / pSF / lT / tR / transRate
									
									//create r / B / p / pSF / lT / tR / transCost
									$transCost = $doc->createElement("transCost");
									$x = $task->wordCounts->matchRepsWords * $sellRates[$task->ltask->id]['Match_Text'];
									$transCost->appendChild($doc->createTextNode("$".number_format($x,2)));
									$transRow->appendChild($transCost);
									//end r / B / p / pSF / lT / tR / transCost
									//end r / B / p / pSF / lT / transRow
								}
	
	
								$subTotal += $taskPrice;
								$catTotal += $taskPrice;
							}
							else
							{
								//create root / Body / projects / projectSubForm / LanguageTable / transRow
								$transRow = $doc->createElement("transRow");
								$LanguageTable->appendChild($transRow);
								
								$transTaskLng = $doc->createElement('tableLanguage');
								$transTaskLng->appendChild($doc->createTextNode($srcLng." to ".$tgtLng));
								$transRow->appendChild($transTaskLng);
								
//								$transTaskLng = $doc->createElement('tableLanguage');
//								$transTaskLng->appendChild($doc->createTextNode($srcLng." to ".$tgtLng));
//								$transRow->appendChild($transTaskLng);
								
								$transTaskName = $doc->createElement('transTaskName');
								$transTaskName->appendChild($doc->createTextNode($taskName));
								$transRow->appendChild($transTaskName);
								//end r / B / p / pSF / lT / tR / transTaskName
								
								//create r / B / p / pSF / LT / tR / transUnits
								$transUnits = $doc->createElement("transUnits"); 
								$words = getTotalWords($task);
								$transUnits->appendChild($doc->createTextNode(number_format($words) . " words"));
								$transRow->appendChild($transUnits);
								//end r / B / p / pSF / lT / tR / transUnits
								
								//create r / B / p / pSF / lT / tR / transRate
								$transRate = $doc->createElement("transRate");
								if ($projectObj->company->docTransPricingScheme != 'LLS Pricing') {
                                    $transRate->appendChild($doc->createTextNode("$" . number_format($sellRates[$task->ltask->id], 2) . "/word"));
                                } else {
                                    $transRate->appendChild($doc->createTextNode("$" . number_format($sellRates[$task->ltask->id], 2) . " per\n25 word block"));
                                }
                                $transRow->appendChild($transRate);
                                //end r / B / p / pSF / lT / tR / transRate

                                //create r / B / p / pSF / lT / tR / transCost
                                $transCost = $doc->createElement("transCost");
                                $transCost->appendChild($doc->createTextNode("$" . number_format($taskPrice, 2)));
                                $transRow->appendChild($transCost);
                                //end r / B / p / pSF / lT / tR / transCost
                                //end r / B / p / pSF / lT / transRow
                                $subTotal += $taskPrice;
                                $catTotal += $taskPrice;
							}
						}
						//do the formatting
						if ( ($dtpPrice > 0) && ($_SESSION['hitMinimum'] == false))
						{
							//create root / Body / projects / projectSubForm / LanguageTable / transRow
							$transRow = $doc->createElement("transRow");
							$LanguageTable->appendChild($transRow);
							
							$transTaskLng = $doc->createElement('tableLanguage');
							$transTaskLng->appendChild($doc->createTextNode($srcLng." to ".$tgtLng));
							$transRow->appendChild($transTaskLng);
							
							$transTaskName = $doc->createElement('transTaskName');
							$transTaskName->appendChild($doc->createTextNode('Formatting'));
							$transRow->appendChild($transTaskName);
							//end r / B / p / pSF / lT / tR / transTaskName
							
							//create r / B / p / pSF / LT / tR / transUnits
							$transUnits = $doc->createElement("transUnits"); 
							$transUnits->appendChild($doc->createTextNode(number_format($task->wordCounts->formattingHours,2) . " hours"));
							$transRow->appendChild($transUnits);
							//end r / B / p / pSF / lT / tR / transUnits
							
							//create r / B / p / pSF / lT / tR / transRate
							$transRate = $doc->createElement("transRate");
							$transRate->appendChild($doc->createTextNode("$".number_format($dtpData[$task->ltask->id]['rate'],2) . "/hour"));
							$transRow->appendChild($transRate);
							//end r / B / p / pSF / lT / tR / transRate
							
							//create r / B / p / pSF / lT / tR / transCost
							$transCost = $doc->createElement("transCost");
							$transCost->appendChild($doc->createTextNode("$".number_format($dtpPrice,2)));
							$transRow->appendChild($transCost);
							//end r / B / p / pSF / lT / tR / transCost
							//end r / B / p / pSF / lT / transRow
							$subTotal += $dtpPrice;
							$catTotal += $dtpPrice;
						}
					}
					
						
					else
					{
					    $workRequired = $task->ltask->workRequired != 0;
                        $proofItem = ($taskName === 'Proofreading' && $_SESSION['proofReading'] !== 'yes');
                        $taskNameBool = $taskName !== 'Proofreading';
						if ($workRequired && ($proofItem || $taskNameBool))
						{
							//create root / Body / projects / projectSubForm / LanguageTable / transRow
							$transRow = $doc->createElement("transRow");
							$LanguageTable->appendChild($transRow);
							
							$transTaskLng = $doc->createElement('tableLanguage');
							$transTaskLng->appendChild($doc->createTextNode($srcLng." to ".$tgtLng));
							$transRow->appendChild($transTaskLng);
							
							//create r / B / p / pSF / LT / tR / transTaskName
							$transTaskName = $doc->createElement("transTaskName");
							
							$transTaskName->appendChild($doc->createTextNode($taskName));
							$transRow->appendChild($transTaskName);
							//end r / B / p / pSF / lT / tR / transTaskName
							
							//create r / B / p / pSF / LT / tR / transUnits
							$transUnits = $doc->createElement("transUnits"); 
							$transUnits->appendChild($doc->createTextNode($task->ltask->workRequired . " hours"));
							$transRow->appendChild($transUnits);
							//end r / B / p / pSF / lT / tR / transUnits
							
							//create r / B / p / pSF / lT / tR / transRate
							$transRate = $doc->createElement("transRate");
							$transRate->appendChild($doc->createTextNode("$".number_format($sellRates[$task->ltask->id],2) . "/hour"));
							$transRow->appendChild($transRate);
							//end r / B / p / pSF / lT / tR / transRate
							
							//create r / B / p / pSF / lT / tR / transCost
							$transCost = $doc->createElement("transCost");
							$transCost->appendChild($doc->createTextNode("$".number_format($task->ltask->price,2)));
							$transRow->appendChild($transCost);
							//end r / B / p / pSF / lT / tR / transCost
							//end r / B / p / pSF / lT / transRow
							$subTotal += $task->ltask->price;
							$catTotal += $task->ltask->price;
						}
					}
				}
					
			} 

			//end root / Body / projects / projectSubForm / LanguageTable / transRow
			
			if($catTotal > 0)
			{
				$transFooter = $doc->createElement('transFooter');
				$transSubTotal = $doc->createElement('transSubTotal');
				$transSubTotal->appendChild($doc->createTextNode("$".number_format($catTotal,2)));
				$transFooter->appendChild($transSubTotal);
				$LanguageTable->appendChild($transFooter);
			}


			
			
			$langFooter = $doc->createElement("langFooter");
			$LanguageTable->appendChild($langFooter);
			
			//create root / Body / projects / projectSubForm / LanguageTable / langFooter / langSubTotal
			$langSubTotal = $doc->createElement("langSubTotal");
			$langSubTotal->appendChild($doc->createTextNode("$".number_format($subTotal,2)));
			$langFooter->appendChild($langSubTotal);
			$projectTotalCost += $subTotal;
			//end root / Body / projects / projectSubForm / LanguageTable / langFooter / langSubTotal
			//end root / Body / projects / projectSubForm / LanguageTable / langFooter
			
			

		}	//end !nondstributed if
	}
		
	//end root / Body / projects / projectSubForm / LanguageTable 
	if (( count($taskService->billableTasks) > 0) && ($_SESSION['hitMinimum'] == false))
	{	
		//create root / Body / projects / projectSubFor / IndependentTable
		$IndependentTable = $doc->createElement("IndependentTable");
		$projectSubForm->appendChild($IndependentTable);
		
		//create root / Body / projects / projectSubForm / IndependentTable / indyTitle
		$indyTitle = $doc->createElement("indyTitle");
		$IndependentTable->appendChild($indyTitle);
		//end root / Body / projects / projectSubForm / IndependentTable / indyTitle
		
		//create root / Body / projects / projectSubForm / IndependentTable / dtpHeader
		$dtpHeader = $doc->createElement("dtpHeader");
		$IndependentTable->appendChild($dtpHeader);
		//end root / Body / projects / projectSubForm / IndependentTable / dtpHeader
		
		$subTotal = 0;
		$catTotal = 0;
		
		
		if ($bundleInternal)
		{
			if (count($taskService->billableTasks) < 2)
			{
				if (($taskService->billableTasks->btask->type != 'Project Manager') && ($taskService->billableTasks->btask->price > 0))
				{
					$catTotal += $taskService->billableTasks->btask->price;
				}
			}
			else
			{
				foreach ($taskService->billableTasks as $billableTask)
				{
					if (($billableTask->btask->type != 'Project Manager') && ($billableTask->btask->price >0))
					{
						$catTotal += $billableTask->btask->price;
					}
				}
				
			}
			
			if ($catTotal > 0)
			{
				//create root / Body / projects / projectSubFor / IndependentTable / dtpRow
				$dtpRow = $doc->createElement("dtpRow");
				$IndependentTable->appendChild($dtpRow);
				
				//create r / B / p / pSF / IndependentTable / dR / dtpTaskName
				$dtpTaskName = $doc->createElement("dtpTaskName");
				$dtpTaskName->appendChild($doc->createTextNode("Formatting"));
				$dtpRow->appendChild($dtpTaskName);
				//end r / B / p / pSF / IndependentTable / dR / dtpTaskName
				
				/*
				//create r / B / p / pSF / IndependentTable / dR / dtpUnits
				$dtpUnits = $doc->createElement("dtpUnits");
				$dtpUnits->appendChild($doc->createTextNode($taskService->billableTasks->btask->workRequired . " hours"));
				$dtpRow->appendChild($dtpUnits);
				//end r / B / p / pSF / IndependentTable / dR / dtpUnits
				
				//create r / B / p / pSF / IndependentTable / dR / dtpRate
				$dtpRate = $doc->createElement("dtpRate");
				$dtpRate->appendChild($doc->createTextNode("$".number_format($sellRates[$taskService->billableTasks->btask->id],2) . "/hour"));
				$dtpRow->appendChild($dtpRate);
				//end r / B / p / pSF / IndependentTable / dR / dtpRate
				*/
				
				//create r / B / p / pSF / IndependentTable / dR / dtpCost
				$dtpCost = $doc->createElement("dtpCost");
				$dtpCost->appendChild($doc->createTextNode("$".number_format($catTotal,2)));
				$dtpRow->appendChild($dtpCost);
				//end r / B / p / pSF / IndependentTable / dR / dtpCost
				$subTotal += $catTotal;
				
				
			}

			//find any pm tasks and make a new row
			if (count($taskService->billableTasks) < 2)
			{
				if ($taskService->billableTasks->btask->type == "Project Manager") 
				{
					if ($taskService->billableTasks->btask->price > 0)
					{

						//create r / B / p / pSF / IndependentTable / dR / pmCost
						$pmRow = $doc->createElement("pmRow");
						$IndependentTable->appendChild($pmRow);
						
						$pmCost = $doc->createElement("pmCost");
						$pmCost->appendChild($doc->createTextNode("$".number_format($taskService->billableTasks->btask->price,2)));
						$pmRow->appendChild($pmCost);
						//end r / B / p / pSF / IndependentTable / dR / pmCost
						$catTotal += $taskService->billableTasks->btask->price;
						$subTotal += $taskService->billableTasks->btask->price;

                        if ($_SESSION['rushFee'] === "custom50" || $_SESSION['rushFee'] === "custom25") {
                            $realSubTotal = $langSubTotal->nodeValue;
                            $realSubTotal = substr($realSubTotal, 1);
                            $subTotal = $realSubTotal / 9;
//                            $pmCost = $realSubTotal / 9;
                            $pmCost->nodeValue = "$" . number_format($realSubTotal / 9, 2);
                        }

					}
					else
					{
						$pmRow = $doc->createElement("pmRow");
						$IndependentTable->appendChild($pmRow);
						
						$pmCost = $doc->createElement("pmCost");
						$pmCost->appendChild($doc->createTextNode("waived"));
						$pmRow->appendChild($pmCost);
					}
				}

			}
			else
			{
				foreach ($taskService->billableTasks as $task)
				{
					if ($task->btask->type == "Project Manager")
					{
						if ($task->btask->price > 0)
						{
							//create r / B / p / pSF / IndependentTable / dR / pmCost
							$pmRow = $doc->createElement("pmRow");
							$IndependentTable->appendChild($pmRow);
							
							$pmCost = $doc->createElement("pmCost");
							$pmCost->appendChild($doc->createTextNode("$".number_format($task->btask->price,2)));
							$pmRow->appendChild($pmCost);
							//end r / B / p / pSF / IndependentTable / dR / pmCost
							$catTotal += $task->btask->price;
							$subTotal += $task->btask->price;
						}
					}
		
				}
			}
			
			//end root / Body / projects / projectSubFor / IndependentTable / pmRow
		}
		else
		{
		
		
			//find any dtp tasks and make a new row
			if (count($taskService->billableTasks) < 2)
			{
				if (($taskService->billableTasks->btask->type == "Formatting Specialist") && ($taskService->billableTasks->btask->price > 0))
				{
					//create root / Body / projects / projectSubFor / IndependentTable / dtpRow
					$dtpRow = $doc->createElement("dtpRow");
					$IndependentTable->appendChild($dtpRow);
					
					//create r / B / p / pSF / IndependentTable / dR / dtpTaskName
					$dtpTaskName = $doc->createElement("dtpTaskName");
					$dtpTaskName->appendChild($doc->createTextNode($taskService->billableTasks->btask->name));
					$dtpRow->appendChild($dtpTaskName);
					//end r / B / p / pSF / IndependentTable / dR / dtpTaskName
					
					//create r / B / p / pSF / IndependentTable / dR / dtpUnits
					$dtpUnits = $doc->createElement("dtpUnits");
					$dtpUnits->appendChild($doc->createTextNode($taskService->billableTasks->btask->workRequired . " hours"));
					$dtpRow->appendChild($dtpUnits);
					//end r / B / p / pSF / IndependentTable / dR / dtpUnits
					
					//create r / B / p / pSF / IndependentTable / dR / dtpRate
					$dtpRate = $doc->createElement("dtpRate");
					$dtpRate->appendChild($doc->createTextNode("$".number_format($sellRates[$taskService->billableTasks->btask->id],2) . "/hour"));
					$dtpRow->appendChild($dtpRate);
					//end r / B / p / pSF / IndependentTable / dR / dtpRate
					
					//create r / B / p / pSF / IndependentTable / dR / dtpCost
					$dtpCost = $doc->createElement("dtpCost");
					$dtpCost->appendChild($doc->createTextNode("$".number_format($taskService->billableTasks->btask->price,2)));
					$dtpRow->appendChild($dtpCost);
					//end r / B / p / pSF / IndependentTable / dR / dtpCost
					$subTotal += $taskService->billableTasks->btask->price;
					$catTotal += $taskService->billableTasks->btask->price;
				}
			}
			else
			{
				foreach ($taskService->billableTasks as $task)
				{
					if ($task->btask->type == "Formatting Specialist")
					{
						if ($task->btask->price > 0)
						{
							//create root / Body / projects / projectSubFor / IndependentTable / dtpRow
							$dtpRow = $doc->createElement("dtpRow");
							$IndependentTable->appendChild($dtpRow);
							
							//create r / B / p / pSF / IndependentTable / dR / dtpTaskName
							$dtpTaskName = $doc->createElement("dtpTaskName");
							$dtpTaskName->appendChild($doc->createTextNode($task->btask->name));
							$dtpRow->appendChild($dtpTaskName);
							//end r / B / p / pSF / IndependentTable / dR / dtpTaskName
							
							//create r / B / p / pSF / IndependentTable / dR / dtpUnits
							$dtpUnits = $doc->createElement("dtpUnits");
							$dtpUnits->appendChild($doc->createTextNode($task->btask->workRequired . " hours"));
							$dtpRow->appendChild($dtpUnits);
							//end r / B / p / pSF / IndependentTable / dR / dtpUnits
							
							//create r / B / p / pSF / IndependentTable / dR / dtpRate
							$dtpRate = $doc->createElement("dtpRate");
							$dtpRate->appendChild($doc->createTextNode("$".number_format($sellRates[$task->btask->id],2) . "/hour"));
							$dtpRow->appendChild($dtpRate);
							//end r / B / p / pSF / IndependentTable / dR / dtpRate
							
							//create r / B / p / pSF / IndependentTable / dR / dtpCost
							$dtpCost = $doc->createElement("dtpCost");
							$dtpCost->appendChild($doc->createTextNode("$".number_format($task->btask->price,2)));
							$dtpRow->appendChild($dtpCost);
							//end r / B / p / pSF / IndependentTable / dR / dtpCost
							$subTotal += $task->btask->price;
							$catTotal += $task->btask->price;
						}
					}
				}
			}
			
			//end root / Body / projects / projectSubFor / IndependentTable / dtpRow
			
			if ($catTotal > 0)
			{
				//create root / body / project / projectSubform / IndependentTable / dtpFooter
				$dtpFooter = $doc->createElement("dtpFooter");
				$dtpSubTotal = $doc->createElement("dtpSubTotal");
				$dtpSubTotal->appendChild($doc->createTextNode("$".number_format($catTotal,2)));
				$dtpFooter->appendChild($dtpSubTotal);
				$IndependentTable->appendChild($dtpFooter);
				//end root/ body / project / projectSubform / IndependentTable / dtpFooter
			}
			
			//create root / Body / projects / projectSubForm / IndependentTable / engHeader
			$engHeader = $doc->createElement("engHeader");
			$IndependentTable->appendChild($engHeader);
			//end root / Body / projects / projectSubForm / IndependentTable / engHeader
			
			$catTotal = 0;
			
			
			
			//find any  eng tasks and make a new row
			if (count($taskService->billableTasks) < 2)
			{
				if (($taskService->billableTasks->btask->type == "Localization Engineer") && ($taskService->billableTasks->btask->price > 0))
				{
					//create root / Body / projects / projectSubFor / IndependentTable / engRow
					$engRow = $doc->createElement("engRow");
					$IndependentTable->appendChild($engRow);
					
					//create r / B / p / pSF / IndependentTable / dR / engTaskName
					$engTaskName = $doc->createElement("engTaskName");
					$engTaskName->appendChild($doc->createTextNode($taskService->billableTasks->btask->name));
					$engRow->appendChild($engTaskName);
					//end r / B / p / pSF / IndependentTable / dR / engTaskName
					
					//create r / B / p / pSF / IndependentTable / dR / engUnits
					$engUnits = $doc->createElement("engUnits");
					$engUnits->appendChild($doc->createTextNode($taskService->billableTasks->btask->workRequired . " hours"));
					$engRow->appendChild($engUnits);
					//end r / B / p / pSF / IndependentTable / dR / engUnits
					
					//create r / B / p / pSF / IndependentTable / dR / engRate
					$engRate = $doc->createElement("engRate");
					$engRate->appendChild($doc->createTextNode("$".number_format($sellRates[$taskService->billableTasks->btask->id],2) . "/hour"));
					$engRow->appendChild($engRate);
					//end r / B / p / pSF / IndependentTable / dR / engRate
					
					//create r / B / p / pSF / IndependentTable / dR / engCost
					$engCost = $doc->createElement("engCost");
					$engCost->appendChild($doc->createTextNode("$".number_format($taskService->billableTasks->btask->price,2)));
					$engRow->appendChild($engCost);
					//end r / B / p / pSF / IndependentTable / dR / engCost
					$subTotal += $taskService->billableTasks->btask->price;
					$catTotal += $taskService->billableTasks->btask->price;
				}
			}
			else
			{
				foreach ($taskService->billableTasks as $task)
				{
					if ($task->btask->type == "Localization Engineer")
					{
						if ($task->btask->price > 0)
						{
							//create root / Body / projects / projectSubFor / IndependentTable / engRow
							$engRow = $doc->createElement("engRow");
							$IndependentTable->appendChild($engRow);
							
							//create r / B / p / pSF / IndependentTable / dR / engTaskName
							$engTaskName = $doc->createElement("engTaskName");
							$engTaskName->appendChild($doc->createTextNode($task->btask->name));
							$engRow->appendChild($engTaskName);
							//end r / B / p / pSF / IndependentTable / dR / engTaskName
							
							//create r / B / p / pSF / IndependentTable / dR / engUnits
							$engUnits = $doc->createElement("engUnits");
							$engUnits->appendChild($doc->createTextNode($task->btask->workRequired . " hours"));
							$engRow->appendChild($engUnits);
							//end r / B / p / pSF / IndependentTable / dR / engUnits
							
							//create r / B / p / pSF / IndependentTable / dR / engRate
							$engRate = $doc->createElement("engRate");
							$engRate->appendChild($doc->createTextNode("$".number_format($sellRates[$task->btask->id],2) . "/hour"));
							$engRow->appendChild($engRate);
							//end r / B / p / pSF / IndependentTable / dR / engRate
							
							//create r / B / p / pSF / IndependentTable / dR / engCost
							$engCost = $doc->createElement("engCost");
							$engCost->appendChild($doc->createTextNode("$".number_format($task->btask->price,2)));
							$engRow->appendChild($engCost);
							//end r / B / p / pSF / IndependentTable / dR / engCost
							$subTotal += $task->btask->price;
							$catTotal += $task->btask->price;
						}
					}
					
				}
			}
			
			//end root / Body / projects / projectSubFor / IndependentTable / engRow
			
			if ($catTotal > 0)
			{
				//create root / body / project / projectSubform / IndependentTable / engFooter
				$engFooter = $doc->createElement("engFooter");
				$engSubTotal = $doc->createElement("engSubTotal");
				$engSubTotal->appendChild($doc->createTextNode("$".number_format($catTotal,2)));
				$engFooter->appendChild($engSubTotal);
				$IndependentTable->appendChild($engFooter);
				//end root/ body / project / projectSubform / IndependentTable / dtpFooter
			}
			
			//find any pm tasks and make a new row
			if (count($taskService->billableTasks) < 2)
			{
				if ($taskService->billableTasks->btask->type == "Project Manager") 
				{
					if ($taskService->billableTasks->btask->price > 0)
					{
						//create r / B / p / pSF / IndependentTable / dR / pmCost
						$pmRow = $doc->createElement("pmRow");
						$IndependentTable->appendChild($pmRow);
						
						$pmCost = $doc->createElement("pmCost");
						$pmCost->appendChild($doc->createTextNode("$".number_format($taskService->billableTasks->btask->price,2)));
						$pmRow->appendChild($pmCost);
						//end r / B / p / pSF / IndependentTable / dR / pmCost
						$catTotal += $taskService->billableTasks->btask->price;
						$subTotal += $taskService->billableTasks->btask->price;

					}
					else
					{
						$pmRow = $doc->createElement("pmRow");
						$IndependentTable->appendChild($pmRow);
						
						$pmCost = $doc->createElement("pmCost");
						$pmCost->appendChild($doc->createTextNode("waived"));
						$pmRow->appendChild($pmCost);
					}
				}
	
			}
			else
			{
				foreach ($taskService->billableTasks as $task)
				{
					if ($task->btask->type == "Project Manager")
					{
						if ($task->btask->price > 0)
						{
							//create r / B / p / pSF / IndependentTable / dR / pmCost
							$pmRow = $doc->createElement("pmRow");
							$IndependentTable->appendChild($pmRow);
							
							$pmCost = $doc->createElement("pmCost");
							$pmCost->appendChild($doc->createTextNode("$".number_format($task->btask->price,2)));
							$pmRow->appendChild($pmCost);
							//end r / B / p / pSF / IndependentTable / dR / pmCost
							$catTotal += $task->btask->price;
							$subTotal += $task->btask->price;

						}
					}
		
				}
			}
			
			//end root / Body / projects / projectSubFor / IndependentTable / pmRow
			
			$catTotal = 0;
			//find any non-distributed other tasks and make a new row
			if (count($taskService->billableTasks) < 2)
			{
				
				if (($taskService->billableTasks->btask->type != "Formatting Specialist") &&
					($taskService->billableTasks->btask->type != "Localization Engineer") &&
					($taskService->billableTasks->btask->type != "Project Manager"))
				{
					if ($taskService->billableTasks->btask->price > 0)
					{
						//create root / Body / projects / projectSubFor / IndependentTable / otherRow
						$otherRow = $doc->createElement("otherRow");
						$IndependentTable->appendChild($otherRow);
						
						//create r / B / p / pSF / IndependentTable / dR / otherTaskName
						$otherTaskName = $doc->createElement("otherTaskName");
						$otherTaskName->appendChild($doc->createTextNode($taskService->billableTasks->btask->name));
						$otherRow->appendChild($otherTaskName);
						//end r / B / p / pSF / IndependentTable / dR / otherTaskName
						
						//create r / B / p / pSF / IndependentTable / dR / otherUnits
						$otherUnits = $doc->createElement("otherUnits");
						$otherUnits->appendChild($doc->createTextNode($taskService->billableTasks->btask->workRequired . " hours"));
						$otherRow->appendChild($otherUnits);
						//end r / B / p / pSF / IndependentTable / dR / otherUnits
						
						//create r / B / p / pSF / IndependentTable / dR / otherRate
						$otherRate = $doc->createElement("otherRate");
						$otherRate->appendChild($doc->createTextNode("$".number_format($sellRates[$taskService->billableTasks->btask->id],2) . "/hour"));
						$otherRow->appendChild($otherRate);
						//end r / B / p / pSF / IndependentTable / dR / otherRate
						
						//create r / B / p / pSF / IndependentTable / dR / otherCost
						$otherCost = $doc->createElement("otherCost");
						$otherCost->appendChild($doc->createTextNode("$".number_format($taskService->billableTasks->btask->price,2)));
						$otherRow->appendChild($otherCost);
						//end r / B / p / pSF / IndependentTable / dR / otherCost
						$subTotal += $taskService->billableTasks->btask->price;
						$catTotal += $taskService->billableTasks->btask->price;
					}
				}
			}
			else
			{
				foreach ($taskService->billableTasks as $task)
				{
					
					if (($task->btask->type != "Formatting Specialist") &&
						($task->btask->type != "Localization Engineer") &&
						($task->btask->type != "Project Manager"))
					{
						if ($task->btask->price > 0)
						{
							//create root / Body / projects / projectSubFor / IndependentTable / otherRow
							$otherRow = $doc->createElement("otherRow");
							$IndependentTable->appendChild($otherRow);
							
							//create r / B / p / pSF / IndependentTable / dR / otherTaskName
							$otherTaskName = $doc->createElement("otherTaskName");
							$otherTaskName->appendChild($doc->createTextNode($task->btask->name));
							$otherRow->appendChild($otherTaskName);
							//end r / B / p / pSF / IndependentTable / dR / otherTaskName
							
							//create r / B / p / pSF / IndependentTable / dR / otherUnits
							$otherUnits = $doc->createElement("otherUnits");
							$otherUnits->appendChild($doc->createTextNode($task->btask->workRequired . " hours"));
							$otherRow->appendChild($otherUnits);
							//end r / B / p / pSF / IndependentTable / dR / otherUnits
							
							//create r / B / p / pSF / IndependentTable / dR / otherRate
							$otherRate = $doc->createElement("otherRate");
							$otherRate->appendChild($doc->createTextNode("$".number_format($sellRates[$task->btask->id],2) . "/hour"));
							$otherRow->appendChild($otherRate);
							//end r / B / p / pSF / IndependentTable / dR / otherRate
							
							//create r / B / p / pSF / IndependentTable / dR / otherCost
							$otherCost = $doc->createElement("otherCost");
							$otherCost->appendChild($doc->createTextNode("$".number_format($task->btask->price,2)));
							$otherRow->appendChild($otherCost);
							//end r / B / p / pSF / IndependentTable / dR / otherCost
							$subTotal += $task->btask->price;
							$catTotal += $task->btask->price;
						}
					}
				}
			}
			//end root / Body / projects / projectSubFor / IndependentTable / engRow
			
			if ($catTotal > 0)
			{
				//create root / body / project / projectSubform / IndependentTable / otherFooter
				$otherFooter = $doc->createElement("otherFooter");
				$otherSubTotal = $doc->createElement("otherSubTotal");
				$otherSubTotal->appendChild($doc->createTextNode("$".number_format($catTotal,2)));
				$otherFooter->appendChild($otherSubTotal);
				$IndependentTable->appendChild($otherFooter);
				//end root/ body / project / projectSubform / IndependentTable / dtpFooter
			}
		}
		
		$indyFooter = $doc->createElement('indyFooter');
		$IndependentTable->appendChild($indyFooter);
		
		$otherTableSubTotal = $doc->createElement('otherTableSubTotal');
		$otherTableSubTotal->appendChild($doc->createTextNode("$".number_format($subTotal,2)));
		$indyFooter->appendChild($otherTableSubTotal);
		$projectTotalCost += $subTotal;
	}
			
	$RushTable = $doc->createElement('RushTable');
	$projectSubForm->appendChild($RushTable);
	
	$subtotalrow = $doc->createElement("subtotalrow");
	$subtotal = $doc->createElement("subtotal");
	$subtotal->appendChild($doc->createTextNode("$".number_format($projectTotalCost,2)));
	$subtotalrow->appendChild($subtotal);
	$RushTable->appendChild($subtotalrow);
	
	$Row1 = $doc->createElement("Row1");
	$rushFee = $doc->createElement("rushFee");
	$rushFee->appendChild($doc->createTextNode("$".number_format($taskService->rushFee,2)));
	$Row1->appendChild($rushFee);
	$RushTable->appendChild($Row1);
	
	$d = abs($taskService->discount);
	if ($d > 0)
	{
		$Row1 = $doc->createElement("Row2");
		$discount = $doc->createElement("discount");
		
		$discount->appendChild($doc->createTextNode("($".number_format($d,2).")"));
		$Row1->appendChild($discount);
		$RushTable->appendChild($Row1);
	}
	
	
	$ProjectTotalTable = $doc->createElement('ProjectTotalTable');
	$projectSubForm->appendChild($ProjectTotalTable);
	
	$Row1 = $doc->createElement("Row1");
	$ProjectTotal = $doc->createElement("ProjectTotal");
	$projectTotalCost += $taskService->rushFee + $taskService->discount;
	$ProjectTotal->appendChild($doc->createTextNode("$".number_format(round($projectTotalCost,2),2)));
	$Row1->appendChild($ProjectTotal);
	$ProjectTotalTable->appendChild($Row1);
			
			
			//end form1
			
	
	
	
//create the datadump
$datadump = $doc->createElement('datadump');
$root->appendChild($datadump);

$xml_TaskService = unserialize($_SESSION['taskService']);
$xml_ProjectObj = unserialize($_SESSION['projectObj']);

$taskservice = $doc->createElement('taskservice');
$taskservice->appendChild($doc->createCDATASection(str_replace("\0", "{[{NULL}]}",serialize($xml_TaskService))));
$datadump->appendChild($taskservice);

$projectobj = $doc->createElement('projectobj');
$projectobj->appendChild($doc->createCDATASection(str_replace("\0", "{[{NULL}]}",serialize($xml_ProjectObj))));
$datadump->appendChild($projectobj);
	
	

$filename = $projectObj->name . "_Quote.xml";
$filename = str_replace(" ","_",$filename);
$filename = str_replace(",","_",$filename);

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=$filename");
echo $doc->saveXML() . "\n";
}

?>
