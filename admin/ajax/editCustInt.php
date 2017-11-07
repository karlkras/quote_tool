<?PHP

include_once("../../definitions.php");

$newValue = $_GET['value'];
$editType = $_GET['editType'];
$clientID = $_GET['clientID'];
$editID = $_GET['editID'];

$error = "FALSE";
$error_text = "none";

if (($newValue == "") || ($newValue == "{null}") || ($newValue == "null"))
	$newValue = "NULL";
else
{
	if ($editType != 'percent')
		$newValue *= 1000;
}



switch($editType)
{
	case 'dtp':
		$query = "UPDATE pricinginternal SET dtp=". $newValue ." WHERE ClientID='".$clientID."'";
		break;		
	
	case 'eng':
		$query = "UPDATE pricinginternal SET engineering=". $newValue ." WHERE ClientID='".$clientID."'";
		break;
		
	case 'senEng':
		$query = "UPDATE pricinginternal SET seniorengineering=". $newValue ." WHERE ClientID='".$clientID."'";
		break;
		
	case 'qa':
		$query = "UPDATE pricinginternal SET qa=". $newValue ." WHERE ClientID='".$clientID."'";
		break;
		
	case 'percent':
		$query = "UPDATE pricinginternal SET pmpercent=". $newValue ." WHERE ClientID='".$clientID."'";
		break;
	
	case 'hourly':
		$query = "UPDATE pricinginternal SET pmhourly=". $newValue ." WHERE ClientID='".$clientID."'";
		break;
		
}

$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	$errorflag = "TRUE";
	$errortext = "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
}
else
{

	$result = $myDBConn->query($query);
	
	if (!$result)
	{		
		$error = "TRUE";
		$error_text = "Error updating database.\n".$myDBConn->error."\n\n".$query;
	}
	else
	{
		$error = "FALSE";
	}
		
}
$myDBConn->close();

if ($newValue == "NULL")
	$newValue = "{null}";
else
	if ($editType != 'percent')
		$newValue /= 1000;

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><update>';
echo "<errorflag>". $error ."</errorflag>";
echo "<errortext>". $error_text ."</errortext>";
echo "<updatedvalue>". $newValue ."</updatedvalue>";
echo "<editid>". $editID ."</editid>";
echo "</update>";




?>
