<?PHP
include_once("../../definitions.php");

$errorFlag = 'FALSE';
$errorString = '';
$log = "in the beginning...\n";

if (!isset($_GET['id']))
{
	$errorFlag = 'TRUE';
	$errorString = 'File ID value not passed into function';
}
else
{
	$log .= "file id successfully retrieved from GET data\n";
	$fileID = $_GET['id'];
	$fileName = 'null';
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		$errorFlag = "TRUE";
		$errorString = "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
	}
	else
	{
		$log .= "Sucessfully connected to database\n";
		//get the file name/path from the DB
		$query = "SELECT filePath FROM clientfiles WHERE ID=$fileID";
		$result = $myDBConn->query($query);
		
		$log .= "sucessfully returned filepath from database\n";
		if (!($res = $result->fetch_assoc()))
		{
			$errorFlag = 'TRUE';
			$errorString = "Error getting filepath from database.\n".$myDBConn->error;
			$result->free();
		}
		else
		{
			$result->free();
			$log .= "sucessfully fetched data from result\n";
			$path_parts = pathinfo($res['filePath']);
			$fileName = $path_parts['basename'];
			$filePath = $path_parts['dirname'];
			
			//delete the local file
			if (!(unlink('..'.$res['filePath'])))
			{
				$errorFlag = 'TRUE';
				$errorString = "Unable to delete the file $fileName";
			}
			else
			{
				$log .= "sucessfully delete file\n";
				//delete the folder
				if (!(rmdir('..'.$filePath)))
				{
					$errorFlag = 'TRUE';
					$errorString = "Unable to delete folder from server";
				}
				else
				{
					$log .= "sucessfully deleted folder\n";
					//drop the info from the DB
					$query = "DELETE FROM clientfiles WHERE ID=$fileID";
					if (!$myDBConn->query($query))
					{
						$errorFlag = 'TRUE';
						$errorString = "Error deleting file entry from database.\n".$myDBConn->error;
					}
					else
					{
						$log .= "Sucessfully delete from database\n";
					}
				}
			}
		}
	}
	$myDBConn->close();
	$log .= "closed database connection\n";
}

	
header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><delete>';
echo "\t<errorflag>$errorFlag</errorflag>\n";
if ($errorString == '')
	echo '\t<errorString>Unknown Error</errorString>\n';
else
	echo "\t<errorString>$errorString</errorString>\n";

echo "\t<filename>$fileName</filename>\n";
echo "\t<target>".$_GET['target']."</target>\n";
echo "\t<log><![CDATA[$log]]></log>\n";
echo "</delete>";
?>