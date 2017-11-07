<?PHP
session_start();
require_once('../uuid.php');

//check to see if we're logged in
if (!isset($_SESSION['userID'])) {
	die('You are not properly logged in and are unable to use this application');
} elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID)) {
    die('Fatal Error! Application authentication is not correct');
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>PDF Upload - LLS Quote Tool</title>
</head>

<body>
<form enctype="multipart/form-data" action="uploader.php" method="POST">
Choose project: <select name="projectID">
<?PHP
require_once("../attaskconn/LingoAtTaskService.php");
require_once("../quoteGen/functions/f_sortProjects.php");
ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 1);

$api = new LingoAtTaskService();

try{

	$g = new getQuotableProjects();
	$quotableProjects = new projectStub;

	set_time_limit(60);
	$quotableProjects = $api->getQuotableProjects($g)->return;
	
	$sortedProjects = sortProjects($quotableProjects);
	foreach ($sortedProjects as $qp)
	{
		{
			echo "\t<option value=\"", $qp->id, "\">", $qp->name,  "</option>\n";
			
		}
	}
	echo "</select>\n\n";

	
	unset($api);
}
catch (exception $e)
{
	echo "<br><br>Error:<pre>\n";
	var_dump($e);
	echo "</pre>";
}

?>
</select><br />

<input type="hidden" name="MAX_FILE_SIZE" value="5242880" />
Choose a file to upload: <input name="uploadedfile" type="file" /><br />
<input type="submit" name="upload" value="Upload File" />
</form>

</body>
</html>
