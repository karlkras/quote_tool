<?PHP

include_once("../../definitions.php");

$row = $_GET['row'];
$clientId = $_GET['clientid'];
$tgtLangId = $_GET['tgtlangid'];
$srcLangId = $_GET['srclangid'];

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
	$query = "delete from pricinglinguistic where ClientID='".$clientId."' AND SrcLang='".$srcLangId."' AND TgtLang='".$tgtLangId."'";
	$result = $myDBConn->query($query);
	
	if (!$result)
	{		
		$error = "TRUE";
		$error_string = "Could not delete Contact from Database\n".$myDBConn->error."\n\n".$query;
	}
	else
	{
		$error = "FALSE";
		$error_string = $query;
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
