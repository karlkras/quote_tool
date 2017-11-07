<?PHP
session_start();
include_once("../definitions.php");

//first things first, check for admin, if not send them to the 'view' portal
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
$row = $_GET['row'];
$error = "";
$error_string = "";


if ($subTask != 'none')
	$taskName .= "#" . $subTask . "#";

$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	$error = "TRUE";
	$error_string = "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
}
else
{
	$query = "DELETE FROM " . $tableName . " WHERE task_name='". $taskName ."'";
	$result = $myDBConn->query($query);
	
	if ((!$result) || ($myDBConn->affected_rows == 0))
	{		
		$error = "TRUE";
		$error_string = "Could not delete Task from Database.\n".$myDBConn->error;
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
echo "<error>". $error ."</error>";
echo "<text>". $error_string ."</text>";
echo "</delete>";
?>
