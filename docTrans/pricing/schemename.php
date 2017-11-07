<?PHP
function getSchemeName($projectObj)
{
$client_id=$projectObj->company->id;
		
	$query = "SELECT * FROM clients WHERE attask_id = ".$client_id;
		
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	
	$result = $myDBConn->query($query);

	if ($result->num_rows > 0)
	{
		$res = $result->fetch_assoc();
		$schemeApplied = $res['table_name'];
		
	}
	else
	{
		$schemeApplied = 'none';

	}
	$result->free();
	$myDBConn->close();
	
	return $schemeApplied;

}

?>