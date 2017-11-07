<?PHP
require_once("class_contact.php");
require_once("class_estimate.php");
require_once("class_language.php");
require_once("class_pm_task.php");
require_once("class_split_task.php");
require_once("class_task.php");

//check to see if we're logged in
session_start();
require_once('uuid.php');

if (!isset($_SESSION['userID']))
{
	header('location:login.php?location=ballpark');
	exit;
}
elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID))
{
	header('location:login.php?err=6&location=ballpark');
	exit;
}


$languageTbl = $_SESSION['languageTbl'];
$estimateObj = $_SESSION['estimate'];
$lingoContact = $_SESSION['lingoContact'];
$lingoPM = $_SESSION['pm'];



$projectTotalCost = 0;

/*$taskService = unserialize($_SESSION['taskService']);
$project = unserialize($_SESSION['project']);
$projectData = unserialize($_SESSION['projectData']);
$langCount = $_SESSION['langCount'];
$thisProject = unserialize($_SESSION['thisProject']);
*/




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
	$QuoteCompany->appendChild($doc->createTextNode($estimateObj->get_projectName()));
	$Row1->appendChild($QuoteCompany);
	//end root / header / Table1 / Row1 / QuoteCompany
	//end root / header / Table1 / Row1 
	//end root / header / Table1 
	
	//create root / header / ProjectDescription
	$ProjectDescription = $doc->createElement("ProjectDescription");
	$ProjectDescription->appendChild($doc->createTextNode("Thank you for the opportunity to provide you with an estimate for localization services. We have estimated the scope of work based on the files provided by you as listed below, and the services requested:"));
	$header->appendChild($ProjectDescription);
	//end root / header / ProjectDescription
	
	//create root / header / ServicesTable
	$ServicesTable = $doc->createElement("ServicesTable");
	$header->appendChild($ServicesTable);
	
	//create a language array for use later
	//do not need to do this, the estimateObj
	//contains the info in targetLanguage array
	
	//create root / header / ServicesTable / Row1(s)
	$langServs = array();
	foreach ($estimateObj->get_services() as $reqService)
	{
		
		$Row1 = $doc->createElement("Row1");
		$ReqServ = $doc->createElement("ReqServ");
		$ReqServ->appendChild($doc->createTextNode($reqService));
		
		$Row1->appendChild($ReqServ);
		$ServicesTable->appendChild($Row1);
		
	}
	
	//end root / header / ServicesTable / Row1
	//end root / header / ServicesTable
	
	
	
	//create root / header / DescriptionTable
	$DescriptionTable = $doc->createElement("DescriptionTable");
	$header->appendChild($DescriptionTable);
	
	//create root / header / DescriptionTable / Row1
	$Row1 = $doc->createElement("Row1");
	$DescriptionTable->appendChild($Row1);
	
	//create root / header / DescriptionTable / Row1 / Company
	$Company = $doc->createElement("Company");	
	$Company->appendChild($doc->createTextNode($estimateObj->get_clientName()));
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
	$Row2->appendChild($ContactName);
	//end root / header / DescriptionTable / Row2 / ContactName
	
	//create root / header / DescriptionTable / Row2 / lingoContact
	$lContact = $doc->createElement("lingoContact");
	$rep = $lingoContact->get_name() ."\n". $lingoContact->get_title() ."\n". $lingoContact->get_phone() ."\n". $lingoContact->get_email();
	$lContact->appendChild($doc->createTextNode($rep));
	$Row2->appendChild($lContact);
	//end root / header / DescriptionTable / Row2 / lingoContact
	//end root / header / DescriptionTable / Row2
	
	//create root / header / DescriptionTable / Row3
	$Row3 = $doc->createElement("Row3");
	$DescriptionTable->appendChild($Row3);
	
	//create root / header / DescriptionTable / Row3 / SrcLang
	$SrcLang = $doc->createElement("SrcLang");
	
	$SrcLang->appendChild($doc->createTextNode($languageTbl[0]->get_sourceLang()));
	$Row3->appendChild($SrcLang);
	//end root / header / DescriptionTable / Row3 / SrcLang
	
	//create root / header / DescriptionTable / Row4
	$Row4 = $doc->createElement("Row4");
	$DescriptionTable->appendChild($Row4);
	
	//create root / header / DescriptionTable / Row3 / TgtLanguages
	$TgtLanguages = $doc->createElement("TgtLanguages");
	$l = "";
	foreach ($estimateObj->get_targetLanguages() as $language)
	{
		$l .= $language . "\n";
	}
	$l = substr($l,0,strlen($l)-1);   //remove the trailing return from the string
	$TgtLanguages->appendChild($doc->createTextNode($l));
	$Row4->appendChild($TgtLanguages);
	//end root / header / DescriptionTable / Row3 / TgtLanguages
	
	
	//end root / header / DescriptionTable 
	//end root / header 
	
	
	
	//create root / Body
	$Body = $doc->createElement("Body");
	$root->appendChild($Body);
	
	//create root / Body / totalProjectCost
	$totalProjectCost = $doc->createElement("totalProjectCost");
	
	$totprice = 0;
	$discountAmount = 0;
	$rushFeeAmount = 0;
	foreach($languageTbl as $language)
	{
		if ($language->get_error() != TRUE)
			$totprice += $language->sellprice();
	}
	if($estimateObj->get_rushfee())
	{
		$rushFeeAmount = $totprice * $estimateObj->get_rushFeeMultiplier();
	}
	if($estimateObj->get_discountType() != "none")
	{
		switch ($estimateObj->get_discountType())
		{
			case "percent":
				$discountPercent = $estimateObj->get_discountPercent()/100;
				$discountAmount = $totprice * $discountPercent;
				break;
			case "fixed":
				$discountAmount = $estimateObj->get_discountAmount();
				break;
		}
	}
	
	$totalProjectCost->appendChild($doc->createTextNode("$". number_format($totprice+$rushFeeAmount-$discountAmount,2)));
	$Body->appendChild($totalProjectCost);
	//end root / Body / totalProjectCost
	
	//create root / Body / projectTimeline
	$projectTimeline = $doc->createElement("projectTimeline");
	$projectTimeline->appendChild($doc->createTextNode("TBD"));
	$Body->appendChild($projectTimeline);
	//end root / Body / projectTimeline
	
		
	//create root / Body / projects
	$projects = $doc->createElement("projects");
	$Body->appendChild($projects);
	
	//create root / Body / projects / projectSubForm
	$projectSubForm = $doc->createElement("projectSubform");
	$projects->appendChild($projectSubForm);
	
	//create root / Body / projects / projectSubForm / ProjectName
	$ProjectName = $doc->createElement("ProjectName");
	$ProjectName->appendChild($doc->createTextNode($estimateObj->get_projectName()));
	$projectSubForm->appendChild($ProjectName);
	//end root / Body / projects / projectSubForm / ProjectName
	
	
	//here we start looping through the project to create language tables/tasks/etc
	foreach ($languageTbl as $language)
	{
		$subTotal =0;
		//create root / Body / projects / projectSubForm / LanguageTable
		$LanguageTable = $doc->createElement("LanguageTable");
		$projectSubForm->appendChild($LanguageTable);
		
		//create root / Body / projects / projectSubForm / LanguageTable / langTitle
		$langTitle = $doc->createElement("langTitle");
		$tableLanguage = $doc->createElement("tableLanguage");
		$tableLanguage->appendChild($doc->createTextNode($language->get_sourceLang()." to ".$language->get_targetLang()));
		$langTitle->appendChild($tableLanguage);
		$LanguageTable->appendChild($langTitle);
		//end root / Body / projects / projectSubForm / LanguageTable/ langTitle
		
		//create root / Body / projects / projectSubForm / LanguageTable / transHeader
		$transHeader = $doc->createElement("transHeader");
		$LanguageTable->appendChild($transHeader);
		//end root / Body / projects / projectSubForm / LanguageTable / transHeader
		
		//check for and print rolled up tasks
		if ((array_key_exists('linguistic', $language->get_rolledUpTasks())) && ($language->get_rolledUpTask_At('linguistic') > 0))
		{		
			$dtpRow = $doc->createElement("transRow");
			$LanguageTable->appendChild($dtpRow);
			$transTaskName = $doc->createElement("transTaskName");
			$transTaskName->appendChild($doc->createTextNode("Linguistic Work"));
			$dtpRow->appendChild($transTaskName);
			$transUnits = $doc->createElement('transUnits');
			$dtpRow->appendChild($transUnits);
			$transRate = $doc->createElement("transRate");
			$dtpRow->appendChild($transRate);
			$transCost = $doc->createElement("transCost");
			$transCost->appendChild($doc->createTextNode("$".number_format($language->get_rolledUpTask_At('linguistic'),2)));
			$dtpRow->appendChild($transCost);
		}
		
		
		//do the individual linguistic tasks (i.e. printable = true)
		$catTotal = 0;
		foreach($language->get_tasks() as $task)
		{
			if ( (($task->get_name() == "New Text") || ($task->get_name() == "Fuzzy Text") || ($task->get_name() == "Repetitions/100% Matches") || 
				  ($task->get_name() == "Online Review") || ($task->get_name() == "Glossary Development") || ($task->get_name() == "Review Leveraged Text") || 
				  ($task->get_name() == "Voiceover Talent") || ($task->get_name() == "Voiceover Recording") || ($task->get_name() == "Voiceover Editing/Mixing") || 
				  ($task->get_name() == "Voiceover Archiving") || ($task->get_name() == "Voiceover Shipping") || ($task->get_name() == "Voiceover Director") || 
				  ($task->get_name() == "Proofread")) && (($task->get_printable() == TRUE) && ($task->get_actualSellPrice() > 0)))
			{
			
				if ($task->isSplit() == "false")
				{
					$units = $task->get_costUnits();
					$unitType = $task->get_costUnitType();
				}
				else
				{
					$units = $task->get_sellUnits();
					$unitType = $task->get_sellUnitType();
				}
				
				if ( floor($units) != ($units))
					$units = number_format($units,2);
				else
					$units = number_format($units);
					
				$units = $units . " " . $unitType;
				
				//create root / Body / projects / projectSubForm / LanguageTable / transRow
				$transRow = $doc->createElement("transRow");
				$LanguageTable->appendChild($transRow);
				
				//create r / B / p / pSF / LT / tR / transTaskName
				$transTaskName = $doc->createElement("transTaskName");
				
				$transTaskName->appendChild($doc->createTextNode($task->get_name()));
				$transRow->appendChild($transTaskName);
				//end r / B / p / pSF / lT / tR / transTaskName
				
				//create r / B / p / pSF / LT / tR / transUnits
				$transUnits = $doc->createElement("transUnits"); 
				$transUnits->appendChild($doc->createTextNode($units));
				$transRow->appendChild($transUnits);
				//end r / B / p / pSF / lT / tR / transUnits
				
				//create r / B / p / pSF / lT / tR / transRate
				$transRate = $doc->createElement("transRate");
				$transRate->appendChild($doc->createTextNode("$".$task->get_actualSellPricePerUnit() . "/" .substr($unitType,0,strlen($unitType)-1)));
				$transRow->appendChild($transRate);
				//end r / B / p / pSF / lT / tR / transRate
				
				//create r / B / p / pSF / lT / tR / transCost
				$transCost = $doc->createElement("transCost");
				$transCost->appendChild($doc->createTextNode("$".number_format($task->get_actualSellPrice(),2)));
				$transRow->appendChild($transCost);
				//end r / B / p / pSF / lT / tR / transCost
				//end r / B / p / pSF / lT / transRow
				$subTotal += $task->get_actualSellPrice();
				$catTotal += $task->get_actualSellPrice();
				
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
		
		foreach($language->get_tasks() as $task)
		{
			if ( (($task->get_name() == "Formatting") || ($task->get_name() == "Graphic Design") || 
				  ($task->get_name() == "EM Cleanup") || ($task->get_name() == "PDF Creation") ||
				 ($task->get_name() == "Formatting Coordination")) && (($task->get_printable() == FALSE) && ($task->get_actualSellPrice() > 0)))
			{
			
				if ($task->isSplit() == "false")
				{

					$rolledUpUnits += $task->get_costUnits();
				}
				else
				{
					$rolledUpUnits += $task->get_sellUnits();
				}
				
				$rolledUpPrice += $task->get_actualSellPrice();
				$rolledUpCount++;
				
			}
				
		} 
		
		if ($rolledUpPrice > 0)
		{
			//create root / Body / projects / projectSubForm / LanguageTable / transRow
			$dtpRow = $doc->createElement("dtpRow");
			$LanguageTable->appendChild($dtpRow);
			
			//create r / B / p / pSF / LT / tR / transTaskName
			$dtpTaskName = $doc->createElement("dtpTaskName");
			
			$dtpTaskName->appendChild($doc->createTextNode("Formatting"));
			$dtpRow->appendChild($dtpTaskName);
			//end r / B / p / pSF / lT / tR / transTaskName
			
			//create r / B / p / pSF / LT / tR / transUnits
			$dtpUnits = $doc->createElement("dtpUnits"); 
			$dtpUnits->appendChild($doc->createTextNode($rolledUpUnits . "Hours"));
			$dtpRow->appendChild($dtpUnits);
			//end r / B / p / pSF / lT / tR / transUnits
			
			//create r / B / p / pSF / lT / tR / transRate
			$dtpRate = $doc->createElement("dtpRate");
			$rolledUpRate = number_format(round($rolledUpPrice / $rolledUpUnits),2);
			$dtpRate->appendChild($doc->createTextNode("$".$rolledUpRate . "/hour"));
			$dtpRow->appendChild($dtpRate);
			//end r / B / p / pSF / lT / tR / transRate
			
			//create r / B / p / pSF / lT / tR / transCost
			$dtpCost = $doc->createElement("dtpCost");
			$dtpCost->appendChild($doc->createTextNode("$".number_format($rolledUpPrice,2)));
			$dtpRow->appendChild($dtpCost);
			//end r / B / p / pSF / lT / tR / transCost
			//end r / B / p / pSF / lT / transRow
			$subTotal += $rolledUpPrice;
			$catTotal += $rolledUpPrice;
			
		}
		
		//find any non-rolledup distributed dtp tasks and make a new row
		foreach($language->get_tasks() as $task)
		{
			if ( (($task->get_name() == "Formatting") || ($task->get_name() == "Graphic Design") || 
				  ($task->get_name() == "EM Cleanup") || ($task->get_name() == "PDF Creation") ||
				 ($task->get_name() == "Formatting Coordination")) && (($task->get_printable() == TRUE) && ($task->get_actualSellPrice() > 0)))
			{
			
				if ($task->isSplit() == "false")
				{
					$units = $task->get_costUnits();
					$unitType = $task->get_costUnitType();
				}
				else
				{
					$units = $task->get_sellUnits();
					$unitType = $task->get_sellUnitType();
				}
				
				if ( floor($units) != ($units))
					$units = number_format($units,2);
				else
					$units = number_format($units);
					
				$units = $units . " " . $unitType;
				
				//create root / Body / projects / projectSubForm / LanguageTable / transRow
				$dtpRow = $doc->createElement("dtpRow");
				$LanguageTable->appendChild($dtpRow);
				
				//create r / B / p / pSF / LT / tR / transTaskName
				$dtpTaskName = $doc->createElement("dtpTaskName");
				
				$dtpTaskName->appendChild($doc->createTextNode($task->get_name()));
				$dtpRow->appendChild($dtpTaskName);
				//end r / B / p / pSF / lT / tR / transTaskName
				
				//create r / B / p / pSF / LT / tR / transUnits
				$dtpUnits = $doc->createElement("dtpUnits"); 
				$dtpUnits->appendChild($doc->createTextNode($units));
				$dtpRow->appendChild($dtpUnits);
				//end r / B / p / pSF / lT / tR / transUnits
				
				//create r / B / p / pSF / lT / tR / transRate
				$dtpRate = $doc->createElement("dtpRate");
				$dtpRate->appendChild($doc->createTextNode("$".$task->get_actualSellPricePerUnit() . "/" .substr($unitType,0,strlen($unitType)-1)));
				$dtpRow->appendChild($dtpRate);
				//end r / B / p / pSF / lT / tR / transRate
				
				//create r / B / p / pSF / lT / tR / transCost
				$dtpCost = $doc->createElement("dtpCost");
				$dtpCost->appendChild($doc->createTextNode("$".number_format($task->get_actualSellPrice(),2)));
				$dtpRow->appendChild($dtpCost);
				//end r / B / p / pSF / lT / tR / transCost
				//end r / B / p / pSF / lT / transRow
				$subTotal += $task->get_actualSellPrice();
				$catTotal += $task->get_actualSellPrice();
				
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
		foreach($language->get_tasks() as $task)
		{
			if ( (($task->get_name() == "TM Work") || ($task->get_name() == "File Treatment") || 
				  ($task->get_name() == "Senior Engineering") || ($task->get_name() == "UI Engineering") || ($task->get_name() == "Website Engineering") || 
				  ($task->get_name() == "Help Engineering") || ($task->get_name() == "Troubleshooting") || ($task->get_name() == "Functional QA") ||
				  ($task->get_name() == "CD/DVD Burning") || ($task->get_name() == "Test Script Development") || ($task->get_name() == "OLR - Lab") ||
				  ($task->get_name() == "Graphic Editing") || ($task->get_name() == "PDF Engineering") || 
				 ($task->get_name() == "Screen Capturing") || ($task->get_name() == 'Flash Engineering')) && (($task->get_printable() == FALSE) && ($task->get_actualSellPrice() > 0)))
			{
			
				if ($task->isSplit() == "false")
				{

					$rolledUpUnits += $task->get_costUnits();
				}
				else
				{
					$rolledUpUnits += $task->get_sellUnits();
				}
				
				$rolledUpPrice += $task->get_actualSellPrice();
				$rolledUpCount++;
				
			}
				
		} 
		
		if ($rolledUpPrice > 0)
		{
			//create root / Body / projects / projectSubForm / LanguageTable / transRow
			$dtpRow = $doc->createElement("engRow");
			$LanguageTable->appendChild($dtpRow);
			
			//create r / B / p / pSF / LT / tR / transTaskName
			$dtpTaskName = $doc->createElement("engTaskName");
			
			$dtpTaskName->appendChild($doc->createTextNode("Engineering"));
			$dtpRow->appendChild($dtpTaskName);
			//end r / B / p / pSF / lT / tR / transTaskName
			
			//create r / B / p / pSF / LT / tR / transUnits
			$dtpUnits = $doc->createElement("engUnits"); 
			$dtpUnits->appendChild($doc->createTextNode($rolledUpUnits . "Hours"));
			$dtpRow->appendChild($dtpUnits);
			//end r / B / p / pSF / lT / tR / transUnits
			
			//create r / B / p / pSF / lT / tR / transRate
			$dtpRate = $doc->createElement("engRate");
			$rolledUpRate = number_format(round($rolledUpPrice / $rolledUpUnits),2);
			$dtpRate->appendChild($doc->createTextNode("$".$rolledUpRate . "/hour"));
			$dtpRow->appendChild($dtpRate);
			//end r / B / p / pSF / lT / tR / transRate
			
			//create r / B / p / pSF / lT / tR / transCost
			$dtpCost = $doc->createElement("engCost");
			$dtpCost->appendChild($doc->createTextNode("$".number_format($rolledUpPrice,2)));
			$dtpRow->appendChild($dtpCost);
			//end r / B / p / pSF / lT / tR / transCost
			//end r / B / p / pSF / lT / transRow
			$subTotal += $rolledUpPrice;
			$catTotal += $rolledUpPrice;
			
		}
		
		//find any non-rolledup distributed engineering tasks and make a new row
		$tmUnits = 0;
		$tmCost = 0;
		$tmRate = 0;
		$tmCount = 0;
		foreach($language->get_tasks() as $task)
		{
			if ( (($task->get_name() == "TM Work") || ($task->get_name() == "File Treatment") || 
				  ($task->get_name() == "Senior Engineering") || ($task->get_name() == "UI Engineering") || ($task->get_name() == "Website Engineering") || 
				  ($task->get_name() == "Help Engineering") || ($task->get_name() == "Troubleshooting") || ($task->get_name() == "Functional QA") ||
				  ($task->get_name() == "CD/DVD Burning") || ($task->get_name() == "Test Script Development") || ($task->get_name() == "OLR - Lab") ||
				  ($task->get_name() == "Graphic Editing") || ($task->get_name() == "PDF Engineering") || 
				 ($task->get_name() == "Screen Capturing") || ($task->get_name() == 'Flash Engineering')) && (($task->get_printable() == TRUE) && ($task->get_actualSellPrice() > 0)))
			{
			
				if ($task->isSplit() == "false")
				{
					$units = $task->get_costUnits();
					$unitType = $task->get_costUnitType();
				}
				else
				{
					$units = $task->get_sellUnits();
					$unitType = $task->get_sellUnitType();
				}
				
				if ( floor($units) != ($units))
					$units = number_format($units,2);
				else
					$units = number_format($units);
					
				$units = $units . " " . $unitType;
				
				//create root / Body / projects / projectSubForm / LanguageTable / transRow
				$engRow = $doc->createElement("engRow");
				$LanguageTable->appendChild($engRow);
				
				//create r / B / p / pSF / LT / tR / transTaskName
				$engTaskName = $doc->createElement("engTaskName");
				
				$engTaskName->appendChild($doc->createTextNode($task->get_name()));
				$engRow->appendChild($engTaskName);
				//end r / B / p / pSF / lT / tR / transTaskName
				
				//create r / B / p / pSF / LT / tR / transUnits
				$engUnits = $doc->createElement("engUnits"); 
				$engUnits->appendChild($doc->createTextNode($units));
				$engRow->appendChild($engUnits);
				//end r / B / p / pSF / lT / tR / transUnits
				
				//create r / B / p / pSF / lT / tR / transRate
				$engRate = $doc->createElement("engRate");
				$engRate->appendChild($doc->createTextNode("$".$task->get_actualSellPricePerUnit() . "/" .substr($unitType,0,strlen($unitType)-1)));
				$engRow->appendChild($engRate);
				//end r / B / p / pSF / lT / tR / transRate
				
				//create r / B / p / pSF / lT / tR / transCost
				$engCost = $doc->createElement("engCost");
				$engCost->appendChild($doc->createTextNode("$".number_format($task->get_actualSellPrice(),2)));
				$engRow->appendChild($engCost);
				//end r / B / p / pSF / lT / tR / transCost
				//end r / B / p / pSF / lT / transRow
				$subTotal += $task->get_actualSellPrice();
				$catTotal += $task->get_actualSellPrice();
				
			}
				
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
		foreach($language->get_tasks() as $task)
		{
			if ( (($task->get_name() == "Quality Assurance") || ($task->get_name() == "QA Coordination") ) &&
				 (($task->get_printable() == FALSE) && ($task->get_actualSellPrice() > 0)))
			{
			
				if ($task->isSplit() == "false")
				{

					$rolledUpUnits += $task->get_costUnits();
				}
				else
				{
					$rolledUpUnits += $task->get_sellUnits();
				}
				
				$rolledUpPrice += $task->get_actualSellPrice();
				$rolledUpCount++;
				
			}
				
		} 
		
		if ($rolledUpPrice > 0)
		{
			//create root / Body / projects / projectSubForm / LanguageTable / transRow
			$dtpRow = $doc->createElement("qaRow");
			$LanguageTable->appendChild($dtpRow);
			
			//create r / B / p / pSF / LT / tR / transTaskName
			$dtpTaskName = $doc->createElement("qaTaskName");
			
			$dtpTaskName->appendChild($doc->createTextNode("Quality Assurance"));
			$dtpRow->appendChild($dtpTaskName);
			//end r / B / p / pSF / lT / tR / transTaskName
			
			//create r / B / p / pSF / LT / tR / transUnits
			$dtpUnits = $doc->createElement("qaUnits"); 
			$dtpUnits->appendChild($doc->createTextNode($rolledUpUnits . "Hours"));
			$dtpRow->appendChild($dtpUnits);
			//end r / B / p / pSF / lT / tR / transUnits
			
			//create r / B / p / pSF / lT / tR / transRate
			$dtpRate = $doc->createElement("qaRate");
			$rolledUpRate = number_format(round($rolledUpPrice / $rolledUpUnits),2);
			$dtpRate->appendChild($doc->createTextNode("$".$rolledUpRate . "/hour"));
			$dtpRow->appendChild($dtpRate);
			//end r / B / p / pSF / lT / tR / transRate
			
			//create r / B / p / pSF / lT / tR / transCost
			$dtpCost = $doc->createElement("qaCost");
			$dtpCost->appendChild($doc->createTextNode("$".number_format($rolledUpPrice,2)));
			$dtpRow->appendChild($dtpCost);
			//end r / B / p / pSF / lT / tR / transCost
			//end r / B / p / pSF / lT / transRow
			$subTotal += $rolledUpPrice;
			$catTotal += $rolledUpPrice;
			
		}
		
		
		//find any non-rolledup distributed engineering tasks and make a new row
		foreach($language->get_tasks() as $task)
		{
			if ( (($task->get_name() == "Quality Assurance") || ($task->get_name() == "QA Coordination") ) &&
				 (($task->get_printable() == TRUE) && ($task->get_actualSellPrice() > 0)))
			{
			
				if ($task->isSplit() == "false")
				{
					$units = $task->get_costUnits();
					$unitType = $task->get_costUnitType();
				}
				else
				{
					$units = $task->get_sellUnits();
					$unitType = $task->get_sellUnitType();
				}
				
				if ( floor($units) != ($units))
					$units = number_format($units,2);
				else
					$units = number_format($units);
					
				$units = $units . " " . $unitType;
				
				//create root / Body / projects / projectSubForm / LanguageTable / transRow
				$qaRow = $doc->createElement("qaRow");
				$LanguageTable->appendChild($qaRow);
				
				//create r / B / p / pSF / LT / tR / transTaskName
				$qaTaskName = $doc->createElement("qaTaskName");
				
				$qaTaskName->appendChild($doc->createTextNode($task->get_name()));
				$qaRow->appendChild($qaTaskName);
				//end r / B / p / pSF / lT / tR / transTaskName
				
				//create r / B / p / pSF / LT / tR / transUnits
				$qaUnits = $doc->createElement("qaUnits"); 
				$qaUnits->appendChild($doc->createTextNode($units));
				$qaRow->appendChild($qaUnits);
				//end r / B / p / pSF / lT / tR / transUnits
				
				//create r / B / p / pSF / lT / tR / transRate
				$qaRate = $doc->createElement("qaRate");
				$qaRate->appendChild($doc->createTextNode("$".$task->get_actualSellPricePerUnit() . "/" .substr($unitType,0,strlen($unitType)-1)));
				$qaRow->appendChild($qaRate);
				//end r / B / p / pSF / lT / tR / transRate
				
				//create r / B / p / pSF / lT / tR / transCost
				$qaCost = $doc->createElement("qaCost");
				$qaCost->appendChild($doc->createTextNode("$".number_format($task->get_actualSellPrice(),2)));
				$qaRow->appendChild($qaCost);
				//end r / B / p / pSF / lT / tR / transCost
				//end r / B / p / pSF / lT / transRow
				$subTotal += $task->get_actualSellPrice();
				$catTotal += $task->get_actualSellPrice();
				
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
		
		foreach($language->get_tasks() as $task)
		{
			if ( (($task->get_name() == "Project Management") || ($task->get_name() == "PDF Annotation") || ($task->get_name() == "Internationalization Consulting") ||
				  ($task->get_name() == "CMS Consulting") || ($task->get_name() == "Testing Services"))  
				  && ($task->get_printable() == TRUE) )
			{
				
			
				if (get_class($task) != "pmTask")
				{
					if ($task->isSplit() == "false")
					{
						$units = $task->get_costUnits();
						$unitType = $task->get_costUnitType();
					}
					else
					{
						$units = '';
						$unitType = '';
					}
					
					if ( floor($units) != ($units))
						$units = number_format($units,2);
					else
						$units = number_format($units);
						
					$units = $units . " " . $unitType;
					
					$asp = $task->get_actualSellPrice();
				}
				else
				{
					$asp = $task->get_actualSellPrice($language);
				}
				
				if ($asp > 0)
				{
			
					//create root / Body / projects / projectSubForm / LanguageTable / transRow
					$pmRow = $doc->createElement("pmRow");
					$LanguageTable->appendChild($pmRow);
					
					//create r / B / p / pSF / LT / tR / transTaskName
					$pmTaskName = $doc->createElement("pmTaskName");					
					$pmTaskName->appendChild($doc->createTextNode($task->get_name()));
					$pmRow->appendChild($pmTaskName);
					//end r / B / p / pSF / lT / tR / transTaskName
					
					//create r / B / p / pSF / LT / tR / transUnits
					$pmUnits = $doc->createElement("pmUnits"); 
					if ($task->get_name() != "Project Management")
						$pmUnits->appendChild($doc->createTextNode($units));
					$pmRow->appendChild($pmUnits);
					//end r / B / p / pSF / lT / tR / transUnits
					
					//create r / B / p / pSF / lT / tR / transRate
					$pmRate = $doc->createElement("pmRate");
					if ($task->get_name() != "Project Management")
						$pmRate->appendChild($doc->createTextNode($task->get_actualSellPricePerUnit()));
					$pmRow->appendChild($pmRate);
					//end r / B / p / pSF / lT / tR / transRate
					
					//create r / B / p / pSF / lT / tR / transCost
					$pmCost = $doc->createElement("pmCost");
					$pmCost->appendChild($doc->createTextNode("$".number_format($asp,2)));
					$pmRow->appendChild($pmCost);
					//end r / B / p / pSF / lT / tR / transCost
					//end r / B / p / pSF / lT / transRow
					$subTotal += $asp;
					$catTotal += $asp;
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


	
	
	
	
/*	
	//create the datadump
	$datadump = $doc->createElement('datadump');
	$root->appendChild($dataDump);
	
	//begin data dump / estimate object
	$estimate =  $doc->createElement('estimate');
	$dataDump->appendChild($estimate);
	
	$date = $doc->createElement('estdate');
	$date->appendChild($doc->createTextNode( $estimateObj->get_estimateDate() ));
	$estimate->appendChild($date);
	
	$clientName = $doc->createElement('clientname');
	$clientName->appendChild($doc->createTextNode( $estimateObj->get_clientName() ));
	$estimate->appendChild($clientName);
	
	$projectName = $doc->createElement('projectname');
	$projectName->appendChild($doc->createTextNode( $estimateObj->get_projectName() ));
	$estimate->appendChild($projectName);
	
	$projectType = $doc->createElement('projecttype');
	$projectType->appendChild($doc->createTextNode( $estimateObj->get_projectType() ));
	$estimate->appendChild($projectType);
	
	$fileType = $doc->createElement('filetype');
	$fileType->appendChild($doc->createTextNode( $estimateObj->get_fileType() ));
	$estimate->appendChild($fileType);
	
	$deliverable = $doc->createElement('deliverable');
	$deliverable->appendChild($doc->createTextNode( $estimateObj->get_deliverable() ));
	$estimate->appendChild($deliverable);
	
	$deliveryDate = $doc->createElement('deliverydate');
	$deliveryDate->appendChild($doc->createTextNode( $estimateObj->get_deliveryDate() ));
	$estimate->appendChild($deliveryDate);
	
	$rushFee = $doc->createElement('rushfee');
	$rushFee->appendChild($doc->createTextNode( $estimateObj->get_rushFee() ));
	$estimate->appendChild($rushFee);
	
	$rushFeeMultiplier = $doc->createElement('rushfeemultiplier');
	$rushFeeMultiplier->appendChild($doc->createTextNode( $estimateObj->get_rushFeeMultiplier() ));
	$estimate->appendChild($rushFeeMultiplier);
	
	$notes = $doc->createElement('notes');
	$notes->appendChild($doc->createTextNode( $estimateObj->get_notes() ));
	$estimate->appendChild($notes);
	
	$pages = $doc->createElement('pages');
	$pages->appendChild($doc->createTextNode( $estimateObj->get_pages() ));
	$estimate->appendChild($pages);
	
	$pagesPerHour = $doc->createElement('pagesperhour');
	$pagesPerHour->appendChild($doc->createTextNode( $estimateObj->get_pagesPerHour() ));
	$estimate->appendChild($pagesPerHour);
	
	$projectID = $doc->createElement('projectID');
	$projectID->appendChild($doc->createTextNode( $estimateObj->get_projectID() ));
	$estimate->appendChild($projectID);
	
	//begin data dump / estimate object / requested services
	$reqServices = $doc->createElement('requestedservices');
	$estimate->appendChild($reqServices);
	
	foreach ($estimateObj->get_services() as $service)
	{
		$temp = $doc->createElement('service');
		$temp->appendChild($doc->createTextNode($service));
		$reqServices->appendChild($temp);
	}
	
	//end data dump / estimate object / requested services
	
	$discountType = $doc->createElement('discounttype');
	$discountType->appendChild($doc->createTextNode( $estimateObj->get_discountType() ));
	$estimate->appendChild($discountType);
	
	$discountPercent = $doc->createElement('discountpercent');
	$discountPercent->appendChild($doc->createTextNode( $estimateObj->get_discountPercent() ));
	$estimate->appendChild($discountPercent);
	
	$discountAmount = $doc->createElement('discountamount');
	$discountAmount->appendChild($doc->createTextNode( $estimateObj->get_discountAmount() ));
	$estimate->appendChild($discountAmount);
	
	$dtpCoordPercent = $doc->createElement('dtpcoordpercent');
	$dtpCoordPercent->appendChild($doc->createTextNode( $estimateObj->get_dtpCoordPercent() ));
	$estimate->appendChild($dtpCoordPercent);
	
	$qaCoordPercent = $doc->createElement('qacoordpercent');
	$qaCoordPercent->appendChild($doc->createTextNode( $estimateObj->get_qaCoordPercent() ));
	$estimate->appendChild($qaCoordPercent);
	
	$projDesc = $doc->createElement('projdesc');
	$projDesc->appendChild($doc->createTextNode( $estimateObj->get_projDesc() ));
	$estimate->appendChild($projDesc);
	
	$billingTerms = $doc->createElement('billingterms');
	$billingTerms->appendChild($doc->createTextNode( $estimateObj->get_billingTerms() ));
	$estimate->appendChild($billingTerms);
	
	$billingCycle = $doc->createElement('billingcycle');
	$billingCycle->appendChild($doc->createTextNode( $estimateObj->get_billingCycle() ));
	$estimate->appendChild($billingCycle);
	
	//end data dump / estimate object
	
	//begin data dump / lingo contact
	$contact =  $doc->createElement('lingocontact');
	$dataDump->appendChild($contact);
	
	$name = $doc->createElement('name');
	$name->appendChild($doc->createTextNode( $lingoContact->get_name() ));
	$contact->appendChild($name);
	
	$title = $doc->createElement('title');
	$title->appendChild($doc->createTextNode( $lingoContact->get_title() ));
	$contact->appendChild($title);
	
	$email = $doc->createElement('email');
	$email->appendChild($doc->createTextNode( $lingoContact->get_email() ));
	$contact->appendChild($email);
	
	$phone = $doc->createElement('phone');
	$phone->appendChild($doc->createTextNode($lingoContact->get_phone()));
	$contact->appendChild($phone);
	
	//end data dump / lingo contact
	
	
	//begin data dump / PM contact
	$PM =  $doc->createElement('lingopm');
	$dataDump->appendChild($PM);
	
	$name = $doc->createElement('name');
	$name->appendChild($doc->createTextNode( $lingoPM->get_name() ));
	$PM->appendChild($name);
	
	$title = $doc->createElement('title');
	$title->appendChild($doc->createTextNode( $lingoPM->get_title() ));
	$PM->appendChild($title);
	
	$email = $doc->createElement('email');
	$email->appendChild($doc->createTextNode( $lingoPM->get_email() ));
	$PM->appendChild($email);
	
	$phone = $doc->createElement('phone');
	$phone->appendChild($doc->createTextNode($lingoPM->get_phone()));
	$PM->appendChild($phone);
	
	//end data dump / PM contact
	
	// begin data dump / language table
	
	$languages = $doc->createElement('languages');
	$dataDump->appendChild($languages);
	
	// begin data dump / language table / language pair
	foreach ($languageTbl as $languagePair)
	{
		$langpair = $doc->createElement('languagepair');
		$languages->appendChild($langpair);
		
		$srclang = $doc->createElement('srclang');
		$srclang->appendChild($doc->createTextNode($languagePair->get_sourceLang()));
		$langpair->appendChild($srclang);
		
		$tgtlang = $doc->createElement('tgtlang');
		$tgtlang->appendChild($doc->createTextNode($languagePair->get_targetLang()));
		$langpair->appendChild($tgtlang);
		
		$ntr = $doc->createElement('newtextrate');
		$ntr->appendChild($doc->createTextNode($languagePair->get_newTextRate()));
		$langpair->appendChild($ntr);
		
		$ftr = $doc->createElement('fuzzytextrate');
		$ftr->appendChild($doc->createTextNode($languagePair->get_fuzzyTextRate()));
		$langpair->appendChild($ftr);
		
		$mtr = $doc->createElement('matchtextrate');
		$mtr->appendChild($doc->createTextNode($languagePair->get_matchTextRate()));
		$langpair->appendChild($mtr);
		
		$transhourly = $doc->createElement('transhourly');
		$transhourly->appendChild($doc->createTextNode($languagePair->get_transHourly()));
		$langpair->appendChild($transhourly);
		
		$prhourly = $doc->createElement('prhourly');
		$prhourly->appendChild($doc->createTextNode($languagePair->get_prHourly()));
		$langpair->appendChild($prhourly);
		
		$errors = $doc->createElement('errors');
		$errors->appendChild($doc->createTextNode($languagePair->get_error()));
		$langpair->appendChild($errors);
		
		$newText = $doc->createElement('newtext');
		$newText->appendChild($doc->createTextNode($languagePair->get_newText()));
		$langpair->appendChild($newText);
		
		$fuzzyText = $doc->createElement('fuzzytext');
		$fuzzyText->appendChild($doc->createTextNode($languagePair->get_fuzzyText()));
		$langpair->appendChild($fuzzyText);
		
		$matchText = $doc->createElement('matchtext');
		$matchText->appendChild($doc->createTextNode($languagePair->get_matchText()));
		$langpair->appendChild($matchText);
		
		$dtpHourly = $doc->createElement('dtphourly');
		$dtpHourly->appendChild($doc->createTextNode($languagePair->get_dtpHourly()));
		$langpair->appendChild($dtpHourly);
		
		$engHourly = $doc->createElement('enghourly');
		$engHourly->appendChild($doc->createTextNode($languagePair->get_engHourly()));
		$langpair->appendChild($engHourly);
		
		//begin data dump / language table / language pair / rolled up tasks
		$rolledTasks = $doc->createElement('rolledtasks');
		$langpair->appendChild($rolledTasks);
		
		foreach ($languagePair->get_rolledUpTasks() as $taskName => $taskValue)
		{
			$temp = $doc->createElement('task');
			$temp->setAttribute('name', $taskName);
			$temp->appendChild($doc->createTextNode( $taskValue ));
			$rolledTasks->appendChild($temp);
		}
		
		//end data dump / language table / language pair / rolled up tasks
	
		
		// begin data dump / language table / language pair / task list
		$tasklistnode = $doc->createElement('tasklist');
		$langpair->appendChild($tasklistnode);
		
		foreach ($languagePair->get_tasks() as $task)
		{
			$tasknode = $doc->createElement('task');
			$tasklistnode->appendChild($tasknode);
			
			if ($task instanceof customTask)
			{
				$tasknode->setAttribute('type', 'customTask');
				
				$xyz = $doc->createElement('xyz');
				$xyz->appendChild($doc->createTextNode($task->get_xyz()));
				$tasknode->appendChild($xyz);
			}
			elseif ($task instanceof pmTask)
			{
				$tasknode->setAttribute('type', 'pmTask');
				
				$pmPercent = $doc->createElement('pmpercent');
				$pmPercent->appendChild($doc->createTextNode($task->get_pmPercent()));
				$tasknode->appendChild($pmPercent);
			}
			elseif ($task instanceof splitTask)
			{
				$tasknode->setAttribute('type', 'splitTask');
				
				$sut = $doc->createElement('sellunittype');
				$sut->appendChild($doc->createTextNode($task->get_sellUnitType()));
				$tasknode->appendChild($sut);
				
				$sellunits = $doc->createElement('sellunits');
				$sellunits->appendChild($doc->createTextNode($task->get_sellUnits()));
				$tasknode->appendChild($sellunits);
				
				$sellPerUnit = $doc->createElement('sellperunit');
				$sellPerUnit->appendChild($doc->createTextNode($task->get_sellPerUnit()));
				$tasknode->appendChild($sellPerUnit);
				
				$utlNode = $doc->createElement('unittypelist');
				$tasknode->appendChild($utlNode);
				
				foreach ($task->get_unitTypeList() as $typeName => $typeValue)
				{
					$utNode = $doc->createElement('unittype');
					$utNode->setAttribute('name', $typeName);
					$utNode->appendChild($doc->createTextNode($typeValue));
					$utlNode->appendChild($utNode);
				}
			}
			else
			{
				$tasknode->setAttribute('type', 'task');
			}
			
			$name = $doc->createElement('name');
			$name->appendChild($doc->createTextNode($task->get_name()));
			$tasknode->appendChild($name);
			
			$costUnits = $doc->createElement('costunits');
			$costUnits->appendChild($doc->createTextNode($task->get_costUnits()));
			$tasknode->appendChild($costUnits);
			
			$costUnitType = $doc->createElement('costunittype');
			$costUnitType->appendChild($doc->createTextNode($task->get_costUnitType()));
			$tasknode->appendChild($costUnitType);
			
			$costPerUnit = $doc->createElement('costperunit');
			$costPerUnit->appendChild($doc->createTextNode($task->get_costPerUnit()));
			$tasknode->appendChild($costPerUnit);
			
			$markupPercent = $doc->createElement('markuppercent');
			$markupPercent->appendChild($doc->createTextNode($task->get_markup()));
			$tasknode->appendChild($markupPercent);
			
			$usesCustomPrice = $doc->createElement('usescustomprice');
			$usesCustomPrice->appendChild($doc->createTextNode($task->usesCustomPrice()));
			$tasknode->appendChild($usesCustomPrice);
			
			$customSellPrice = $doc->createElement('customsellprice');
			$customSellPrice->appendChild($doc->createTextNode($task->get_customSellPrice()));
			$tasknode->appendChild($customSellPrice);
			
			$unitsLocked = $doc->createElement('unitslocked');
			$unitsLocked->appendChild($doc->createTextNode($task->get_unitsLocked()));
			$tasknode->appendChild($unitsLocked);
			
			$isSplit = $doc->createElement('issplit');
			$isSplit->appendChild($doc->createTextNode($task->isSplit()));
			$tasknode->appendChild($isSplit);
			
			$printable = $doc->createElement('printable');
			$printable->appendChild($doc->createTextNode($task->get_printable()));
			$tasknode->appendChild($printable);
			
		}
		
		// end data dump / language table / language pair / task list
	}
	
	// end data dump / language table / language pair 
	
	// end data dump / language table
	
	// end data dump
*/		
		
	
	$filename = $estimateObj->get_clientName() . "-" . $estimateObj->get_projectID() . "-" . date('M-d-Y-Gi') . ".xml";
	$filename = str_replace(" ","_",$filename);
	
	
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=$filename");
	
	//$result = $doc->save($relativepath);
	
	echo $doc->saveXML() . "\n";


?>
