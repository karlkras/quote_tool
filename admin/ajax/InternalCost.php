<?PHP

include_once("../../definitions.php");


$editType = $_GET['edittype'];
$value = $_GET['value'];
$editID = $_GET['id'];

$error = "FALSE";

switch($editType)
{
	case 'name':
		$query = "UPDATE internalefforts SET Name='". $value ."' WHERE ID='". $editID ."'";
		break;		
		
	case 'rate':
		$query = "UPDATE internalefforts SET HourlyRate='". $value*100 ."' WHERE ID='". $editID ."'";
		break;
}

$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	$error = "TRUE";
}
else
{	

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
echo "<updatedvalue>$". number_format($value,2) ."</updatedvalue>";
echo "<editid>". $editID ."</editid>";
echo "</update>";




?>
