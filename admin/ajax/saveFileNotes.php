<?PHP
include_once("../../definitions.php");
session_start();


$errorFlag = 'FALSE';
$errorString = '';
$fileNotes = $_GET['text'];
$tableName = $_GET['cid'];
$fileID = 0;
$updateDate = date('Y-m-d');
$updateUser = $_SESSION['userName'];
$userPrettyName = $_SESSION['userFirstName']. " " .$_SESSION['userLastName'];


if (!isset($_GET['id']))
{
	$errorFlag = 'TRUE';
	$errorString = 'File ID value not passed into function';
}
else
{
	$fileID = $_GET['id'];
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		$errorFlag = "TRUE";
		$errorString = "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
	}
	else
	{
		$query = "UPDATE clientfiles SET fileComments='".$myDBConn->real_escape_string(urldecode($fileNotes))."', ";
		$query .= "uploadDate='$updateDate', uploadedBy='$updateUser'";
		$query .= "WHERE id=$fileID";
		if ($myDBConn->query($query) !== TRUE)
		{
			$errorFlag = 'TRUE';
			$errorString = "Error updating comments in database.\n".$myDBConn->error;
		}
		
	}
	$myDBConn->close();
}


header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><update>';
echo "<errorflag>$errorFlag</errorflag>\n";
if ($errorString == '')
	echo '<errorString>Unknown Error</errorString>\n';
else
	echo "<errorString>$errorString</errorString>\n";


echo "<fileID>$fileID</fileID>\n";
echo "<fileNotes>$fileNotes</fileNotes>\n";
echo "<tableName>$tableName</tableName>\n";
echo "<date>$updateDate</date>\n";
echo "<user>$updateUser</user>\n";
echo "<userPretty>$userPrettyName</userPretty>\n";
echo "</update>";




?>
