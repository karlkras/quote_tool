<?PHP
//error_reporting(E_ALL ^ E_DEPRECATED);
require_once('../definitions.php');

function calculateMinimum($taskService)
{
$project_minimum = 0;

$projectObj = unserialize($_SESSION['projectObj']);
$rushFee = $_SESSION['rushFee'];

	if (count($taskService->lingTasks) < 2)
	{
		switch ($taskService->lingTasks->targLang)
		{
			case 'Spanish (International)':
			case 'Spanish (Latin America)':
			case 'Spanish (Spain)':
			case 'Spanish (US)':
			case 'Chinese (Simplified)':
			case 'Chinese (Traditional)':
			case 'Chinese (Traditional-Hong Kong)':
			case 'French (Belgium)':
			case 'French (Canada)':
			case 'French (France)':
			case 'Japanese':
			case 'Korean':
			case 'Russian':
			case 'Vietnamese':
				if (($rushFee != 0) && ($project_minimum < 149))
				{
					$project_minimum = 149;
				}
				elseif ($project_minimum < 99)
				{
					$project_minimum = 99;
				}
				break;
					
			case 'Armenian':
			case 'Cambodian':
			case 'German':
			case 'German (Austria)':
			case 'Haitian Creole':
			case 'Italian':
			case 'Polish':
			case 'Portuguese (Brazil)':
			case 'Portuguese (Portugal)':
				if (($rushFee != 0) && ($project_minimum < 159))
				{
					$project_minimum = 159;
				}
				elseif ($project_minimum = 109)
				{
					$project_minimum = 109;
				}
				break;
			default:
				if (($rushFee != 0) && ($project_minimum < 179))
				{
					$project_minimum = 179;
				}
				elseif ($project_minimum < 125)
				{
					$project_minimum = 125;
				}
		}
	}
	else
	{
		foreach ($taskService->lingTasks as $lingTask)
		{
			switch ($lingTask->targLang)
			{
				case 'Spanish (International)':
				case 'Spanish (Latin America)':
				case 'Spanish (Spain)':
				case 'Spanish (US)':
				case 'Chinese (Simplified)':
				case 'Chinese (Traditional)':
				case 'Chinese (Traditional-Hong Kong)':
				case 'French (Belgium)':
				case 'French (Canada)':
				case 'French (France)':
				case 'Japanese':
				case 'Korean':
				case 'Russian':
				case 'Vietnamese':
					if (($rushFee != 0) && ($project_minimum < 149))
					{
						$project_minimum = 149;
					}
					elseif ($project_minimum < 99)
					{
						$project_minimum = 99;
					}
					break;
						
				case 'Armenian':
				case 'Cambodian':
				case 'German':
				case 'German (Austria)':
				case 'Haitian Creole':
				case 'Italian':
				case 'Polish':
				case 'Portuguese (Brazil)':
				case 'Portuguese (Portugal)':
					if (($rushFee != 0) && ($project_minimum < 159))
					{
						$project_minimum = 159;
					}
					elseif ($project_minimum = 109)
					{
						$project_minimum = 109;
					}
					break;
				default:
					if (($rushFee != 0) && ($project_minimum < 179))
					{
						$project_minimum = 179;
					}
					elseif ($project_minimum < 125)
					{
						$project_minimum = 125;
					}
			}
		}
	}
	//check for minimum price from database
	$client_id = $projectObj->company->id;
	$query = "SELECT * FROM clients WHERE attask_id = ".$client_id;
		
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	
	$result = $myDBConn->query($query) or die($myDBConn->error);

	if ($result->num_rows > 0)
	{
		$res = $result->fetch_assoc();
		$db_table_name = $res['table_name'];
		if($rushFee === 'custom25' || $rushFee === 'custom50') {
			$query = "SELECT rush_rate FROM $db_table_name WHERE task_name='Minimum' AND units='hour'";
			$result = $myDBConn->query($query);
			$res1 = $result->fetch_assoc();
			if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
				$query = "SELECT rate FROM $db_table_name WHERE task_name='Minimum' AND units='hour'";
				$result = $myDBConn->query($query) or die($myDBConn->error);
			}
		}
		else {
			$query = "SELECT rate FROM $db_table_name WHERE task_name='Minimum' AND units='hour'";
			$result = $myDBConn->query($query) or die($myDBConn->error);
		}

		
		if ($result->num_rows > 0)
		{
            $result->data_seek(0);
			$res = $result->fetch_array();
			if ($project_minimum < $res[0] / 1000)
				$project_minimum = $res[0] / 1000;
		}
	}
		
	return $project_minimum;
}


?>