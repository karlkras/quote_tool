<?PHP
require_once("class_language.php");
require_once("definitions.php");
require_once("class_estimate.php");
require_once("db_functions.php");

//open the session
session_start();
$languageTbl = $_SESSION['languageTbl'];
$estimate = $_SESSION['estimate'];



$newUnitType = $_GET["newType"];
$langID = $_GET["lang"];
$taskID = $_GET["id"];
$costSell = $_GET["costSell"];

if ($costSell == "cost")
	$languageTbl[$langID]->get_task($taskID)->set_costUnitType($newUnitType);
else
	$languageTbl[$langID]->get_task($taskID)->set_sellUnitType($newUnitType);


$invalidType = FALSE;
$newUnits = 0;

//get the number of units from the language
switch ($newUnitType)
{
	case "Words":
		switch ($taskID)
		{
			case NEWTEXT:
				$newUnits = $languageTbl[$langID]->get_newText();
				$newPricePer = $languageTbl[$langID]->get_newTextRate();
				break;
			case FUZZY:
				$newUnits = $languageTbl[$langID]->get_fuzzyText();
				$newPricePer = $languageTbl[$langID]->get_fuzzyTextRate();
				break;
			case MATCHES:
				$newUnits = $languageTbl[$langID]->get_matchText();
				$newPricePer = $languageTbl[$langID]->get_matchTextRate();
				break;
			
		}
		break;
		
	case "Hours":
		switch ($taskID)
		{
			case NEWTEXT:
				$newUnits = $languageTbl[$langID]->get_newText() / 250;
				$newUnits += $languageTbl[$langID]->get_newText() / 1000;
				$newUnits = round($newUnits*4)/4;
				$newPricePer = $languageTbl[$langID]->get_transHourly();
				break;
			case FUZZY:
				$newUnits = $languageTbl[$langID]->get_fuzzyText() / 250;
				$newUnits += $languageTbl[$langID]->get_fuzzyText() / 1000;
				$newUnits = round($newUnits*4)/4;
				$newPricePer = $languageTbl[$langID]->get_transHourly();
				break;
			case MATCHES:
				$newUnits = $languageTbl[$langID]->get_matchText() / 250;
				$newUnits += $languageTbl[$langID]->get_matchText() / 1000;
				$newUnits = round($newUnits*4)/4;
				$newPricePer = $languageTbl[$langID]->get_transHourly();
				break;
			case PROOF:
				$newUnits = $languageTbl[$langID]->total_words() / 2000;
				$newUnits = round($newUnits*4)/4;
				$newPricePer = $languageTbl[$langID]->get_prHourly();
				break;
			case FORMAT:
				$newUnits = ($estimate->get_pages() / $estimate->get_pagesPerHour());
				$newUnits = round($newUnits*4)/4;
				$newPricePer = $languageTbl[$langID]->get_dtpHourly();
				break;
		}
		break;
	
	case "Pages":
		switch ($taskID)
		{
			case FORMAT:
				$newUnits = $estimate->get_pages();
				$temp = (($estimate->get_pages() / $estimate->get_pagesPerHour()) * $languageTbl[$langID]->get_dtpHourly())/$estimate->get_pages();
				$temp = round($temp*4)/4;
				$newPricePer = $temp;
				break;
		}
		break;
	

		
}
		
if ($costSell == "cost")
{
	$languageTbl[$langID]->get_task($taskID)->set_costUnits($newUnits);
	$languageTbl[$langID]->get_task($taskID)->set_costPerUnit($newPricePer);
	if ($languageTbl[$langID]->get_task($taskID)->isSplit() == 'false')
	{
		$languageTbl[$langID]->get_task($taskID)->set_sellUnits($newUnits);
		$languageTbl[$langID]->get_task($taskID)->set_sellPerUnit($newPricePer);
		$languageTbl[$langID]->get_task($taskID)->set_sellUnitType($languageTbl[$langID]->get_task($taskID)->get_costUnitType());
	}
}
else
{
	$languageTbl[$langID]->get_task($taskID)->set_sellUnits($newUnits);
	$languageTbl[$langID]->get_task($taskID)->set_sellPerUnit($newPricePer);
}

