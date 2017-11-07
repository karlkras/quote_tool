<?PHP
require_once("class_language.php");
require_once("definitions.php");
require_once("class_estimate.php");

//open the session
session_start();
$languageTbl = $_SESSION['languageTbl'];
$estimateObj = $_SESSION['estimate'];



$langID = $_GET["lang"];
$taskType = $_GET["type"];
$newValue = $_GET['value'];

$asp = 0;

switch($taskType)
{
	case 'linguistic':
		if (($newValue == "true") || ($newValue == "TRUE") || ($newValue == "True"))
		{
			$asp = $languageTbl[$langID]->get_task(NEWTEXT)->get_actualSellPrice() + $languageTbl[$langID]->get_task(FUZZY)->get_actualSellPrice();
			$asp += $languageTbl[$langID]->get_task(MATCHES)->get_actualSellPrice();
			$languageTbl[$langID]->set_rolledUpTask($taskType, $asp);
			
			$languageTbl[$langID]->get_task(NEWTEXT)->set_printable(false);
			$languageTbl[$langID]->get_task(FUZZY)->set_printable(false);
			$languageTbl[$langID]->get_task(MATCHES)->set_printable(false);
		}
		else
		{
			$languageTbl[$langID]->remove_rolledUpTask_at($taskType);
			
			$languageTbl[$langID]->get_task(NEWTEXT)->set_printable(true);
			$languageTbl[$langID]->get_task(FUZZY)->set_printable(true);
			$languageTbl[$langID]->get_task(MATCHES)->set_printable(true);
		}
		break;
		
	case 'dtp':
		if (($newValue == "true") || ($newValue == "TRUE") || ($newValue == "True"))
		{
			$asp = $languageTbl[$langID]->get_task(FORMAT)->get_actualSellPrice() + $languageTbl[$langID]->get_task(DTPCOORD)->get_actualSellPrice();
			$asp += $languageTbl[$langID]->get_task(GRAPHICS)->get_actualSellPrice();
			$languageTbl[$langID]->set_rolledUpTask($taskType, $asp);
			
			$languageTbl[$langID]->get_task(FORMAT)->set_printable(false);
			$languageTbl[$langID]->get_task(DTPCOORD)->set_printable(false);
			$languageTbl[$langID]->get_task(GRAPHICS)->set_printable(false);
		}
		else
		{
			$languageTbl[$langID]->remove_rolledUpTask_at($taskType);
			
			$languageTbl[$langID]->get_task(FORMAT)->set_printable(true);
			$languageTbl[$langID]->get_task(DTPCOORD)->set_printable(true);
			$languageTbl[$langID]->get_task(GRAPHICS)->set_printable(true);
		}
		break;
		
	case 'engineering':
		if (($newValue == "true") || ($newValue == "TRUE") || ($newValue == "True"))
		{
			$asp = $languageTbl[$langID]->get_task(TMWORK)->get_actualSellPrice() + $languageTbl[$langID]->get_task(ENGINEERING)->get_actualSellPrice();
			$asp += $languageTbl[$langID]->get_task(SCAPS)->get_actualSellPrice();
			$languageTbl[$langID]->set_rolledUpTask($taskType, $asp);
			
			$languageTbl[$langID]->get_task(TMWORK)->set_printable(false);
			$languageTbl[$langID]->get_task(ENGINEERING)->set_printable(false);
			$languageTbl[$langID]->get_task(SCAPS)->set_printable(false);
			
		}
		else
		{
			$languageTbl[$langID]->remove_rolledUpTask_at($taskType);
			
			$languageTbl[$langID]->get_task(TMWORK)->set_printable(true);
			$languageTbl[$langID]->get_task(ENGINEERING)->set_printable(true);
			$languageTbl[$langID]->get_task(SCAPS)->set_printable(true);
			
		}
		break;
		
	case 'qa':
		if (($newValue == "true") || ($newValue == "TRUE") || ($newValue == "True"))
		{
			$asp = $languageTbl[$langID]->get_task(QA)->get_actualSellPrice() + $languageTbl[$langID]->get_task(QACOORD)->get_actualSellPrice();
			$languageTbl[$langID]->set_rolledUpTask($taskType, $asp);
			
			$languageTbl[$langID]->get_task(QA)->set_printable(false);
			$languageTbl[$langID]->get_task(QACOORD)->set_printable(false);
		}
		else
		{
			$languageTbl[$langID]->remove_rolledUpTask_at($taskType);
			
			$languageTbl[$langID]->get_task(QA)->set_printable(true);
			$languageTbl[$langID]->get_task(QACOORD)->set_printable(true);
		}
		break;
		
	case 'additional':
		if (($newValue == "true") || ($newValue == "TRUE") || ($newValue == "True"))
		{
			$asp = $languageTbl[$langID]->get_task(ADD1)->get_actualSellPrice() + $languageTbl[$langID]->get_task(ADD2)->get_actualSellPrice();
			$asp += $languageTbl[$langID]->get_task(ADD3)->get_actualSellPrice();
			$languageTbl[$langID]->set_rolledUpTask($taskType, $asp);
			
			$languageTbl[$langID]->get_task(ADD1)->set_printable(false);
			$languageTbl[$langID]->get_task(ADD2)->set_printable(false);
			$languageTbl[$langID]->get_task(ADD3)->set_printable(false);
		}
		else
		{
			$languageTbl[$langID]->remove_rolledUpTask_at($taskType);
			
			$languageTbl[$langID]->get_task(ADD1)->set_printable(true);
			$languageTbl[$langID]->get_task(ADD2)->set_printable(true);
			$languageTbl[$langID]->get_task(ADD3)->set_printable(true);
		}
		break;
	
	
}




	
$_SESSION['languageTbl'] = $languageTbl;	
$_SESSION['estimate'] = $estimateObj;

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><update>';
echo "<langcode>". $langID ."</langcode>";
echo "<tasktype>". $taskType ."</tasktype>";
echo "<newvalue>". $newValue ."</newvalue>";
echo "<asp>". $asp ."</asp>";
echo "</update>";


