<?PHP
include_once('../definitions.php');

//script to rename Web_site_Engineering tasks to Website_Engineering in each customer's table so that pricing can be pulled for those items.
function renameToWebsiteEngineering()
{
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	
	$query = "SELECT * FROM clients ORDER BY Name";
	$clients = $myDBConn->query($query) or die($myDBConn->error);
	
	//fetch all clients table names
	while ($row =  $clients->fetch_assoc())
	{
		$tableName = $row['table_name'];
		
		
		//fetch all clients table names that have the wrong Web site Engineer task(s)
		$query = "SELECT task_name FROM " . $tableName;
		$result = $myDBConn->query($query) or die($myDBConn->error);
		$result2 = $myDBConn->query($query) or die($myDBConn->error);

		while ($row =  $result->fetch_assoc())
		{			
			$task = $row['task_name'];
			if (stripos($task, 'Web_site_Engineering') !== false) 
			{
				//check if client has both Web_site_Engineering and Website_Engineering as tasks and delete Web_site_Engineering if yes
				while ($row2 =  $result2->fetch_assoc())
				{			
					$task2 = $row2['task_name'];
					
					if (stripos($task2, 'Website_Engineering') !== false)
					{
						//delete Web_site_Engineering;
						$delete = $myDBConn->query("DELETE FROM " . $tableName . " WHERE task_name='Web_site_Engineering'");
						$delete = $myDBConn->query($query) or die($myDBConn->error);
						if (!$delete)
						{		
							$error = "TRUE";
						}
						else
						{
							$error = "FALSE";
						}
					}
				}
			}
		}
		$result->free();
		$result2->free();
		
		//replace Web_site_Engineering with Website_Engineering in database
		$query = "UPDATE " . $tableName . " SET task_name='Website_Engineering' WHERE task_name='Web_site_Engineering'";
		$update = $myDBConn->query($query) or die($myDBConn->error);
		
		if (!$update)
		{		
			$error = "TRUE";
		}
		else
		{
			$error = "FALSE";
		}
	}
	
	$clients->free();
	$myDBConn->close();
}

?>