<?PHP
require_once('../attaskconn/LingoAtTaskService.php');

function viewClient($tableName)
{
	$defaultTasks = array();
	$languageTasks = array();
        $rushColumn = false;
	
	echo '<H2 style="margin-bottom:1px;padding-bottom:1px">Client Specific Information</H2>';
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
        
        //check if the rush column exists or not
	$query = "SHOW COLUMNS FROM `$tableName` LIKE 'rush_rate'";
	$result = $myDBConn->query($query) or die($myDBConn->error);
	$rushColumn = ($result->num_rows)?TRUE:FALSE;
		
	//check for discount
	$query = "SELECT discount, comments FROM clients WHERE table_name=\"".$tableName."\"";
	$result = $myDBConn->query($query) or die($myDBConn->error);
	
	if ($res = $result->fetch_assoc())
	{
		if (is_null($res['discount']))
		{
			$discountString = '<tr><td colspan="2" class="rightAligned admin">No client discount has been established</td></tr>';
			$clientDiscount = -1;
		}
		else
		{
			$clientDiscount = $res['discount'];
			$discountString = '<tr><td class="rightAligned admin">'.$clientDiscount.'</td><td class="admin">percent</td></tr>';
		}
		
		echo "<table border=0 id=\"client-discount\" class=\"inlineTable admin\" style=\"min-width:200px\">\n";
		echo "<tr><th class=\"admin\" style=\"padding-right:15px;\" colspan=\"2\"><b>Discount</b></th></tr>\n";
		echo $discountString;
		echo "</table>\n";
			
	}
	$result->free();
	
	$clientNotes = '';
	//get any comments
	if (is_null($res['comments']))
	{
		$clientNotes = 'No notes    ';
		$commentName = '&lt;none&gt;';
		$commentDate = '&lt;unknown&gt;';
	}
	else
	{
		$noteID = $res['comments'];
		$query = "SELECT updateDate, username, userNotes FROM comments WHERE id=$noteID";
		$result = $myDBConn->query($query) or die($myDBConn->error);
		if ($res = $result->fetch_assoc())
		{
			if (is_null($res['userNotes']))
			{
				$clientNotes = 'No notes  ';
				$commentName = '&lt;none&gt;';
				$commentDate = '&lt;unknown&gt;';
			}
			else
			{
				$clientNotes = $res['userNotes'];
				is_null($res['updateDate']) ? $commentDate = '&lt;unknown&gt;' : $commentDate = $res['updateDate'];
				if (is_null($res['username']))
				{
					$commentName = '&lt;none&gt;';
				}
				else
				{
					//get the user's first and last name from attask
					$attaskUserName = $res['username'];
					
					set_time_limit(60);
					try{
						$api = new LingoAtTaskService();
						$g = new getUserByUsername;
						$g->accountName = $attaskUserName;
						$user = $api->getUserByUsername($g)->return;
						
						$commentName = $user->firstName ." ". $user->lastName;
						
					}
					catch(exception $e)
					{
						$commentName = '&lt;error&gt;';
					}
				}
			}
		}
		else
		{
			$clientNotes = 'No notes ';
			$commentName = '&lt;none&gt;';
			$commentDate = '&lt;unknown&gt;';
		}
		$result->free();
		
	}
	
	echo "<table border=0 id=\"client-notes\" class=\"inlineTable admin\" style=\"min-width:400px\">\n";
	echo "<tr><th class=\"admin\" style=\"padding-right:15px;\" ><b>Notes</b></th></tr>\n";
	echo "<tr><td class=\"admin note\" style=\"width:375px\">$clientNotes</td></tr>";
	echo "\t<tr class=\"nohover\"><td class=\"admin breadcrumbs\">Comment by: $commentName on $commentDate</td></tr>\n";
	echo "</table>\n";
	
	
	//check for attahced files.
	$fileName = '';
	$filePath = '';
	$fileDate = '';
	$fileUser = '';
	$fileComment = '';
	
	//first get the client attask id
	$query = "SELECT attask_id FROM clients WHERE table_name=\"".$tableName."\"";
	$result = $myDBConn->query($query) or die($myDBConn->error);
	
	if ($res = $result->fetch_assoc())
	{
		$clientAttaskId = $res['attask_id'];
		$result->free();
		$query = "SELECT filePath, fileComments, uploadedBy, uploadDate FROM clientfiles WHERE attaskID=$clientAttaskId";
		
		if ($result = $myDBConn->query($query))
		{
			if ( $result->num_rows == 0)
			{
				echo "<table border=0 id=\"client_files\" class=\"inlineTable admin\" style=\"min-width:550px\">\n";
				echo "\t<tr><th class=\"admin\" colspan=3>Attached files</th></tr>\n";
				echo "\t<tr class=\"nohover\"><td class=\"admin\">No attached files found</td></tr>\n";
				echo "</table>\n\n";
			}
			else
			{
				echo "<table border=0 id=\"client_files\" class=\"inlineTable admin\" style=\"min-width:550px\">\n";
				echo "\t<tr><th class=\"admin\" colspan=2>Attached files</th></tr>\n";
				while ($res = $result->fetch_assoc())
				{
					$path_parts = pathinfo($res['filePath']);
					$fileName = $path_parts['basename'];
					$filePath = $path_parts['dirname'];
					echo "\t<tr class=\"nohover\">\n";
					echo "\t\t<td class=\"admin\">\n";
					echo "\t\t\t<a href=\".".$res['filePath']."\" target=\"_new\"><strong>".$fileName."</strong></a>\n";
					echo "\t\t</td>\n";
					echo "\t\t<td class=\"admin breadcrumbs\">\n";
					echo "\t\t\t<a href=\".".$res['filePath']."\" target=\"_new\">Download</a>\n";
					echo "\t\t</td>\n";
					echo "\t</tr>\n";
					echo "\t<tr class=\"nohover\">\n";
					echo "\t\t<td class=\"admin breadcrumbs\">\n";
					echo "\t\t\t<em>Uploaded by ".$res['uploadedBy']." on ".$res['uploadDate']."</em>\n";
					echo "\t\t</td>\n";
					echo "\t\t<td class=\"admin breadcrumbs\">&nbsp;</td>\n";
					echo "\t</tr>\n";
					echo "\t<tr class=\"nohover last\">\n";
					echo "\t\t<td class=\"admin last\">".$res['fileComments']."</td>\n";
					echo "\t\t<td class=\"admin breadcrumbs last\">&nbsp;</td>\n";
					echo "\t</tr>\n";
					
				}
				echo "\t<tr class=\"nohover\">";
				echo "<td class=\"admin centerAligned breadcrumbs\" colspan=3>&nbsp;</td></tr>\n";
				echo "</table>\n\n";
			}
			$result->free();
		}
	}
	
	
	//get non-language tasks
	echo "<hr><H2 style=\"margin-bottom:1px;padding-bottom:1px\">Non-Language Tasks</H2>";
	$query = "SELECT * FROM " . $tableName;
	$result = $myDBConn->query($query) or die($myDBConn->error);
	
	while ($row =  $result->fetch_assoc())
	{
		$task = $row['task_name'];
		$rate = $row['rate'] / 1000;
		$units = $row['units'];
		if ($rushColumn)
			$rushRate = $row['rush_rate'] / 1000;
		else
			$rushRate = 0;
		
		//parse out the task name to determine if there is a language pair attached
		parseTasks($task, $rate, $rushRate, $units, $defaultTasks, $languageTasks);
		
	}
	$result->free();
	
	if (count($defaultTasks) > 0)
	{
		echo "<table border=0 id=\"non-language-tasks\" class=\"inlineTable admin\">";
		echo "<tr><th class=\"admin\"><b>Task</b></th><th class=\"admin\"><b>Rate</b></th>";
		if ($rushColumn)
			echo "<th class=\"admin\">Rush Rate</th>";
		echo "<th class=\"admin\"><b>Per</b></th></tr>";
                
		foreach ($defaultTasks as $t)
		{
			echo "<tr><td class=\"admin\">", $t->get_name();
			if ($t->get_subCategory() != "none")
			{
				echo " ", $t->get_subCategory();
			}
			echo "</td><td class=\"rightAligned admin\">", number_format($t->get_rate(),3), "</td>";
			if ($rushColumn)
				echo "<td class=\"rightAligned admin\">", number_format($t->get_rushRate(),3),"</td>";
			echo "<td class=\"admin\">", $t->get_unit(), "</td>";
                        /*$temp_client = str_replace(" ","_",$clientName);
			$temp_client = urlencode($temp_client);
			$temp_name = str_replace(" ","_",$t->get_name());
			$temp_name = urlencode($temp_name);
			$temp_sub = str_replace(" ", "_", $t->get_subCategory());
			$temp_sub = urlencode($temp_sub);*/
			
			echo "</td></tr>";
		}
		
		echo "</table>";
	}
	else
	{
		echo "<p>No non-language tasks found</p>\n";
	}
	
	
	echo "<hr><H2 style=\"margin-bottom:1px;padding-bottom:1px\">Language Tasks</H2>";
	
	if (count($languageTasks) < 1)
	{
		echo "<p>No language tasks found</p>\n";
	}
	
	foreach ($languageTasks as $key1=>$value1)
	{
		
		foreach ($value1 as $key2=>$value2)
		{
			echo "<table border=0 class=\"inlineTable admin\" id=\"". str_replace(" ","_",$key1), "_", str_replace(" ","_",$key2) ."\">\n";
			echo "\t<tr><td colspan=5 class=\"language admin\">",str_replace("_"," ",$key1), " to ", str_replace("_"," ",$key2), "</td></tr>\n";
			echo "<tr><th class=\"admin\"><b>Task</b></th><th class=\"admin\"><b>Rate</b></th>";
			if ($rushColumn)
				echo "<th class=\"admin\">Rush Rate</th>";
			echo "<th class=\"admin\"><b>Per</b></th></tr>";
			
			foreach ($value2 as $value3)
			{
				echo "<tr><td class=\"admin\">", $value3->get_name();
				if ($value3->get_subCategory() != "none")
				{
					echo " ", $value3->get_subCategory();
				}
				
				/*$temp_client = str_replace(" ","_",$clientName);
				$temp_client = urlencode($temp_client);
				$temp_name = str_replace(" ","_",$value3->get_name());
				$temp_name = str_replace("+","%2B",$temp_name);
				$temp_name = urlencode($temp_name);
				$temp_sub = str_replace(" ", "_", $value3->get_subCategory());
				$temp_sub = urlencode($temp_sub);
				$temp_src = urlencode($key1);
				$temp_tgt = urlencode($key2);*/
				
				echo "</td><td align=right class=\"admin\">", number_format($value3->get_rate(),3), "</td>";
				if ($rushColumn)
					echo "<td class=\"rightAligned admin\">", number_format($value3->get_rushRate(),3), "</td>";
				echo "<td class=\"admin\">", $value3->get_unit(), "</td>";
				echo "</tr>";
			}
			
			echo "</table>&nbsp;";
		}
		
		
	}
	
	
	$myDBConn->close();

	
}

?>