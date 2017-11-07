<?PHP

function getClientsFromAtTask($api)
{
$fullList = array();

	$g = new getClientCompanies();
	
	$APIfullList = $api->getClientCompanies($g)->return;
	
	foreach ($APIfullList as $client)
	{
		$fullList[$client->id] = str_replace('\'','',$client->name);
	}
	
	asort($fullList);
	
	return $fullList;
	
}

?>