//check for custom sell price data and adjust accordingly
$DB_ClientID = getClientID($estimate->get_clientName());
$DB_SrcLangID = getSourceLangID($languageTbl[$langID]->get_sourceLang());
$DB_TgtLangID = getTargetLangID($languageTbl[$langID]->get_targetLang());


switch($taskID)
{
	case PM:
	case ADD1:
	case ADD2:
	case ADD3:
	case QA:
	case QACOORD:
	case SCAPS:
	case ENGINEERING:
	case TMWORK:
	case DTPCOORD:
	case GRAPHICS:
	case PROOF:
		$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, $taskID);
		break;
	case FORMAT:
		if ($languageTbl[$langID]->get_task($taskID)->isSplit() == 'false')
		{
			if ($languageTbl[$langID]->get_task($taskID)->get_costUnitType() == 'Hours')
			{
				$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, FORMAT);
			}
			else
			{
				$customPrice = 0;
			}
		}
		else
		{
			if ($languageTbl[$langID]->get_task($taskID)->get_sellUnitType() == 'Hours')
			{
				$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, FORMAT);
			}
			else
			{
				$customPrice = 0;
			}
		}
		break;
	case FUZZY:
		if ($languageTbl[$langID]->get_task($taskID)->isSplit() == 'false')
		{	
			if ($languageTbl[$langID]->get_task($taskID)->get_costUnitType() == 'Hours')
			{
				$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, "transHourly");
			}
			else
			{
				$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, FUZZY);
			}		
		}
		else
		{
			if ($languageTbl[$langID]->get_task($taskID)->get_sellUnitType() == 'Hours')
			{
				$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, "transHourly");
			}
			else
			{
				$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, FUZZY);
			}		
		}			
		break;
	case MATCHES:
		if ($languageTbl[$langID]->get_task($taskID)->isSplit() == 'false')
		{	
			if ($languageTbl[$langID]->get_task($taskID)->get_costUnitType() == 'Hours')
			{
				$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, "transHourly");
			}
			else
			{
				$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, MATCHES);
			}		
		}
		else
		{
			if ($languageTbl[$langID]->get_task($taskID)->get_sellUnitType() == 'Hours')
			{
				$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, "transHourly");
			}
			else
			{
				$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, MATCHES);
			}		
		}			
		break;
	case NEWTEXT:
		if ($languageTbl[$langID]->get_task($taskID)->isSplit() == 'false')
		{
			if ($languageTbl[$langID]->get_task($taskID)->get_costUnitType() == 'Hours')
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
			if ($languageTbl[$langID]->get_task($taskID)->get_selltUnitType() == 'Hours')
			{
				$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, "transHourly");
			}
			else
			{
				$customPrice = checkCustom($DB_SrcLangID, $DB_TgtLangID, $DB_ClientID, "NEWTEXT");
			}	
		}
		break;		
}
		
	
$customPrice /= 1000;
$languageTbl[$langID]->get_task($taskID)->set_customPrice($customPrice);


$_SESSION['languageTbl'] = $languageTbl;

if (floor($newUnits) != $newUnits)
	$newUnits = number_format($newUnits,2);
else
	$newUnits = number_format($newUnits);
	
if (floor($newPricePer*100) == ($newPricePer*100))
	$newPricePer = number_format($newPricePer,2);
else
	$newPricePer = number_format($newPricePer,3);

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><update>';
echo "<langCode>". $langID ."</langCode>";
echo "<idNum>". $taskID ."</idNum>";
echo "<newunits>". $newUnits ."</newunits>";
echo "<newcostper>". $newPricePer ."</newcostper>";
echo "<costsell>". $costSell ."</costsell>";
echo "<newtype>". $newUnitType ."</newtype>";
if ($languageTbl[$langID]->get_task($taskID)->usesCustomPrice())
	echo "<usescustom>true</usescustom>";
echo "</update>";


