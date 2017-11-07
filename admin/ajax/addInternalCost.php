<?PHP

include_once("../../definitions.php");

if ( isset($_GET['name']) && isset($_GET['rate']) )
{
	$taskName = $_GET['name'];
	$hourlyRate = $_GET['rate'];
	$row = $_GET['row'];
}
else
{
	exit();
}

$error = "FALSE";
$errortext = "";

//get data from the database
$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	$error = "TRUE";
	$errortext = "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
}
else
{

	$query = "INSERT INTO internalefforts (Name, HourlyRate) VALUES ('". $taskName . "', '". $hourlyRate*100 ."')";
	$result = $myDBConn->query($query);
	
	if (!$result)
	{	
		$error = "TRUE";
		$errortext = "Could not insert task into database.\n".$myDBConn->error;	
	}
	else
	{
		$error = "FALSE";
		$errortext = "none";
		
		//get the id to pass on
		$query = "SELECT ID FROM internalefforts WHERE Name='". $taskName ."'";
		$result = $myDBConn->query($query);
		$res =  $result->fetch_assoc();
		$ID = $res['ID'];
		$result->free();
	
	}
	
}
$myDBConn->close();


header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><update>';
echo "<errorflag>". $error ."</errorflag>";
echo "<errortext>". $errortext ."</errortext>";
echo "<row>". $row ."</row>";
echo "<name>". $taskName ."</name>";
echo "<rate>$". number_format($hourlyRate,2) ."</rate>";
echo "<id>". $ID ."</id>";
echo "</update>";



?>