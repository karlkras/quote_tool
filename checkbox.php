<?PHP
require_once("class_language.php");
require_once("definitions.php");
require_once("class_estimate.php");

//open the session
session_start();

$languageTbl = $_SESSION['languageTbl'];



$langID = $_GET["lang"];
$taskID = $_GET["id"];
$newValue = $_GET['value'];


if (($newValue == "false") || ($newValue == "FALSE") || ($newValue == "False"))
	$languageTbl[$langID]->get_task($taskID)->set_printable(false);
else
	$languageTbl[$langID]->get_task($taskID)->set_printable(true);

if ($languageTbl[$langID]->get_task($taskID)->get_printable() == false)
	$newValue = 0;
else
	$newValue = 1;
	
	
$_SESSION['languageTbl'] = $languageTbl;	

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><update>';
echo "<langcode>". $langID ."</langcode>";
echo "<idnum>". $taskID ."</idnum>";
echo "<newvalue>". $newValue ."</newvalue>";
echo "</update>";


