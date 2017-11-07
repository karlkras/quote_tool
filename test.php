<?PHP
ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 1);
include_once('definitions.php');
require_once('/attaskconn/LingoAtTaskService.php');

try{
$api = new LingoAtTaskService();
$g = new validateUserPassword;
$g->UserName = "mvangrunsven@llts.com";
$g->Password = "Q1Lingo2014";
$result = $api->validateUserPassword($g)->return;
}
catch(exception $e)
{
	echo "<h2>There was a problem with the @task service</h2>";
	echo "<strong>Overview:</strong> ". $e->getMessage(). "<br><br>";
	
	echo "<strong>Detail:</strong> ". $e->detail->ProcessFault->message ."<br><br><br><hr>";
	
	
	echo "Debug Data:<br><pre>";
	var_dump($e);
	echo "</pre>";
	
	exit;
}

try{
	$g = new getLingoUsers;
	$lingoUsers = $api->getLingoUsers($g)->return;
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

foreach ($lingoUsers as $user)
{
	/*echo "Name: ",$user->firstName," ",$user->lastName,"<br>";
	echo "ID: ",$user->id,"<br>";
	echo "username: ", $user->userName,"<br><br>";*/
	if ($user->userName == "mvangrunsven@llts.com")
	{
		$userID = $user->id;
		$userRoles = $user->roles;
		break;
	}
}

$x=1;
foreach ($userRoles as $role)
{
	echo $x++,": ",$role,"<br>";
}

echo "done<br><br>";


if (in_array("Localization Engineer",$userRoles))
{
	echo "IS an Engineer<br>";
}
else
{
	echo "not an engineer<br>";
}



?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<p><?PHP 
	if ($result)
	{
		echo "Correct";
	}
	else
	{
		echo "Incorrect";
	}?></p>
</body>
</html>