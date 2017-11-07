<?PHP

function removeUsedClients($allClients)
{

$newClients = $allClients;

	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}

	foreach ($allClients as $client_id => $client_name)
	{
		$query = "SELECT * FROM clients WHERE attask_id = ".$client_id;
		$result = $myDBConn->query($query) or die($myDBConn->error);

		if ($result->num_rows > 0)
		{
			unset($newClients[$client_id]);
		}
		$result->free();
		
	}
	$myDBConn->close();
	return $newClients;

}

?>