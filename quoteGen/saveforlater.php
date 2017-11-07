<?PHP
session_start();

ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 1);
include_once('../attaskconn/LingoAtTaskService.php');
include_once('functions/gettotal.php');
include_once('classes/billableTask.php');
include_once('classes/linguistTask.php');
include_once('classes/projectData.php');
include_once('classes/dtpTask.php');
include_once('classes/pmTask.php');



function save_for_later()
{
$projectTotalCost = 0;

$taskService = unserialize($_SESSION['taskService']);
$projectObj = unserialize($_SESSION['projectObj']);
$projectData = unserialize($_SESSION['projectData']);
$langCount = $_SESSION['langCount'];
$thisProject = unserialize($_SESSION['thisProject']);

$sourceLanguage = '';


//make sure all the prices in the taskService are up-to-date
foreach($thisProject as $srcLang =>$bySource)
{
	if ($srcLang != 'nonDistributed')
	{
		$sourceLanguage = $srcLang;
		foreach($bySource as $tgtLang => $byTarget)
		{
			foreach($byTarget['linguistTasks'] as $tpLingTask)
			{
				$id = $tpLingTask->get_id();
				//go through the task service until we find the right task
				foreach($taskService->lingTasks as $tsLingTask)
				{
					if ($tsLingTask->ltask->id == $id)
					{
						if ($tpLingTask->get_sellUnits() == 'hours')
						{
							$tsLingTask->ltask->price = $tpLingTask->asp('hourly');
						}
						else
						{
							$tsLingTask->ltask->price = $tpLingTask->asp('new') + $tpLingTask->asp('fuzzy') + $tpLingTask->asp('match');
						}
						break;
					}
				}
			}

			foreach ($byTarget['billableTasks'] as $tpBillTask)
			{
				if (get_class($tpBillTask) == 'pmTask')
					$fullPrice = $tpBillTask->aspByLanguage($thisProject,$tpBillTask->get_targLang());
				else
					$fullPrice = $tpBillTask->asp();

				$id = $tpBillTask->get_id();
				//now loop the rest of the languages to add up the distributed totals
				foreach($thisProject as $sl => $byS)
				{
					if ($sl != 'nonDistributed')
					{
						foreach($byS as $byT)
						{
							foreach($byT['billableTasks'] as $thisTask)
							{
								if($thisTask->get_targLang() != $tpBillTask->get_targLang())
								{
									if ($thisTask->get_id() == $tpBillTask->get_id())
									{
										if (get_class($thisTask) == 'pmTask')
											$fullPrice += $thisTask->aspByLanguage($thisProject,$thisTask->get_targLang());
										else
											$fullPrice += $thisTask->asp();
									}
								}
							}
						}
					}
				}
				//go through the task service until we find the right task
				foreach($taskService->billableTasks as $tsBillTask)
				{
					if ($tsBillTask->btask->id == $id)
					{
						$tsBillTask->btask->price = $fullPrice;
						break;
					}
				}
			}
		}
	}
	else	//non distributed tasks
	{
		foreach($bySource as $nonDist)
		{
			$id = $nonDist->get_id();
			foreach($taskService->billableTasks as $tsBillTask)
			{
				if ($tsBillTask->btask->id == $id)
				{
					if (get_class($nonDist) == 'pmTask')
					{
						$tsBillTask->btask->price = $nonDist->asp($thisProject);
					}
					else
					{
						$tsBillTask->btask->price = $nonDist->asp();
					}
					break;
				}
			}
		}
	}
	
}

//now that the task service is up-to-date, we'll calculate the total project price
//now that the task service is up-to-date, we'll calculate the total project price
$totPrice = 0;
if (count($taskService->lingTasks) < 2)
	$totPrice += $taskService->lingTasks->ltask->price;
else
{
	foreach($taskService->lingTasks as $lt)
	{
		$totPrice += $lt->ltask->price;
	}
}
if (count($taskService->billableTasks) < 2)
	$totPrice += $taskService->billableTasks->btask->price;
else
{
	foreach($taskService->billableTasks as $bt)
	{
		$totPrice += $bt->btask->price;
	}
}

//update the budget field in the project 
//to do this we need to make sure the cost data for each task
//is pulled from $thisProject and updated in the $taskService
$tempCost = 0;
foreach($taskService->lingTasks as $lt)
{
	foreach($thisProject[$lt->sourceLang][$lt->targLang]['linguistTasks'] as $tpLT)
	{
		if ($lt->ltask->id == $tpLT->get_id())
		{
			if ($tpLT->get_buyUnits() == 'hours')
			{
				$lt->ltask->workRequired = $tpLT->get_workRequired();
				$lt->wordRateDetails->hourly = $tpLT->get_hourlyRate();
				$tempCost += $lt->ltask->workRequired * $lt->wordRateDetails->hourly;
			}
			else
			{
				if (($tpLT->get_taskonly_name() == 'TR+CE') || ($tpLT->get_taskonly_name() == 'TR/CE'))
				{
					$tempCost += $lt->wordRateDetails->trce_new * $lt->wordCounts->newWords;
					$tempCost += $lt->wordRateDetails->trce_fuzzy * $lt->wordCounts->fuzzyWords;
					$tempCost += $lt->wordRateDetails->trce_100Match * $lt->wordCounts->matchRepsWords;
				}
				else
				{
					$tempCost += $lt->wordRateDetails->tr_new * $lt->wordCounts->newWords;
					$tempCost += $lt->wordRateDetails->tr_fuzzy * $lt->wordCounts->fuzzyWords;
					$tempCost += $lt->wordRateDetails->tr_100Match * $lt->wordCounts->matchRepsWords;
				}
				
			}
			break;
		}
	}
	

}
foreach($taskService->billableTasks as $bt)
{
	if ($bt->distributionStrategy == 'unevenly')  //then this is a special case and we need to find them all
	{
		foreach($thisProject as $srcLang => $bySource)
		{
			if ($srcLang != 'nonDistributed')
			{
				foreach($bySource as $tgtLang => $byTarget)
				{
					foreach($byTarget['billableTasks'] as $tpBillTask)
					{
						if ($bt->btask->id == $tpBillTask->get_id())
						{
							$bt->btask->workRequired = $tpBillTask->get_workRequired();
							$tempCost += $bt->btask->workRequired * $bt->hourlyRate;
							break;
						}
					}
				}
			}
		}
	}
	else
	{
		if (($bt->btask->type =='Document QA Specialist') || ($bt->btask->type == 'Project Manager'))
		{
			$found = false;
			foreach($thisProject['nonDistributed'] as $nonDistTask)
			{
				if ($nonDistTask->get_id() == $bt->btask->id)
				{
					$found = true;
					$bt->btask->workRequired = $nonDistTask->get_workRequired();
					$tempCost += $bt->btask->workRequired * $bt->hourlyRate;
					break;
				}
			}
			if ($found == false)
			{
				//now we have to look through the languages to find all occurances of the task
				$distHours = 0;
				foreach($thisProject as $srcLang => $bySource)
				{
					if ($srcLang != 'nonDistributed')
					{
						foreach($bySource as $tgtLang => $byTarget)
						{
							foreach($byTarget['billableTasks'] as $tpBillTask)
							{
								if ($bt->btask->id == $tpBillTask->get_id())
								{
									$distHours += $tpBillTask->get_workRequired();
									break;
								}
							}
						}
					}
				}
				$bt->btask->workRequired = $distHours;
				$tempCost += $bt->btask->workRequired * $bt->hourlyRate;
			}
		}
		else
		{
			$tempCost += $bt->btask->workRequired * $bt->hourlyRate;
		}
	}

}


$rushFee = $projectData->rushFee($thisProject);
$discount = $projectData->get_discount($thisProject);

$projectObj->budget = round($tempCost,2);

$taskService->discount = 0-$discount;
$taskService->rushFee = $rushFee;



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
	foreach($taskService->lingTasks as $lingTask)
	{
		if (!in_array($lingTask->targLang, $langArray ))
		{
			$langArray[] = $lingTask->targLang;
		}
	}
	
	//create root / header / ServicesTable / Row1(s)
	$langServs = array();
	foreach($taskService->lingTasks as $lingTask)
	{
		if ( (!in_array($lingTask->ltask->type, $langServs )) && ($lingTask->ltask->price > 0))
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
	foreach($taskService->billableTasks as $billTask)
	{
		if ($billTask->btask->price > 0)
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
	$lc = $projectObj->sponsor->firstName . " " . $projectObj->sponsor->lastName."\n";
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
	
	
	//end root / header / DescriptionTable 
	//end root / header 
	
	
	
	//create root / Body
	$Body = $doc->createElement("Body");
	$root->appendChild($Body);
	
	//create root / Body / totalProjectCost
	$totalProjectCost = $doc->createElement("totalProjectCost");
	$cost = $totPrice + $taskService->rushFee + $taskService->discount;
	$totalProjectCost->appendChild($doc->createTextNode("$".number_format(round($cost,2),2)));
	$Body->appendChild($totalProjectCost);
	//end root / Body / totalProjectCost
	
	//create root / Body / projectTimeline
	$projectTimeline = $doc->createElement("projectTimeline");
	if ($projectData->get_reqDevDate() == "")
		$projectTimeline->appendChild($doc->createTextNode("TBD"));
	else
		$projectTimeline->appendChild($doc->createTextNode("Requested delivery is ".$projectData->get_reqDevDate()));
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
	switch($projectData->get_billingCycle())
	{
		case 'progress': $cycle_str = $projectData->get_billingCycleOther(); break;
		case '50-50': $cycle_str = "50% at project start, 50% at project delivery"; break;
		case 'Project Start': $cycle_str = "At project start"; break;
		case 'On Delivery': $cycle_str = "At project delivery"; break;		
	}
	$billingCycle->appendChild($doc->createTextNode($cycle_str));
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
	
	
	//here we start looping through the project to create language tables/tasks/etc
	foreach ($thisProject as $srcLng => $bySource)
	{
		if ($srcLng != 'nonDistributed')
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
				
				//do the rolled up linguistic tasks (i.e. printable = false)
				$rolledUpPrice = 0;
				$subTotal = 0;
				$catTotal = 0;
				foreach($byTarget['linguistTasks'] as $task)
				{
					if ($task->get_printable() == false)
					{
						if ($task->get_newWords() != 0)
						{
							$rolledUpPrice += $task->asp('new');
							$subTotal += $task->asp('new');
							$catTotal += $task->asp('new');
						}
						if ($task->get_fuzzyWords() != 0)
						{
							$rolledUpPrice += $task->asp('fuzzy');
							$subTotal += $task->asp('fuzzy');
							$catTotal += $task->asp('fuzzy');
						}
						if ($task->get_matchRepsWords() != 0)
						{
							$rolledUpPrice += $task->asp('match');
							$subTotal += $task->asp('match');
							$catTotal += $task->asp('match');
						}
						if ($task->get_workRequired() != 0)
						{
							$rolledUpPrice += $task->asp('hourly');
							$subTotal += $task->asp('hourly');
							$catTotal += $task->asp('hourly');
						}
					}
				}
				if ($rolledUpPrice > 0)
				{
					$transRow = $doc->createElement("transRow");
					$LanguageTable->appendChild($transRow);
					$transTaskName = $doc->createElement("transTaskName");
					$transTaskName->appendChild($doc->createTextNode("Linguistic Work"));
					$transRow->appendChild($transTaskName);
					$transUnits = $doc->createElement("transUnits"); 
					$transRow->appendChild($transUnits);
					$transRate = $doc->createElement("transRate");
					$transRow->appendChild($transRate);
					$transCost = $doc->createElement("transCost");
					$transCost->appendChild($doc->createTextNode("$".number_format($rolledUpPrice,2)));
					$transRow->appendChild($transCost);
				}
				
				
				//do the individual linguistic tasks (i.e. printable = true)
				foreach($byTarget['linguistTasks'] as $task)
				{
					if ($task->get_printable() == true)
					{
						if ($task->get_sellUnits() == 'words')
						{
							if ($task->get_newWords() != 0)
							{
								//create root / Body / projects / projectSubForm / LanguageTable / transRow
								$transRow = $doc->createElement("transRow");
								$LanguageTable->appendChild($transRow);
								
								//create r / B / p / pSF / LT / tR / transTaskName
								$transTaskName = $doc->createElement("transTaskName");
								switch($task->get_type())
								{
									case "TR+CE": $taskName = "Translation and Copyediting"; break;
									case "TR": $taskName = "Translation"; break;
									case "CE": $taskName = "Copyediting"; break;
									default: $taskName = $task->get_type();
								}
								$transTaskName->appendChild($doc->createTextNode($taskName ."- New Text"));
								$transRow->appendChild($transTaskName);
								//end r / B / p / pSF / lT / tR / transTaskName
								
								//create r / B / p / pSF / LT / tR / transUnits
								$transUnits = $doc->createElement("transUnits"); 
								$transUnits->appendChild($doc->createTextNode(number_format($task->get_newWords()) . " words"));
								$transRow->appendChild($transUnits);
								//end r / B / p / pSF / lT / tR / transUnits
								
								//create r / B / p / pSF / lT / tR / transRate
								$transRate = $doc->createElement("transRate");
								$transRate->appendChild($doc->createTextNode("$".number_format($task->aspp('new'),2) . "/word"));
								$transRow->appendChild($transRate);
								//end r / B / p / pSF / lT / tR / transRate
								
								//create r / B / p / pSF / lT / tR / transCost
								$transCost = $doc->createElement("transCost");
								$transCost->appendChild($doc->createTextNode("$".number_format($task->asp('new'),2)));
								$transRow->appendChild($transCost);
								//end r / B / p / pSF / lT / tR / transCost
								//end r / B / p / pSF / lT / transRow
								$subTotal += $task->asp('new');
								$catTotal += $task->asp('new');
							}
							
							if ($task->get_fuzzyWords() != 0)
							{
								//create root / Body / projects / projectSubForm / LanguageTable / transRow
								$transRow = $doc->createElement("transRow");
								$LanguageTable->appendChild($transRow);
								
								//create r / B / p / pSF / LT / tR / transTaskName
								$transTaskName = $doc->createElement("transTaskName");
								$transTaskName->appendChild($doc->createTextNode($taskName ."- Fuzzy Text"));
								$transRow->appendChild($transTaskName);
								//end r / B / p / pSF / lT / tR / transTaskName
								
								//create r / B / p / pSF / LT / tR / transUnits
								$transUnits = $doc->createElement("transUnits"); 
								$transUnits->appendChild($doc->createTextNode(number_format($task->get_fuzzyWords()) . " words"));
								$transRow->appendChild($transUnits);
								//end r / B / p / pSF / lT / tR / transUnits
								
								//create r / B / p / pSF / lT / tR / transRate
								$transRate = $doc->createElement("transRate");
								$transRate->appendChild($doc->createTextNode("$".number_format($task->aspp('fuzzy'),2) . "/word"));
								$transRow->appendChild($transRate);
								//end r / B / p / pSF / lT / tR / transRate
								
								//create r / B / p / pSF / lT / tR / transCost
								$transCost = $doc->createElement("transCost");
								$transCost->appendChild($doc->createTextNode("$".number_format($task->asp('fuzzy'),2)));
								$transRow->appendChild($transCost);
								//end r / B / p / pSF / lT / tR / transCost
								//end r / B / p / pSF / lT / transRow
								$subTotal += $task->asp('fuzzy');
								$catTotal += $task->asp('fuzzy');
							}
							
							if ($task->get_matchRepsWords() != 0)
							{
								//create root / Body / projects / projectSubForm / LanguageTable / transRow
								$transRow = $doc->createElement("transRow");
								$LanguageTable->appendChild($transRow);
								
								//create r / B / p / pSF / LT / tR / transTaskName
								$transTaskName = $doc->createElement("transTaskName");
								$transTaskName->appendChild($doc->createTextNode($taskName ."- Repetition & 100% Matches"));
								$transRow->appendChild($transTaskName);
								//end r / B / p / pSF / lT / tR / transTaskName
								
								//create r / B / p / pSF / LT / tR / transUnits
								$transUnits = $doc->createElement("transUnits"); 
								$transUnits->appendChild($doc->createTextNode(number_format($task->get_matchRepsWords()) . " words"));
								$transRow->appendChild($transUnits);
								//end r / B / p / pSF / lT / tR / transUnits
								
								//create r / B / p / pSF / lT / tR / transRate
								$transRate = $doc->createElement("transRate");
								$transRate->appendChild($doc->createTextNode("$".number_format($task->aspp('match'),2) . "/word"));
								$transRow->appendChild($transRate);
								//end r / B / p / pSF / lT / tR / transRate
								
								//create r / B / p / pSF / lT / tR / transCost
								$transCost = $doc->createElement("transCost");
								$transCost->appendChild($doc->createTextNode("$".number_format($task->asp('match'),2)));
								$transRow->appendChild($transCost);
								//end r / B / p / pSF / lT / tR / transCost
								//end r / B / p / pSF / lT / transRow
								$subTotal += $task->asp('match');
								$catTotal += $task->asp('match');
							}
						}
						else
						{
						
							if ($task->get_workRequired() != 0)
							{
								//create root / Body / projects / projectSubForm / LanguageTable / transRow
								$transRow = $doc->createElement("transRow");
								$LanguageTable->appendChild($transRow);
								
								//create r / B / p / pSF / LT / tR / transTaskName
								$transTaskName = $doc->createElement("transTaskName");
								switch($task->get_type())
								{
									case "PR": $taskName = "Proofreading"; break;
									case "OLR": $taskName = "Online Review"; break;
									default: $taskName = $task->get_name();
								}
								$transTaskName->appendChild($doc->createTextNode($taskName));
								$transRow->appendChild($transTaskName);
								//end r / B / p / pSF / lT / tR / transTaskName
								
								//create r / B / p / pSF / LT / tR / transUnits
								$transUnits = $doc->createElement("transUnits"); 
								$transUnits->appendChild($doc->createTextNode($task->get_workRequired() . " hours"));
								$transRow->appendChild($transUnits);
								//end r / B / p / pSF / lT / tR / transUnits
								
								//create r / B / p / pSF / lT / tR / transRate
								$transRate = $doc->createElement("transRate");
								$transRate->appendChild($doc->createTextNode("$".number_format($task->aspp('hourly'),2) . "/hour"));
								$transRow->appendChild($transRate);
								//end r / B / p / pSF / lT / tR / transRate
								
								//create r / B / p / pSF / lT / tR / transCost
								$transCost = $doc->createElement("transCost");
								$transCost->appendChild($doc->createTextNode("$".number_format($task->asp('hourly'),2)));
								$transRow->appendChild($transCost);
								//end r / B / p / pSF / lT / tR / transCost
								//end r / B / p / pSF / lT / transRow
								$subTotal += $task->asp('hourly');
								$catTotal += $task->asp('hourly');
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
				
				
				
				$catTotal = 0;
				$rolledUpPrice = 0;
				$rolledUpUnits = 0;
				$rolledUpRate = 0;
				$rolledUpCount = 0;
				//find any rolled up dtp tasks
				foreach ($byTarget['billableTasks'] as $task)
				{
					if ($task->get_type() == 'Formatting Specialist')
					{
						if ($task->get_printable() == false)
						{
							$rolledUpPrice += $task->asp();
							$subTotal += $task->asp();
							$catTotal += $task->asp();
							
							$rolledUpUnits += $task->get_workRequired();
							$rolledUpRate += $task->aspp();
							$rolledUpCount++;
						}
					}
				}
				
				$rolledUpRate = number_format($rolledUpRate/$rolledUpCount,2);
				if ($rolledUpPrice > 0)
				{
					$dtpRow = $doc->createElement("dtpRow");
					$LanguageTable->appendChild($dtpRow);
					$dtpTaskName = $doc->createElement("dtpTaskName");
					$dtpTaskName->appendChild($doc->createTextNode("Formatting"));
					$dtpRow->appendChild($dtpTaskName);
					$dtpUnits = $doc->createElement("dtpUnits"); 
					$dtpUnits->appendChild($doc->createTextNode($rolledUpUnits . " hours"));
					$dtpRow->appendChild($dtpUnits);
					$dtpRate = $doc->createElement("dtpRate");
					$dtpRate->appendChild($doc->createTextNode("$".$rolledUpRate."/hour"));
					$dtpRow->appendChild($dtpRate);
					$dtpCost = $doc->createElement("dtpCost");
					$dtpCost->appendChild($doc->createTextNode("$".number_format($rolledUpPrice,2)));
					$dtpRow->appendChild($dtpCost);
				}
				//find any non-rolledup distributed dtp tasks and make a new row
				foreach ($byTarget['billableTasks'] as $task)
				{
					if ($task->get_printable() == true)
						{
						if (($task->get_type() == 'Formatting Specialist') && ($task->asp() > 0))
						{
							$dtpRow = $doc->createElement('dtpRow');
							$LanguageTable->appendChild($dtpRow);
							
							$dtpTaskName = $doc->createElement('dtpTaskName');
							$dtpTaskName->appendChild($doc->createTextNode($task->get_name()));
							$dtpRow->appendChild($dtpTaskName);
							
							$dtpUnits = $doc->createElement('dtpUnits');
							if ((get_class($task) == 'dtpTask') && ($task->get_sellUnits() == 'pages'))
							{
								$dtpUnits->appendChild($doc->createTextNode($task->get_numPages() . " pages"));
								$rateUnits = "page";
							}
							else
							{
								$dtpUnits->appendChild($doc->createTextNode($task->get_workRequired() . " hours"));
								$rateUnits = "hour";
							}
							$dtpRow->appendChild($dtpUnits);
							
							$dtpRate = $doc->createElement('dtpRate');
							$dtpRate->appendChild($doc->createTextNode("$".number_format($task->aspp(),2)."/".$rateUnits));
							$dtpRow->appendChild($dtpRate);
							
							$dtpCost = $doc->createElement('dtpCost');
							$dtpCost->appendChild($doc->createTextNode("$".number_format($task->asp(),2)));
							$dtpRow->appendChild($dtpCost);
							
							$catTotal += $task->asp();
							$subTotal += $task->asp();
						}
					}
				}
				
				if($catTotal > 0)
				{
					$dtpHeader = $doc->createElement("dtpHeader");
					$LanguageTable->appendChild($dtpHeader);
					$dtpFooter = $doc->createElement('dtpFooter');
					$dtpSubTotal = $doc->createElement('dtpSubTotal');
					$dtpSubTotal->appendChild($doc->createTextNode("$".number_format($catTotal,2)));
					$dtpFooter->appendChild($dtpSubTotal);
					$LanguageTable->appendChild($dtpFooter);
				}
				
								
				$catTotal = 0;
				$rolledUpPrice = 0;
				$rolledUpUnits = 0;
				$rolledUpRate = 0;
				$rolledUpCount = 0;
				//find any rolled up engineering tasks
				foreach ($byTarget['billableTasks'] as $task)
				{
					if ($task->get_type() == 'Localization Engineer')
					{
						if ($task->get_printable() == false)
						{
							$rolledUpPrice += $task->asp();
							$subTotal += $task->asp();
							$catTotal += $task->asp();
							
							$rolledUpUnits += $task->get_workRequired();
							$rolledUpRate += $task->aspp();
							$rolledUpCount++;
						}
					}
				}
				
				if ($rolledUpCount == 0)
					$rolledUpRate = number_format(0,2);
				else
					$rolledUpRate = number_format($rolledUpRate/$rolledUpCount,2);
				if ($rolledUpPrice > 0)
				{
					$engRow = $doc->createElement("engRow");
					$LanguageTable->appendChild($engRow);
					$engTaskName = $doc->createElement("engTaskName");
					$engTaskName->appendChild($doc->createTextNode("Engineering"));
					$engRow->appendChild($engTaskName);
					$engUnits = $doc->createElement("engUnits"); 
					$engUnits->appendChild($doc->createTextNode($rolledUpUnits . " hours"));
					$engRow->appendChild($engUnits);
					$engRate = $doc->createElement("engRate");
					$engRate->appendChild($doc->createTextNode("$".$rolledUpRate."/hour"));
					$engRow->appendChild($engRate);
					$engCost = $doc->createElement("engCost");
					$engCost->appendChild($doc->createTextNode("$".number_format($rolledUpPrice,2)));
					$engRow->appendChild($engCost);
				}
				//find any non-rolledup distributed engineering tasks and make a new row
				$tmUnits = 0;
				$tmCost = 0;
				$tmRate = 0;
				$tmCount = 0;
				foreach ($byTarget['billableTasks'] as $task)
				{
					if ($task->get_type() == 'Localization Engineer')
					{
						if ($task->get_name() != "TM Work")
						{
							if (($task->get_printable() == true) && ($task->asp() > 0))
							{
								$engRow = $doc->createElement('engRow');
								$LanguageTable->appendChild($engRow);
								
								$engTaskName = $doc->createElement('engTaskName');
								$engTaskName->appendChild($doc->createTextNode($task->get_name()));
								$engRow->appendChild($engTaskName);
								
								$engUnits = $doc->createElement('engUnits');
								$engUnits->appendChild($doc->createTextNode($task->get_workRequired() . " hours"));
								$engRow->appendChild($engUnits);
								
								$engRate = $doc->createElement('engRate');
								$engRate->appendChild($doc->createTextNode("$".number_format($task->aspp(),2)."/hour"));
								$engRow->appendChild($engRate);
								
								$engCost = $doc->createElement('engCost');
								$engCost->appendChild($doc->createTextNode("$".number_format($task->asp(),2)));
								$engRow->appendChild($engCost);
								
								$catTotal += $task->asp();
								$subTotal += $task->asp();
							}
						}
						else
						{
							if (($task->get_printable() == true) && ($task->asp() > 0))
							{
								$tmUnits += $task->get_workRequired();
								$tmRate += $task->aspp();
								$tmCost += $task->asp();
								$tmCount++;
								$catTotal += $task->asp();
								$subTotal += $task->asp();
							}
						}
            		}
				}
				
				if ($tmCount > 0)
				{
					$engRow = $doc->createElement('engRow');
					$LanguageTable->appendChild($engRow);
					
					$engTaskName = $doc->createElement('engTaskName');
					$engTaskName->appendChild($doc->createTextNode('Translation Memory Maintenance'));
					$engRow->appendChild($engTaskName);
					
					$engUnits = $doc->createElement('engUnits');
					$engUnits->appendChild($doc->createTextNode($tmUnits . " hours"));
					$engRow->appendChild($engUnits);
					
					$engRate = $doc->createElement('engRate');
					$tempRate = $tmRate / $tmCount;
					$engRate->appendChild($doc->createTextNode("$".number_format($tempRate,2)."/hour"));
					$engRow->appendChild($engRate);
					
					$engCost = $doc->createElement('engCost');
					$engCost->appendChild($doc->createTextNode("$".number_format($tmCost,2)));
					$engRow->appendChild($engCost);
				}
				
				if($catTotal > 0)
				{
					$engHeader = $doc->createElement("engHeader");
					$LanguageTable->appendChild($engHeader);
					$engFooter = $doc->createElement('engFooter');
					$engSubTotal = $doc->createElement('engSubTotal');
					$engSubTotal->appendChild($doc->createTextNode("$".number_format($catTotal,2)));
					$engFooter->appendChild($engSubTotal);
					$LanguageTable->appendChild($engFooter);
				}
				
				$catTotal = 0;
				$rolledUpPrice = 0;
				$rolledUpUnits = 0;
				$rolledUpRate = 0;
				$rolledUpCount = 0;
				//find any rolled up qa tasks
				foreach ($byTarget['billableTasks'] as $task)
				{
					if ($task->get_type() == 'Document QA Specialist')
					{
						if ($task->get_printable() == false)
						{
							$rolledUpPrice += $task->asp();
							$subTotal += $task->asp();
							$catTotal += $task->asp();
							
							$rolledUpUnits += $task->get_workRequired();
							$rolledUpRate += $task->aspp();
							$rolledUpCount++;
						}
					}
				}
				
				$rolledUpRate = number_format($rolledUpRate/$rolledUpCount,2);
				if ($rolledUpPrice > 0)
				{
					$qaRow = $doc->createElement("qaRow");
					$LanguageTable->appendChild($qaRow);
					
					$qaTaskName = $doc->createElement("qaTaskName");
					$qaTaskName->appendChild($doc->createTextNode("Quality Assurance"));
					$qaRow->appendChild($qaTaskName);
					
					$qaUnits = $doc->createElement("qaUnits"); 
					$qaUnits->appendChild($doc->createTextNode($rolledUpUnits . " hours"));
					$qaRow->appendChild($qaUnits);
					
					$qaRate = $doc->createElement("qaRate");
					$qaRate->appendChild($doc->createTextNode("$".$rolledUpRate."/hour"));
					$qaRow->appendChild($qaRate);
					
					$qaCost = $doc->createElement("qaCost");
					$qaCost->appendChild($doc->createTextNode("$".number_format($rolledUpPrice,2)));
					$qaRow->appendChild($qaCost);
				}
				//find any non-rolledup distributed engineering tasks and make a new row
				foreach ($byTarget['billableTasks'] as $task)
				{
					if ($task->get_type() == 'Document QA Specialist')
					{
						if (($task->get_printable() == true) && ($task->asp() > 0))
						{
							$qaRow = $doc->createElement('qaRow');
							$LanguageTable->appendChild($qaRow);
							
							$qaTaskName = $doc->createElement('qaTaskName');
							$qaTaskName->appendChild($doc->createTextNode($task->get_name()));
							$qaRow->appendChild($qaTaskName);
							
							$qaUnits = $doc->createElement('qaUnits');
							$qaUnits->appendChild($doc->createTextNode($task->get_workRequired() . " hours"));
							$qaRow->appendChild($qaUnits);
							
							$qaRate = $doc->createElement('qaRate');
							$qaRate->appendChild($doc->createTextNode("$".number_format($task->aspp(),2)."/hour"));
							$qaRow->appendChild($qaRate);
							
							$qaCost = $doc->createElement('qaCost');
							$qaCost->appendChild($doc->createTextNode("$".number_format($task->asp(),2)));
							$qaRow->appendChild($qaCost);
							
							$catTotal += $task->asp();
							$subTotal += $task->asp();
						}
            		}
				}
				
				
				if($catTotal > 0)
				{
					$qaHeader = $doc->createElement("qaHeader");
					$LanguageTable->appendChild($qaHeader);
					$qaFooter = $doc->createElement('qaFooter');
					$qaSubTotal = $doc->createElement('qaSubTotal');
					$qaSubTotal->appendChild($doc->createTextNode("$".number_format($catTotal,2)));
					$qaFooter->appendChild($qaSubTotal);
					$LanguageTable->appendChild($qaFooter);
				}
				
				//create root / Body / projects / projectSubFor / LanguageTable / pmHeader
				$pmHeader = $doc->createElement("pmHeader");
				$LanguageTable->appendChild($pmHeader);
				
				foreach ($byTarget['billableTasks'] as $task)
				{
					if ($task->get_type() == 'Project Manager')
					{
						if (($task->get_printable() == true) && ($task->aspByLanguage($thisProject,$task->get_targLang()) > 0))
						{
							$pmRow = $doc->createElement('pmRow');
							$LanguageTable->appendChild($pmRow);
							
							$pmCost = $doc->createElement('pmCost');
							$pmCost->appendChild($doc->createTextNode("$".number_format($task->aspByLanguage($thisProject,$task->get_targLang()),2)));
							$pmRow->appendChild($pmCost);
							
							$catTotal += $task->aspByLanguage($thisProject,$task->get_targLang());
							$subTotal += $task->aspByLanguage($thisProject,$task->get_targLang());
						}
            		}
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
				
				
			}
		}	//end !nondstributed if
	}
		
		if (count($thisProject['nonDistributed']) > 0)
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
			$rolledUpPrice = 0;
			$rolledUpUnits = 0;
			$rolledUpRate = 0;
			$rolledUpCount = 0;
			
			//find any rolled up tasks
			foreach ($thisProject['nonDistributed'] as $task)
			{
				if ($task->get_printable() == false)
				{
					$rolledUpPrice += $task->asp();
					$subTotal += $task->asp();
					$catTotal += $task->asp();
					
					$rolledUpUnits += $task->get_workRequired();
					$rolledUpRate += $task->aspp();
					$rolledUpCount++;
				}

			}
			
			if ($rolledUpCount == 0)
				$rolledUpRate = number_format(0,2);
			else
				$rolledUpRate = number_format($rolledUpRate/$rolledUpCount,2);
			if ($rolledUpPrice > 0)
			{
				$otherRow = $doc->createElement("otherRow");
				$IndependentTable->appendChild($otherRow);
				
				$otherTaskName = $doc->createElement("otherTaskName");
				$otherTaskName->appendChild($doc->createTextNode("Other Services"));
				$otherRow->appendChild($otherTaskName);
				
				$otherUnits = $doc->createElement("otherUnits"); 
				$otherUnits->appendChild($doc->createTextNode($rolledUpUnits . " hours"));
				$otherRow->appendChild($otherUnits);
				
				$otherRate = $doc->createElement("otherRate");
				$otherRate->appendChild($doc->createTextNode("$".$rolledUpRate."/hour"));
				$otherRow->appendChild($otherRate);
				
				$otherCost = $doc->createElement("otherCost");
				$otherCost->appendChild($doc->createTextNode("$".number_format($rolledUpPrice,2)));
				$otherRow->appendChild($otherCost);
			}
			//find any non-distributed dtp tasks and make a new row
			foreach ($thisProject['nonDistributed'] as $task)
			{
				if ($task->get_type() == "Formatting Specialist")
				{
					if (($task->get_printable() == true) && ($task->asp() > 0))
					{
						//create root / Body / projects / projectSubFor / IndependentTable / dtpRow
						$dtpRow = $doc->createElement("dtpRow");
						$IndependentTable->appendChild($dtpRow);
						
						//create r / B / p / pSF / IndependentTable / dR / dtpTaskName
						$dtpTaskName = $doc->createElement("dtpTaskName");
						$dtpTaskName->appendChild($doc->createTextNode($task->get_name()));
						$dtpRow->appendChild($dtpTaskName);
						//end r / B / p / pSF / IndependentTable / dR / dtpTaskName
						
						//create r / B / p / pSF / IndependentTable / dR / dtpUnits
						$dtpUnits = $doc->createElement("dtpUnits");
						$dtpUnits->appendChild($doc->createTextNode($task->get_workRequired() . " hours"));
						$dtpRow->appendChild($dtpUnits);
						//end r / B / p / pSF / IndependentTable / dR / dtpUnits
						
						//create r / B / p / pSF / IndependentTable / dR / dtpRate
						$dtpRate = $doc->createElement("dtpRate");
						$dtpRate->appendChild($doc->createTextNode("$".number_format($task->aspp(),2) . "/hour"));
						$dtpRow->appendChild($dtpRate);
						//end r / B / p / pSF / IndependentTable / dR / dtpRate
						
						//create r / B / p / pSF / IndependentTable / dR / dtpCost
						$dtpCost = $doc->createElement("dtpCost");
						$dtpCost->appendChild($doc->createTextNode("$".number_format($task->asp(),2)));
						$dtpRow->appendChild($dtpCost);
						//end r / B / p / pSF / IndependentTable / dR / dtpCost
						$subTotal += $task->asp();
						$catTotal += $task->asp();
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
			
			
			
			//find any non-distributed eng tasks and make a new row
			foreach ($thisProject['nonDistributed'] as $task)
			{
				if ($task->get_type() == "Localization Engineer")
				{
					if (($task->get_printable() == true) && ($task->asp() > 0))
					{
						//create root / Body / projects / projectSubFor / IndependentTable / engRow
						$engRow = $doc->createElement("engRow");
						$IndependentTable->appendChild($engRow);
						
						//create r / B / p / pSF / IndependentTable / dR / engTaskName
						$engTaskName = $doc->createElement("engTaskName");
						$engTaskName->appendChild($doc->createTextNode($task->get_name()));
						$engRow->appendChild($engTaskName);
						//end r / B / p / pSF / IndependentTable / dR / engTaskName
						
						//create r / B / p / pSF / IndependentTable / dR / engUnits
						$engUnits = $doc->createElement("engUnits");
						$engUnits->appendChild($doc->createTextNode($task->get_workRequired() . " hours"));
						$engRow->appendChild($engUnits);
						//end r / B / p / pSF / IndependentTable / dR / engUnits
						
						//create r / B / p / pSF / IndependentTable / dR / engRate
						$engRate = $doc->createElement("engRate");
						$engRate->appendChild($doc->createTextNode("$".number_format($task->aspp(),2) . "/hour"));
						$engRow->appendChild($engRate);
						//end r / B / p / pSF / IndependentTable / dR / engRate
						
						//create r / B / p / pSF / IndependentTable / dR / engCost
						$engCost = $doc->createElement("engCost");
						$engCost->appendChild($doc->createTextNode("$".number_format($task->asp(),2)));
						$engRow->appendChild($engCost);
						//end r / B / p / pSF / IndependentTable / dR / engCost
						$subTotal += $task->asp();
						$catTotal += $task->asp();
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
			
			//find any non-distributed pm tasks and make a new row
			foreach ($thisProject['nonDistributed'] as $task)
			{
				if ($task->get_type() == "Project Manager")
				{
					if (($task->get_printable() == true) && ($task->asp($thisProject) > 0))
					{
						//create r / B / p / pSF / IndependentTable / dR / pmCost
						$pmRow = $doc->createElement("pmRow");
						$IndependentTable->appendChild($pmRow);
						
						$pmCost = $doc->createElement("pmCost");
						$pmASP = $task->asp($thisProject);
						$pmCost->appendChild($doc->createTextNode("$".number_format($pmASP,2)));
						$pmRow->appendChild($pmCost);
						//end r / B / p / pSF / IndependentTable / dR / pmCost
						$subTotal += $pmASP;
						$catTotal += $pmASP;
					}
				}

			}
			
			//end root / Body / projects / projectSubFor / IndependentTable / pmRow
			
			$catTotal = 0;
			//find any non-distributed other tasks and make a new row
			foreach ($thisProject['nonDistributed'] as $task)
			{
				
				if (($task->get_type() != "Formatting Specialist") &&
					($task->get_type() != "Localization Engineer") &&
					($task->get_type() != "Project Manager"))
				{
					if (($task->get_printable() == true) && ($task->asp() > 0))
					{
						//create root / Body / projects / projectSubFor / IndependentTable / otherRow
						$otherRow = $doc->createElement("otherRow");
						$IndependentTable->appendChild($otherRow);
						
						//create r / B / p / pSF / IndependentTable / dR / otherTaskName
						$otherTaskName = $doc->createElement("otherTaskName");
						$otherTaskName->appendChild($doc->createTextNode($task->get_name()));
						$otherRow->appendChild($otherTaskName);
						//end r / B / p / pSF / IndependentTable / dR / otherTaskName
						
						//create r / B / p / pSF / IndependentTable / dR / otherUnits
						$otherUnits = $doc->createElement("otherUnits");
						$otherUnits->appendChild($doc->createTextNode($task->get_workRequired() . " hours"));
						$otherRow->appendChild($otherUnits);
						//end r / B / p / pSF / IndependentTable / dR / otherUnits
						
						//create r / B / p / pSF / IndependentTable / dR / otherRate
						$otherRate = $doc->createElement("otherRate");
						$otherRate->appendChild($doc->createTextNode("$".number_format($task->aspp(),2) . "/hour"));
						$otherRow->appendChild($otherRate);
						//end r / B / p / pSF / IndependentTable / dR / otherRate
						
						//create r / B / p / pSF / IndependentTable / dR / otherCost
						$otherCost = $doc->createElement("otherRate");
						$otherCost->appendChild($doc->createTextNode("$".number_format($task->asp(),2)));
						$otherRow->appendChild($otherCost);
						//end r / B / p / pSF / IndependentTable / dR / otherCost
						$subTotal += $task->asp();
						$catTotal += $task->asp();
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


$taskservice = $doc->createElement('taskservice');
$taskservice->appendChild($doc->createCDATASection(str_replace("\0", "{[{NULL}]}",serialize($taskService))));
$datadump->appendChild($taskservice);

$projectobj = $doc->createElement('projectobj');
$projectobj->appendChild($doc->createCDATASection(str_replace("\0", "{[{NULL}]}",serialize($projectObj))));
$datadump->appendChild($projectobj);

$projectdata = $doc->createElement('projectdata');
$projectdata->appendChild($doc->createCDATASection(str_replace("\0", "{[{NULL}]}",serialize($projectData))));
$datadump->appendChild($projectdata);

$langcount = $doc->createElement('langcount');
$langcount->appendChild($doc->createCDATASection(str_replace("\0", "{[{NULL}]}",serialize($langCount))));
$datadump->appendChild($langcount);

$thisproject = $doc->createElement('thisproject');
$thisproject->appendChild($doc->createCDATASection(str_replace("\0", "{[{NULL}]}",serialize($thisProject))));
$datadump->appendChild($thisproject);
	
	

$filename = $projectObj->name . "_Quote.xml";
$filename = str_replace(" ","_",$filename);

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=$filename");

//$result = $doc->save($relativepath);

echo $doc->saveXML() . "\n";
}

?>
