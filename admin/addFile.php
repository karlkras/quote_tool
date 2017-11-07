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

$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
	exit;
}

if (isset($_GET['action']) && ($_GET['action'] == 'upload'))
{
	$clientName = $_GET['target'];
	
	//get the file from the temporary file php uploaded
	//and store it more permanently.
	
	//first check to make sure we have a directory to put it in,
	//and if not make it
	$targetRootDir = "files";
	$targetUniqueDir = uniqid();
	
	if (!file_exists($targetRootDir) && !is_dir($targetRootDir)) 
	{
    	if (!mkdir($targetRootDir))
		{
			$error = true;
			$errorString = 'Could not create folder '.$targetRootDir;
		}
	} 
	if (!file_exists($targetRootDir."/".$targetUniqueDir) && !is_dir($targetRootDir."/".$targetUniqueDir)) 
	{
    	if(!mkdir($targetRootDir."/".$targetUniqueDir))
		{
			$error = true;
			$errorString = 'Could not create folder '.$targetRootDir.'/'.$targetUniqueDir;
		}
	} 
	
	$tmp_name = $_FILES["fileToUpload"]["tmp_name"];
	$name = $_FILES["fileToUpload"]["name"];
	if (!move_uploaded_file($tmp_name, "$targetRootDir/$targetUniqueDir/$name"))
	{
		$error = true;
		$errorString = 'Could not upload file';
	}
	
	
	
	$fullFileName = "/".$targetRootDir."/".$targetUniqueDir."/".$name;
	$clientID = $_POST['clientID'];
	$attaskID = $_POST['attaskID'];
	$fileComments = $myDBConn->real_escape_string($_POST['fileComments']);
	$userName = $_SESSION['userFirstName']. " " .$_SESSION['userLastName'];
	$updateDate = date('Y-m-d');
	
	
	$query = "INSERT INTO clientfiles (clientID, attaskID, filePath, fileComments, uploadedBy, uploadDate) ";
	$query .= "VALUES ($clientID, $attaskID, '$fullFileName', '$fileComments', '$userName', '$updateDate')";
	
	if ($myDBConn->query($query) === TRUE)
	{
		//file was successfully added, so return to the client's edit page
		$myDBConn->close();
		header('location:customPricing.php?action=edit&target='.$_GET['target']);
		exit;
		
	}
	else
	{
		$error = true;
		$errorString = 'Could not add file to database: '. $myDBConn->error;
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
	
	
	$query = "SELECT Name, ID, attask_id FROM clients WHERE table_name=\"".$tableName."\"";
	$result = $myDBConn->query($query);
	
	if ($result->num_rows > 0)
	{
		$res = $result->fetch_assoc();
		$prettyName = $res['Name'];
		$clientID = $res['ID'];
		$clientAttask = $res['attask_id'];
	}
	$result->free();

}
	
$myDBConn->close();

?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Edit Client Discount Rate</title>
<link href="admin.css" rel="stylesheet" type="text/css">
</head>

<body>
<?PHP if ($error): ?>
	<h2>Error: <?PHP echo $errorString; ?></h2>
	<p>Exiting</p>
<?PHP else: ?>
	<h2>Add file for <?PHP echo $prettyName; ?></h2>
	
	<form action="addFile.php?action=upload&target=<?PHP echo $_GET['target'];?>" method="post" enctype="multipart/form-data">
		<strong>File to upload:</strong> <input type="file" name="fileToUpload" id="fileToUpload"><br/>
		<br>
		Comments:<br><textarea name="fileComments" id="fileComments" cols="50"></textarea><br>
		<input type="hidden" name="clientID" value="<?PHP echo $clientID; ?>">
		<input type="hidden" name="attaskID" value="<?PHP echo $clientAttask; ?>">
		<input type="submit" name="Submit" value="Upload" />
		<input type="button" name="cancel" value="Cancel" onClick="window.location.href = 'customPricing.php?action=edit&target=<?PHP echo $_GET['target'];?>'">
	</form>
	
	</p>
<?PHP endif; ?>
</body>
</html>