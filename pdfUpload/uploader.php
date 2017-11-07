<?PHP
session_start();
require_once('../uuid.php');

//check to see if we're logged in
if (!isset($_SESSION['userID'])) {
	die('You are not properly logged in and are unable to use this application');
} elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID)) {
    die('Fatal Error! Application authentication is not correct');
}

ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 1);
require_once("../attaskconn/LingoAtTaskService.php");



if ((isset($_POST['upload'])) && ($_POST['upload'] == "Upload File"))
{
	$output = "<p><strong>Processing file:</strong> ". $_FILES['uploadedfile']['name'] ."</p>\n";
	if ($_FILES['uploadedfile']['error'] > 0)
	{
		switch ($_FILES['uploadedfile']['error'])
		{
			case UPLOAD_ERR_INI_SIZE:
				$output .= "The uploaded file exceeds the maximum size for PHP<br />\n";
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$output .= "The uploaded file exceeds the maximum size that was specified in the HTML form<br />\n";
				break;
			case UPLOAD_ERR_PARTIAL:
				$output .= "The uploaded file was only partially uploaded<br />\n";
				break;
			case UPLOAD_ERR_NO_FILE:
				$output .= "No file was uploaded<br />\n";
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$output .= "Missing a temporary folder<br />\n";
				break;
			case UPLOAD_ERR_CANT_WRITE:
				$output .= "Failed to write file to disk<br />\n";
				break;
			case UPLOAD_ERR_EXTENSION:
				$output .= "File upload stopped by extension<br />\n";
				break;
			default:
				$output .= "Unknown upload error<br />\n";
				break;
		}
     
	}
/*	else if ($_FILES['uploadedfile']['type'] != 'text/xml')
	{
		$output .= "Error: File is not XML file <br />\n";
	}*/
	else if ($_FILES['uploadedfile']['size'] > 5242880)
	{
		$output .= "Error: File is too large. 5 MB maximum<br />\n";
	}
	else
	{
		
		$api = new LingoAtTaskService();
		try{
			set_time_limit(60);
			$g = new getQuotableProjects();
			$quotableProjects = $api->getQuotableProjects($g)->return;
			
			$projectStub = NULL;
			foreach ($quotableProjects as $qp)
			{
				if ($qp->id == $_POST['projectID'])
				{
					$projectStub = $qp;
					break;
				}
			}
			
			$handle = fopen($_FILES["uploadedfile"]["tmp_name"], "r");
			$fileObj = fread($handle, filesize($_FILES["uploadedfile"]["tmp_name"]));
		
			$a = new attachFileToEstimateTask();
			$a->projectObject = $projectStub;
			$a->fileName = $_FILES["uploadedfile"]["name"];
			$a->fileData = base64_encode($fileObj); 
			set_time_limit(60);
			$api->attachFileToEstimateTask($a);
			

			$g = new getProject($projectStub);
			$projectObj = $api->getProject($g)->return;
			
			$s = new setProjectStatus();
			$s->projectObject = $projectObj;
			$s->status = "AWA";
			$api->setProjectStatus($s);
			
			
			$output .= "file successully uploaded<br><br>";
			$output .= "project status set to Awaiting Client Approval<br><br>";
			
		}
		catch(exception $e)
		{
			$output .= "<br><br>Error attaching file:<pre>\n";
			ob_start();	// start output buffering to capture the var_dump
			var_dump($e);
			$output .= ob_get_clean();	//get the buffer as a string and clean it
			echo "</pre>";
		}
			
	}
	
	
}

?>






<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Upload saved file</title>
</head>

<body>
<?PHP print $output; ?>

<a href="../index.php">Return</a>

</body>
</html>
