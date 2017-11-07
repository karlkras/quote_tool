<?PHP
session_start();
ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 1);

require_once("../../attaskconn/LingoAtTaskService.php");
include_once('../../definitions.php');


$error = 'false';
$errString = 'no err';
$checkKB = 'false';
$company = 'none';


if (!isset($_GET['p']))
{
	$error = 'true';
	$errString = 'no project value was set';
}
elseif (!isset($_SESSION['quotableProjects']))
{
	$error = 'true';
	$errString = 'No quotable projects in the session!';
}
else
{

	try
	{
		set_time_limit(60);
		$api = new LingoAtTaskService();
		
		$quotableProjects = unserialize($_SESSION['quotableProjects']);
		
		foreach($quotableProjects as $qp)
		{
			if ($qp->id == $_GET['p'])
				$projectStub = $qp;
		}
		
		$g = new getProject($projectStub);
		$projectObj = $api->getProject($g)->return;
		
		if ($projectObj->company->checkKnowledgeMgt == false)
			$errString = 'false';
		
		if ($projectObj->company->checkKnowledgeMgt == true)
		{
			$checkKB='true';
			$company = $projectObj->company->name;
			$errString = 'true';
			
		}
		
		
		
	}
	catch(exception $e)
	{
		$error = 'true';
		$errString = $e->getMessage();
		
	}

}




header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><check>';
echo "<error>". $error ."</error>";
echo "<errstring>". $errString ."</errstring>";
echo "<checkkb>". $checkKB ."</checkkb>";
echo "<company>". str_replace("&","&amp;",$company) ."</company>";
echo "</check>";


?>