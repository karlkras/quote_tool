<?PHP
include_once("../../definitions.php");
$errorFlag = 'FALSE';
$errorString = '';
$fileNotes = '';

$fileID = -1;
$tableName = $_GET['cid'];


if (!isset($_GET['id']))
{
	$errorFlag = 'TRUE';
	$errorString = 'File ID value not passed into function';
}
else
{
	if ($_GET['id'] == -1)
	{
		$fileNotes = '';
	}
	
	$fileID = $_GET['id'];
	
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		$errorFlag = "TRUE";
		$errorString = "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
	}
	else
	{
	
		$query = "SELECT fileComments FROM clientfiles WHERE ID=$fileID";
		if (!($result = $myDBConn->query($query)))
		{
			$errorFlag = 'TRUE';
			$errorString = "error retrieving comments from database.\n".$myDBConn->error;
		}
		else
		{
			if (!($res = $result->fetch_assoc()))
			{
				$errorFlag = 'TRUE';
				$errorString = "error parsing comments.\n".$myDBConn->error;
			}
			else
			{
				if (is_null($res['fileComments']))
				{
					$fileNotes = '';
				}
				else
				{
					$fileNotes = $res['fileComments'];
				}
			}
			$result->free();
		}
		$myDBConn->close();
	}
}


header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><update>';
echo "<errorflag>FALSE</errorflag>\n";
if ($errorString == '')
	echo '<errorString>Unknown Error</errorString>\n';
else
	echo "<errorString>$errorString</errorString>\n";

if ($fileNotes == '')
	echo '<fileNotes>NONE</fileNotes>\n';
else
	echo "<fileNotes>$fileNotes</fileNotes>\n";

echo "<fileID>$fileID</fileID>\n";
echo "<tableName>$tableName</tableName>\n";
echo "</update>";




?>
