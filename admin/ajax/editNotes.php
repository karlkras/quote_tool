<?PHP
include_once("../../definitions.php");
$errorFlag = 'FALSE';
$errorString = '';
$userNotes = '';

$noteID = -1;
$tableName = $_GET['cid'];


if (!isset($_GET['id']))
{
	$errorFlag = 'TRUE';
	$errorString = 'Note ID value not passed into function';
}
else
{
	if ($_GET['id'] == -1)
	{
		$userNotes = '';
	}
	
	$noteID = $_GET['id'];
	
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		$errorFlag = "TRUE";
		$errorString = "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
	}
	else
	{
		$query = "SELECT userNotes FROM comments WHERE id=$noteID";
		if (!($result = $myDBConn->query($query)))
		{
			$errorFlag = 'TRUE';
			$errorString = "Could not retrieve notes from database.\n".$myDBConn->error;
		}
		else
		{
			if (!($res = $result->fetch_assoc()))
			{
				$errorFlag = 'TRUE';
				$errorString = "Could not parse results from database.\n".$myDBConn->error;
			}
			else
			{
				if (is_null($res['userNotes']))
				{
					$userNotes = '';
				}
				else
				{
					$userNotes = $res['userNotes'];
				}
				$result->free();
			}
		}
	}
	$myDBConn->close();
}


header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><update>';
echo "<errorflag>FALSE</errorflag>\n";
if ($errorString == '')
	echo '<errorString>Unknown Error</errorString>\n';
else
	echo "<errorString>$errorString</errorString>\n";

if ($userNotes == '')
	echo '<userNotes>NONE</userNotes>\n';
else
	echo "<userNotes>$userNotes</userNotes>\n";

echo "<noteID>$noteID</noteID>\n";
echo "<tableName>$tableName</tableName>\n";
echo "</update>";




?>
