<?PHP

include_once("../../definitions.php");

$row = $_GET['row'];
$clientID = $_GET['clientid'];
$clientName = $_GET['clientname'];

$error = "FALSE";
$error_string = "";

//check to see if this ID is used in any other tables
$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	$error = "TRUE";
	$error_string = "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
}
else
{

	$query = "SELECT * FROM pricinginternal WHERE ClientID='". $clientID ."'";
	$result = $myDBConn->query($query);
	
	if ($result->num_rows != 0)
	{
		$error = "TRUE";
		$error_string .= "Client in use in Custom Pricing (Internal)\n";
	}
	
	$result->free();
	
	$query = "SELECT * FROM pricinglinguistic WHERE ClientID='". $clientID . "'";
	$result = $myDBConn->query($query);
	
	if($result->num_rows != 0)
	{
		$error = "TRUE";
		$error_string .= "Client in use in Custom Pricing (Linguistic)\n";
	}
	
	$result->free();
	
	//if there are no Errors, then delete
	if ($error == "FALSE")
	{
		$query = "DELETE FROM clients WHERE ID='". $clientID ."'";
		$result = $myDBConn->query($query);
		
		if (!$result)
		{		
			$error = "TRUE";
			$error_string .= "\nCould not delete Client from Database.\n".$myDBConn->error."\n\n".$query;
		}
		else
		{
			$error = "FALSE";
			$error_string = 'none';
		}
	}
	else
	{
		$error_string .= "Please remove dependent entries from above table(s) before deleting\n";
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
