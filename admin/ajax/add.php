<?PHP

include_once("../../definitions.php");

define('___DEBUG___', false);

$row = $_GET['row'];
$sourceLang = $_GET['srcLang'];
$targetLang = $_GET['tgtLang'];

$newText = (isset($_GET['newText'])) ? $_GET['newText'] : "NULL";
$fuzzyText = (isset($_GET['fuzzyText'])) ? $_GET['fuzzyText'] : "NULL";
$matchText = (isset($_GET['matchText'])) ? $_GET['matchText'] : "NULL";
$transHourly = (isset($_GET['transH'])) ? $_GET['transH'] : "NULL";
$prHourly = (isset($_GET['prH'])) ? $_GET['prH'] : "NULL";

$error = "FALSE";
$errortext = "none";
$debug_log = "";

//get data from the database
$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	$error = "TRUE";
	$errortext = "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
}
else
{

	//get the source langauge ID
	$query = "SELECT ID FROM sourcelang WHERE Language='$sourceLang'";
	$result = $myDBConn->query($query);
	if (___DEBUG___)
	{
		$debug_log .= "query= " . $query . "\n";
		$debug_log .= "result= " . $result . "\n";
	}
	
	
	if ($result != NULL)
	{
		$res =  $result->fetch_assoc();
		$sourceLangID = $res['ID'];
		if(___DEBUG___)
		{
			$debug_log .= "Source Lang ID= " . $sourceLangID . "\n";
		}
		$result->free();
	
	}
	else
	{	// we need to add the language to the database
		$error = "TRUE";
		$errortext = "Source language does not exist in database";
		if(___DEBUG___)
		{
			$debug_log .= $errortext . "\n";
		}
		
		if (!is_bool($result))
		{
			$result->free();
		}
		$query = "INSERT INTO sourcelang (Language) VALUES (". $sourceLang .")";
		$result = $myDBConn->query($query);
		
		if(___DEBUG___)
		{
			$debug_log .= "query= " . $query . "\n";
			$debug_log .= "result= " . $result . "\n";
		}	
		
		if ($result == TRUE)
		{
			$error = "FALSE";
			$errortext = "none";
			
			//get the source langauge ID (again!)
			$query = "SELECT ID FROM sourcelang WHERE Language='$sourceLang'";
			$result = $myDBConn->query($query);
			$res =  $result->fetch_assoc();
			$sourceLangID = $res['ID'];
			
			if(___DEBUG___)
			{
				$debug_log .= "query= ". $query . "\n";
				$debug_log .= "result= ". $result . "\n";
				$debug_log .= "Source Lang ID= ". $sourceLangID . "\n";
			}		
			
			$result->free();
		}
		else
		{
			$error = "TRUE";
			$errortext = "Could not insert ". $sourceLang ." into database";
			if(___DEBUG___)
			{
				$debug_log .= $errortext . "\n";
			}		
		}
	}
	
	//get the target language id
	$query = "SELECT ID FROM targetlang WHERE Language='$targetLang'";
	$result = $myDBConn->query($query);
	
	if(___DEBUG___)
	{
		$debug_log .= "query= " . $query . "\n";
		$debug_log .= "result= " . $result . "\n";
	}
	
	if (!$result)
	{	// then the language is already in the database, we need to abort.
		$error = "TRUE";
		$errortext = "Source and Target Pair already exist. Please use 'edit' instead of 'add'.";
	
		$targetLangID = 0;
		if(___DEBUG___)
		{
			$debug_log .= $errortext;
		}	
	}
	else
	{
		if (!is_bool($result))
		{
			$result->free();
		}
		// we need to add the language to the database
		$query = "INSERT INTO targetlang (Language) VALUES ('". $targetLang ."')";
		$result = $myDBConn->query($query);
		if(___DEBUG___)
		{
			$debug_log .= "query= ".$query."\n";
			$debug_log .= "result= ".$result."\n";
		}	
		if (!$result)
		{
			$error = "TRUE";
			$errortext = "Could not insert ". $targetLang ." into database";
			if(___DEBUG___)
			{
				$debug_log .= $errortext . "\n";
			}		
					
			
		}
		else
		{
			$error = "FALSE";
			$errortext = "none";
			
			//get the target language id (again!)
			$query = "SELECT ID FROM targetlang WHERE Language='$targetLang'";
			$result = $myDBConn->query($query);
			$res =  $result->fetch_assoc();
			$targetLangID = $res['ID'];
			if(___DEBUG___)
			{
				$debug_log .= "query= ".$query."\n";
				$debug_log .= "result= ".$result."\n";
				$debug_log .= "target lang id= ".$targetLangID."\n";
			}
			$result->free();
	
		}
	}
		
	//add the new data to the database
	if ($error == "FALSE")
	{
		$query = "INSERT INTO linguisticcost (srcLang, targetLang, newTextRate, fuzzyTextRate, matchTextRate, transHourly, prHourly) VALUES ('";
		$query = $query . $sourceLangID . "', '". $targetLangID ."', '". $newText*1000 ."', '". $fuzzyText*1000 ."', '". $matchText*1000 ."', '". $transHourly*1000 ."', '". $prHourly*1000;
		$query = $query . "')";
		$result = $myDBConn->query($query);
		if(___DEBUG___)
		{
			$debug_log .= "query= ".$query."\n";	
		}	
		
		if (!$result)
		{	
			$error = "TRUE";
			$errortext = "Could not insert language pair into database";	
		}
		else
		{
			$error = "FALSE";
			$errortext = "none";
	
		}
	}
		
	$result->free();
}
$myDBConn->close();


header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><update>';
echo "<errorflag>". $error ."</errorflag>";
echo "<errortext>". $errortext ."</errortext>";
echo "<srclang>". $sourceLang ."</srclang>";
echo "<srclangid>". $sourceLangID ."</srclangid>";
echo "<tgtlang>". $targetLang ."</tgtlang>";
echo "<tgtlangid>". $targetLangID ."</tgtlangid>";
echo "<newtext>". $newText ."</newtext>";
echo "<fuzzytext>". $fuzzyText ."</fuzzytext>";
echo "<matchtext>". $matchText ."</matchtext>";
echo "<transhourly>". $transHourly ."</transhourly>";
echo "<prhourly>". $prHourly ."</prhourly>"; 
echo "<row>". $row ."</row>";
if (___DEBUG___)
{
	echo "<debug>", $debug_log, "</debug>";
}
echo "</update>";




?>
