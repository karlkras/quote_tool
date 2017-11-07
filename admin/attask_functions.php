<?PHP

require_once('../attaskconn/LingoAtTaskService.php');

function getClientsFromAtTask($api)
{
$fullList = array();

	$g = new getClientCompanies();
	
	$APIfullList = $api->getClientCompanies($g)->return;
	
	foreach ($APIfullList as $client)
	{
		$fullList[$client->id] = $client->name;
	}
	
	asort($fullList);
	
	return $fullList;
	
}

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
		$result = $myDBConn->query($query);

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