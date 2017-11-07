<?PHP

include_once("../../definitions.php");

$newValue = $_GET['value'];
$editType = $_GET['editType'];
$clientID = $_GET['clientID'];
$sourceLang = $_GET['sourceLang'];
$targetLang = $_GET['targetLang'];
$editID = $_GET['editID'];

$error = "FALSE";
$error_text = "none";

if (($newValue == "") || ($newValue == "{null}") || ($newValue == "null"))
	$newValue = "NULL";
else
	$newValue *= 1000;




switch($editType)
{
	case 'newtext':
		$query = "UPDATE pricinglinguistic SET newTextRate=". $newValue ." WHERE ClientID='".$clientID."' AND SrcLang='". $sourceLang ."' AND TgtLang='". $targetLang ."'";
		break;		
	
	case 'fuzzy':
		$query = "UPDATE pricinglinguistic SET fuzzyTextRate=". $newValue ." WHERE ClientID='".$clientID."' AND SrcLang='". $sourceLang ."' AND TgtLang='". $targetLang ."'";
		break;
		
	case 'match':
		$query = "UPDATE pricinglinguistic SET matchTextRate=". $newValue ." WHERE ClientID='".$clientID."' AND SrcLang='". $sourceLang ."' AND TgtLang='". $targetLang ."'";
		break;
		
	case 'trans':
		$query = "UPDATE pricinglinguistic SET transHourly=". $newValue ." WHERE ClientID='".$clientID."' AND SrcLang='". $sourceLang ."' AND TgtLang='". $targetLang ."'";
		break;
		
	case 'proof':
		$query = "UPDATE pricinglinguistic SET PRhourly=". $newValue ." WHERE ClientID='".$clientID."' AND SrcLang='". $sourceLang ."' AND TgtLang='". $targetLang ."'";
		break;
		
}

$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	$error = "TRUE";
	$error_text = "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
}
else
{
	
	
	$result = $myDBConn->query($query);
	
	if (!$result)
	{		
		$error = "TRUE";
		$error_text = "Error updating pricing in database.\n".$myDBConn->error."\n\n".$query;
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
	$newValue /= 1000;

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><update>';
echo "<errorflag>". $error ."</errorflag>";
echo "<errortext>". $error_text ."</errortext>";
echo "<updatedvalue>". $newValue ."</updatedvalue>";
echo "<editid>". $editID ."</editid>";
echo "</update>";




?>
