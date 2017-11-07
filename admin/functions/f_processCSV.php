<?PHP
function processCSV($clientID, $clientName, $tableName)
{
	if ($lines = file($_FILES['uploadedfile']['tmp_name'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))
	{
		echo "<p>Removing data from <strong>".$clientName."'s</strong> database entry</p>";
		
		$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
		if ($myDBConn->connect_errno)
		{
			echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
			exit;
		}
		
		$query = "DELETE FROM ".$tableName;
		
		$result = $myDBConn->query($query);
		
		if ($result === FALSE)
		{
			$string = "<p>Could not delete client table in database: ". $myDBConn->error."</p>";
			die($string);
		}
		
		echo "<p>Adding new data into database</p>";
		
		foreach ($lines as $line_num => $line) 
		{
			$task = ''; 
			$textType = ''; 
			$sourceLang = '';
			$targetLang = '';
			$rate = '';
			$unit = '';
			list($task, $textType, $sourceLang, $targetLang, $rate, $unit) = explode(',',$line);
			
			echo "<p>inserting <i>$task</i> into database... ";
			
			if ($textType != '')
			{
				$task .= "#".$textType."#";
			}
			if ($sourceLang != '')
			{
				$task .= "=".$sourceLang;
			}
			if ($targetLang != '')
			{
				$task .= "=".$targetLang;
			}
			$task = str_replace(' ','_',$task);
			
			$rate = $rate * 1000;
			
			$query = "INSERT INTO $tableName (task_name, rate, units) VALUES ('$task', $rate, '$unit')";
			
			
			if (!$myDBConn->query($query))
			{
				echo "Failed: <strong>", $myDBConn->error,"</strong></p>\n";
			}
			else
			{
				echo "<strong>Success</strong>.</p>\n";
			}
			
		}
		
		
		
		$myDBConn->close();
		echo "<p>Data sucessfully writen to client <strong>".$clientName."'s</strong> database</p>";
		
		return true;
		
	}
	else
	{
		echo "<p>Unable to open file</p>";
		return false;
	}
}
?>