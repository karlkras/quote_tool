<?PHP

include_once("../../definitions.php");

$row = $_GET['row'];
$ID = $_GET['id'];
$Name = $_GET['name'];

$error = "FALSE";
$error_string = "";

//check to see if this ID is used in any other tables
$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	$error = "TRUE";
	$error_string = "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
}
else
{

	$query = "DELETE FROM internalefforts WHERE ID='". $ID ."'";
	$result = $myDBConn->query($query);
	
	if (!$result)
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
echo '<?xml version="1.0" encoding="ISO-8859-1"?><update>';
echo "<errorflag>". $error ."</errorflag>";
echo "<errordesc>". $error_string ."</errordesc>";
echo "<row>". $row ."</row>";
echo "</update>";
?>
