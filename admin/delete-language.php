<?PHP
include_once("../definitions.php");
session_start();

if ((!isset($_SESSION['isAdmin'])) || (!$_SESSION['isAdmin']))
{
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="ISO-8859-1"?><delete/></xml>';
	exit;
}

$clientName = $_GET['client'];
$tableName = "client_" . $clientName;

$taskName = str_replace("%2B", "+",$_GET['task']);
$subTask = $_GET['sub'];

if ($subTask != 'none')
	$taskName .= "#" . $subTask . "#";

$row = $_GET['row'];
$tableID = $_GET['table'];

$srcLang = $_GET['src'];
$tgtLang = $_GET['tgt'];

$taskName .= "=" . $srcLang . "=" . $tgtLang;


$error_string = "";
$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	$error = "TRUE";
	$error_string .= "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
}
else
{

	$query = "DELETE FROM " . $tableName . " WHERE task_name='". $taskName ."'";
	$result = $myDBConn->query($query);
	
	
	if ((!$result) || ($myDBConn->affected_rows == 0))
	{		
		$error = "TRUE";
		$error_string .= "\nCould not delete Task from Database\n".$myDBConn->error."\n\n".$query;
	}
	else
	{
		$error = "FALSE";
		$error_string = 'none';
	}
}

$myDBConn->close();


header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><delete>';
echo "<row>". $row ."</row>";
echo "<tableid>". $tableID ."</tableid>";
echo "<error>". $error ."</error>";
echo "<text>". $error_string ."</text>";
echo "</delete>";


?>
