<?PHP
function getClientFromId($clientID, &$clientName, &$tableName)
{
$clientName = "";
$tableName = "";
	
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	
	$clientQuery = "SELECT Name, table_name FROM clients WHERE ID=".$clientID;
	$clientResult = $myDBConn->query($clientQuery) or die($myDBConn->error);
	
	if ($clientResult->num_rows != 1)
	{
		echo "There was an error retrieving the client information from the client table<br>\n";
		exit;
	}
	
	
	$clientRow = $clientResult->fetch_assoc();
	$clientName = $clientRow['Name'];
	$tableName = $clientRow['table_name'];
	
	$clientResult->free();
	$myDBConn->close();
	
}
?>