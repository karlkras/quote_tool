<?PHP
//check to see if we're logged in
session_start();
require_once('uuid.php');

if (!isset($_SESSION['userID']))
{
	header('location:../login.php?location=admin');
	exit;
	
}
elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID))
{
	header('location:../login.php?err=6&location=admin');
	exit;
}

//if we're logged in, check to see if we have admin permissions
if ((!isset($_SESSION['isAdmin'])) || (!$_SESSION['isAdmin']))
{
	header('location:./customPricing.php');
	exit;
}


require_once("../definitions.php");
$error = false;

if (isset($_GET['action']) && ($_GET['action'] == 'deleteConfirmed'))
{
	$clientName = $_GET['target'];
	$tableName = 'client_' . $clientName;
	
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	
	$query = "UPDATE clients SET discount = NULL WHERE table_name='$tableName'";
	if ($myDBConn->query($query) === TRUE)
	{
		//discount was updated correctly so send them back to the 
		//client's edit page
		$myDBConn->close();
		header('location:customPricing.php?action=edit&target='.$_GET['target']);
		exit;
		
	}
	else
	{
		$error = true;
		$errorString = 'Could not update discount in database: '. $myDBConn->error;
		
	}
}

if (!isset($_GET['target']))
{
	$error = true;
	$errorString = "Target client name not set";
}
else
{
	$clientName = $_GET['target'];
	$tableName = 'client_' . $clientName;
		
	if (!isset($myDBConn) || (!is_a($myDBConn,"mysqli")))
	{
		$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
		if ($myDBConn->connect_errno)
		{
			echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
			exit;
		}
	}
	$query = "SELECT Name, discount FROM clients WHERE table_name=\"".$tableName."\"";
	$result = $myDBConn->query($query) or die($myDBConn->error);
	
	if ($res = $result->fetch_assoc())
	{
		$prettyName = $res['Name'];
		if (is_null($res['discount']))
		{
			$clientDiscount = 0;
		}
		else
		{
			$clientDiscount = $res['discount'];
			
		}
		$result->free();
	}
	$myDBConn->close();
}

?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Delete Discount</title>
<link href="admin.css" rel="stylesheet" type="text/css">
</head>

<body>
<?PHP if ($error): ?>
	<h2>Error: <?PHP echo $errorString; ?></h2>
	<p>Exiting</p>
<?PHP else: ?>
	<h3>Are you sure you want to delete the discount for for <?PHP echo $prettyName; ?>?</h3>
	<p><strong>Existing discount:</strong> <?PHP echo $clientDiscount; ?> percent<br>
	<input type="button" name="confirm" value="Yes, delete it" onClick="window.location.href = 'deleteDiscount.php?action=deleteConfirmed&target=<?PHP echo $_GET['target'];?>'"> &nbsp; 
	<input type="button" name="cancel" value="No, leave it" onClick="window.location.href = 'customPricing.php?action=edit&target=<?PHP echo $_GET['target'];?>'">
	</p>
<?PHP endif; ?>

</body>
</html>