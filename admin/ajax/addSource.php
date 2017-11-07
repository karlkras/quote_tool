<?PHP

include_once("../../definitions.php");

if (isset($_GET['name']))
{
	$languageName = $_GET['name'];
	$row = $_GET['row'];
}
else
{
	exit();
}

$error = "FALSE";
$errortext = "";
$languageID = "";

//get data from the database
$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	$error = "TRUE";
	$errortext = "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
}
else
{

	$query = "INSERT INTO sourcelang (Language) VALUES ('". $languageName . "')";
	$result = $myDBConn->query($query);
	
	if (!$result)
	{	
		$error = "TRUE";
		$errortext = "Could not insert source language into database.\n".$myDBConn->error;	
	}
	else
	{
		$error = "FALSE";
		$errortext = "none";
		
		//get the id to pass on
		$query = "SELECT ID FROM sourcelang WHERE Language='". $languageName ."'";
		$result = $myDBConn->query($query);
		$res =  $result->fetch_assoc();
		$languageID = $res['ID'];
		$result->free();
	}
	
}
$myDBConn->close();


header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><update>';
echo "<errorflag>". $error ."</errorflag>";
echo "<errortext>". $errortext ."</errortext>";
echo "<row>". $row ."</row>";
echo "<name>". $languageName ."</name>";
echo "<id>". $languageID ."</id>";
echo "</update>";



?>