<?PHP

include_once("../../definitions.php");

$row = $_GET['row'];
$sourceLang = $_GET['sourceLang'];
$targetLang = $_GET['targetLang'];

$error = "FALSE";


//get filetype data from the database
$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	$error = "TRUE";
	
}
else
{

	$query = "DELETE FROM linguisticcost WHERE srcLang='". $sourceLang ."' AND targetLang='". $targetLang ."'";
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
echo "<row>". $row ."</row>";
echo "</update>";




?>
