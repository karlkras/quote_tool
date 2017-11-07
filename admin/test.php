<?PHP
ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 1);
require_once('../attaskconn/LingoAtTaskService.php');
require_once('../definitions.php');



	try{
		$api = new LingoAtTaskService();
		$g = new getAccessLevelByUsername();
		$g->accountName = 'emanning@llts.com';
		$userAccessLevel = $api->getAccessLevelByUsername($g)->return;
	}
	catch(exception $e)
	{
		echo "<h2>There was a problem getting the user list</h2>";
		echo "<strong>Overview:</strong> ". $e->getMessage(). "<br><br>";
		
		echo "<strong>Detail:</strong> ". $e->detail->ProcessFault->message ."<br><br><br><hr>";
		
		
		echo "Debug Data:<br><pre>";
		var_dump($e);
		echo "</pre>";
		
		exit;
	}
	
	if ($userAccessLevel == '9b503b87b9abdede1a9c23e553319579')
	{
		echo "user is an administrator";
	}
	else
	{
		echo "not an admin";
	}




?>
