<?PHP

include_once("../../definitions.php");

$id = $_GET['id'];
$oldName = $_GET['oldname'];
$newName = $_GET['newname'];

$error = "FALSE";


$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	$error = "TRUE";
	
}
else
{

	$query = "UPDATE clients SET Name='" . $newName ."' WHERE ID='". $id ."'";
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
echo "<value>". $newName ."</value>";
echo "<id>". $id ."</id>";
echo "</update>";




?>
