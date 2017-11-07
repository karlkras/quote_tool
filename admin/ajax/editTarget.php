<?PHP

include_once("../../definitions.php");

$id = $_GET['id'];
$newName = $_GET['newname'];

$error = "FALSE";
$error_text = "none";


$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	$error = "TRUE";
	$error_text = "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
}
else
{
	$query = "UPDATE targetlang SET Language='" . $newName ."' WHERE ID='". $id ."'";
	$result = $myDBConn->query($query);
	
	if (!$result)
	{		
		$error = "TRUE";
		$error_text = "Error updating database.\n".$myDBConn->error;
	}
	else
	{
		$error = "FALSE";
	}
	
}
$myDBConn->close();


header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><update>';
echo "<errorflag>". $error ."</errorflag>";
echo "<errortext>". $error_text ."</errortext>";
echo "<value>". $newName ."</value>";
echo "<id>". $id ."</id>";
echo "</update>";




?>
