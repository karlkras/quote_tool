<?PHP
include_once("../../definitions.php");
session_start();

$errorFlag = 'FALSE';
$errorString = '';
$userNotes = $_GET['text'];
$tableName = $_GET['cid'];
$noteID = 0;
$updateDate = date('Y-m-d');
$updateUser = $_SESSION['userName'];
$userPrettyName = $_SESSION['userFirstName']. " " .$_SESSION['userLastName'];


if (!isset($_GET['id']))
{
	$errorFlag = 'TRUE';
	$errorString = 'Note ID value not passed into function';
}
else
{
	$noteID = $_GET['id'];
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		$errorFlag = "TRUE";
		$errorString = "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
	}
	else
	{
		if ($noteID == -1)	//then we need to create a new note
		{
			
			$query = "INSERT INTO comments (updateDate, username, userNotes) VALUES ('$updateDate', '$updateUser', '";
			$query .= $myDBConn->real_escape_string(urldecode($userNotes)) ."')";
			if ($myDBConn->query($query) !== TRUE)
			{
				$errorFlag = 'TRUE';
				$errorString = "Error inserting notes into database.\n".$myDBConn->error;
			}
			else
			{
				$noteID = $myDBConn->insert_id;	//gets the primary key value of the previous insert statement
				$query = "UPDATE clients SET comments=$noteID WHERE table_name='$tableName'";			

				if ($myDBConn->query($query) !== TRUE)
				{
					$errorFlag = 'TRUE';
					$errorString = "Error updating ID in database.\n".$myDBConn->error;
				}
			}
		}
		else	//we update the existing note
		{
			
			$query = "UPDATE comments SET userNotes='".$myDBConn->real_escape_string(urldecode($userNotes))."', ";
			$query .= "updateDate='$updateDate', username='$updateUser'";
			$query .= "WHERE id=$noteID";
			if ($myDBConn->query($query) !== TRUE)
			{
				$errorFlag = 'TRUE';
				$errorString = "Error updating note in database.\n".$myDBConn->error;
			}
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


echo "<noteID>$noteID</noteID>\n";
echo "<userNotes>$userNotes</userNotes>\n";
echo "<tableName>$tableName</tableName>\n";
echo "<date>$updateDate</date>\n";
echo "<user>$updateUser</user>\n";
echo "<userPretty>$userPrettyName</userPretty>\n";
echo "</update>";




?>
