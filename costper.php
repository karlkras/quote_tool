<?PHP
require_once("class_language.php");
require_once("definitions.php");

//open the session
session_start();
$languageTbl = $_SESSION['languageTbl'];

$langID = $_GET['lang'];
$taskID = $_GET['id'];
$newValue = $_GET['value'];

$languageTbl[$langID]->get_task($taskID)->set_costPerUnit($newValue);

if ( (get_class($languageTbl[$langID]->get_task($taskID)) == 'splitTask') && ($languageTbl[$langID]->get_task($taskID)->get_costUnitType() == $languageTbl[$langID]->get_task($taskID)->get_sellUnitType()))
{
	$languageTbl[$langID]->get_task($taskID)->set_sellPerUnit($newValue);
}

$units = $languageTbl[$langID]->get_task($taskID)->get_costUnits();

$_SESSION['languageTbl'] = $languageTbl;

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><update>';
echo "<langCode>". $langID ."</langCode>";
echo "<idNum>". $taskID ."</idNum>";
echo "<newunits>". $units ."</newunits>";
echo "</update>";

?>