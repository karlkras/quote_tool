<?PHP

include_once("../../definitions.php");

$row = $_GET['row'];
$id = $_GET['id'];

$error = "FALSE";
$error_string = "";

//get data from the database
$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	$error = "TRUE";
	$error_string = "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
}
else
{

	$query = "DELETE FROM lingocontacts WHERE id='". $id ."'";
	$result = $myDBConn->query($query);
	
	if (!$result)
	{		
		$error = "TRUE";
		$error_string .= "\nCould not delete Contact from Database.\n".$myDBConn->error."\n\n".$query;
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
