<?PHP
include_once("../definitions.php");

if (!isset($_GET['client']))
{
	echo "Client field is not set!";
	exit;
}

$table_name = 'client_' . str_replace(" ", "_", $_GET['client']);

//check if table exists
$query = "SHOW TABLES LIKE '$table_name'";
$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
	exit;
}

$tbls = $myDBConn->query($query);

if ($tbls === FALSE)
{
	$string = "Could not check for client table in database: ". $myDBConn->error."<br>";
	die($string);
}

if ($tbls->num_rows == 0)
{
	$string = "No client pricing table is setup for " . $_GET['client'].". <br>Please add the client to the database before uploading pricing.";
	die($string);
}

$tbls->free();

if ($lines = file('test.csv', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))
{
	foreach ($lines as $line_num => $line) 
	{
		$task = ''; 
		$textType = ''; 
		$sourceLang = '';
		$targetLang = '';
		$rate = '';
		$unit = '';
		list($task, $textType, $sourceLang, $targetLang, $rate, $unit) = explode(',',$line);
		
		echo "inserting <i>$task</i> into database... ";
		
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
		
		$query = "INSERT INTO $table_name (task_name, rate, units) VALUES ('$task', $rate, '$unit')";
		
		if (!$myDBConn->query($query))
		{
			echo "Failed: <strong>", $myDBConn->error,"</strong><br>\n";
		}
		else
		{
			echo "<strong>Success</strong>.<br>\n";
		}
		
	}
}
else
{
	echo "Unable to open file";
}
$myDBConn->close();
?>