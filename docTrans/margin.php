<?PHP
session_start();
require_once('../uuid.php');

//check to see if we're logged in
if (!isset($_SESSION['userID']))
{
	header('location:../login.php?location=standard');
	exit;
}
elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID))
{
	header('location:../login.php?err=6&location=standard');
	exit;
}

require_once('../attaskconn/LingoAtTaskService.php');
require_once('pm.php');
include_once('pricing/margin.php');
include_once('pricing/rates.php');
include_once('saveXML.php');

$passTrados = $_SESSION['passTrados'];
$useUSLinguists = $_SESSION['useUSLinguists'];
$pmSurcharge = $_SESSION['pmSurcharge'];
$projectObj = unserialize($_SESSION['projectObj']);

//process the form here
if (isset($_GET['action']) && ($_GET['action'] == 'process'))
{

	if (is_numeric($_POST['percent']))
	{
		if (($_POST['percent'] >= 0) && ($_POST['percent'] <= 100))
		{
			$sellRates = array();
			$totalPrice = margin($_POST['percent'],$passTrados,$pmSurcharge, $sellRates);
			$taskService = unserialize($_SESSION['taskService']);
			$dtpData = unserialize($_SESSION['dtpData']);
			$projectObj->budget = $_SESSION['totalCost'];
			$_SESSION['totalprice'] = $totalPrice;
			save_to_xml($taskService, $projectObj, $sellRates, $passTrados, $_SESSION['bundleInternal']);
			
			exit;
		}
		else
		{
			header('location: ?err=range');
			exit;
		}
	}
	else
	{
		header('location: ?err=nostring');
		exit;
	}
}




?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Enter Margin Value</title>
</head>

<body>
<?PHP
if (isset($_GET['err']))
{
	if ($_GET['err'] == 'nostring')
	{
		echo "<p style=\"color:red\">Value entered must be numbers only</p>";
	}
	if ($_GET['err'] == 'range')
	{
		echo "<p style=\"color:red\">Value entered must be between 0 and 100</p>";
	}
	if ($_GET['err'] == 'pricing')
	{
		echo "<p style=\"color:red\">Error. Client's Pricing Scheme is set for Client-Specific, but no data exists in Database.<br>";
		echo "Defaulting to Margin-based Pricing.</p>";
	}
}
?>
<form action="?action=process" method="post" name="margin" >
Enter margin value: <input type="text" value="40" name="percent" size="5" />%<br />
<input type="submit" name="submit" value="Submit" />

</form>
<div style="text-align: left; margin-top: 8px;">
    <a href="../index.php"><button >Return to Main Page</button></a>
</div>
<div style="text-align: left; margin-top: 8px;">
    <a href="index.php"><button >Return to Standard Quote page</button></a>
</div>

</body>
</html>
