<?PHP

include_once("../../definitions.php");

$row = $_GET['row'];
$clientid = $_GET['clientid'];
$sourceLangId = $_GET['sourceLangId'];
$targetLangId = $_GET['targetLangId'];
$newTextRate = $_GET['newTextRate'];
$fuzzyTextRate = $_GET['fuzzyTextRate'];
$matchTextRate = $_GET['matchTextRate'];
$transHourly = $_GET['transHourly'];
$prHourly = $_GET['prHourly'];

$error = "FALSE";
$error_text = "none";

if (($newTextRate == "") || ($newTextRate == "{null}") || ($newTextRate == "null"))
	$newTextRate = "NULL";
else
	$newTextRate *= 1000;
	
if (($fuzzyTextRate == "") || ($fuzzyTextRate == "{null}") || ($fuzzyTextRate == "null"))
	$fuzzyTextRate = "NULL";
else
	$fuzzyTextRate *= 1000;
	
if (($matchTextRate == "") || ($matchTextRate == "{null}") || ($matchTextRate == "null"))
	$matchTextRate = "NULL";
else
	$matchTextRate *= 1000;
	
if (($transHourly == "") || ($transHourly == "{null}") || ($transHourly == "null"))
	$transHourly = "NULL";
else
	$transHourly *= 1000;
	
if (($prHourly == "") || ($prHourly == "{null}") || ($prHourly == "null"))
	$prHourly = "NULL";
else
	$prHourly *= 1000;
	
	
//get data from the database
$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	$error = "TRUE";
	$error_text = "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
}
else
{

	$query = "INSERT INTO pricinglinguistic (ClientID, SrcLang, TgtLang, newTextRate, fuzzyTextRate, matchTextRate, transHourly, PRhourly) VALUES ('". $clientid . "', '". $sourceLangId ."', '". $targetLangId ."', ". $newTextRate .", ". $fuzzyTextRate .", ". $matchTextRate .", ". $transHourly .", ". $prHourly .")";
	$result = $myDBConn->query($query);
	
	if (!$result)
	{	
		$error = "TRUE";
		$error_text = "Could not insert pricing info  into database.\n".$myDBConn->error;	
	}
	else
	{
		$error = "FALSE";
		$error_text = "none";
		
		//get client and language names
		$query = "SELECT Name FROM clients WHERE ID='".$clientid."'";
		$result = $myDBConn->query($query);
		$res = $result->fetch_assoc();
		$clientname = $res['Name'];
		
		$result->free();
		$query = "SELECT Language FROM sourcelang WHERE ID='".$sourceLangId."'";
		$result = $myDBConn->query($query);
		$res = $result->fetch_assoc();
		$sourcename = $res['Language'];
		
		$result->free();
		$query = "SELECT Language FROM targetlang WHERE ID='".$targetLangId."'";
		$result = $myDBConn->query($query);
		$res = $result->fetch_assoc();
		$targetname = $res['Language'];
		$result->free();
		
	}
	
	
}
$myDBConn->close();


if ($newTextRate == "NULL")
	$newTextRate = "{null}";
else
	$newTextRate /= 1000;
	
if ($fuzzyTextRate == "NULL")
	$fuzzyTextRate = "{null}";
else
	$fuzzyTextRate /= 1000;	

if ($matchTextRate == "NULL")
	$matchTextRate = "{null}";
else
	$matchTextRate /= 1000;	
	
if ($transHourly == "NULL")
	$transHourly = "{null}";
else
	$transHourly /= 1000;
	
if ($prHourly == "NULL")
	$prHourly = "{null}";
else
	$prHourly /= 1000;

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><update>';
echo "<errorflag>". $error ."</errorflag>";
echo "<errortext>". $error_text ."</errortext>";
echo "<row>". $row ."</row>";
echo "<clientid>". $clientid ."</clientid>";
echo "<clientname>". $clientname ."</clientname>";
echo "<sourcelangid>". $sourceLangId ."</sourcelangid>";
echo "<sourcename>". $sourcename ."</sourcename>";
echo "<targetlangid>". $targetLangId ."</targetlangid>";
echo "<targetname>". $targetname ."</targetname>";
echo "<newtextrate>". $newTextRate ."</newtextrate>";
echo "<fuzzytextrate>". $fuzzyTextRate ."</fuzzytextrate>";
echo "<matchtextrate>". $matchTextRate ."</matchtextrate>";
echo "<transhourly>". $transHourly ."</transhourly>";
echo "<prhourly>". $prHourly ."</prhourly>";
echo "</update>";




?>
