<?PHP

include_once("../../definitions.php");

$id = $_GET['id'];
$type = $_GET['type'];
$value = $_GET['value'];

$error = "FALSE";


$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	$errorflag = "TRUE";
}
else
{

	$query = "UPDATE lingocontacts SET ". $type ."='" . $value ."' WHERE ID='". $id ."'";
	$result = $myDBConn->query($query);
	
	if (!$result)
	{		
		$error = "TRUE";
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
echo "<type>". $type ."</type>";
echo "<value>". $value ."</value>";
echo "<id>". $id ."</id>";
echo "</update>";




?>
