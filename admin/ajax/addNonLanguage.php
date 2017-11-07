<?PHP

include_once("../../definitions.php");

if (isset($_GET['index']))
{
	$rowIndex = $_GET['index'];
	$taskName = str_replace("%2B", "+",$_GET['taskname']);
	$taskName = str_replace("%26", "&", $taskName);
	$taskName = str_replace("%2F", "/", $taskName);
	$taskRate = $_GET['taskrate'];
	$taskUnit = $_GET['taskunit'];
	$taskSubCat = $_GET['subcat'];
	
	$clientName = $_GET['clientname'];
	
	$_taskName = str_replace(" ", "_", $taskName);
	if ($taskSubCat != "none")
		$_taskName = $taskName . "#" . $taskSubCat . "#";
		
	$_taskRate = $taskRate * 1000;
	
	$tableName = "client_" . $clientName;
	
}
else
{
	exit();
}

$result = "";
$error = "";

$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	$result = "ERROR";
	$error = "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
}
else
{
	$query = "INSERT INTO " . $tableName . " (task_name, rate, units) VALUES ('". $_taskName . "', '" . $_taskRate . "', '" . $taskUnit . "')";
	if (!$myDBConn->query($query))
	{
		$result = "ERROR";
		$error = "Error inserting into database.\n".$myDBConn->error;
	}
	else
	{
		$result = "COMPLETE";
		$error = "none";
	}
}

$myDBConn->close();

$taskName = str_replace("_", " ", $taskName);

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><add>';
echo "<index>". $rowIndex ."</index>";
echo "<name>". str_replace("&", "&amp;", $taskName) ."</name>";
echo "<subcat>". $taskSubCat ."</subcat>";
echo "<rate>". number_format($taskRate,3) ."</rate>";
echo "<units>". $taskUnit ."</units>";
echo "<client>". $clientName ."</client>";
echo "<result>". $result ."</result>";
echo "<error>". $error ."</error>";
echo "</add>";



?>