<?PHP
include_once('../definitions.php');

function hasTask($clientTableName, $clientTask)
{
	$retVal = false;
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	
	//Return true if client table has task, false if not
	print("<table id='table1'  class='admin' border=0>\n");
	print ("<tr><th colspan=6 class='admin'>Running hasTask</th></tr>\n");
	
	$query = "SELECT task_name FROM " . $clientTableName;
	$result = $myDBConn->query($query) or die($myDBConn->error);

	while ($row =  $result->fetch_assoc())
	{			
		$task = $row['task_name'];
		
		if (stripos($task, $clientTask) !== false)
		{
			$retVal = true;
			break;
		}
	}
	
	$result->free();
	$myDBConn->close();
	
	return $retVal;
}
?>