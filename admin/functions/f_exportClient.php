<?PHP
function exportClient($clientID)
{
$tableName = "";
$clientName = "";
		
	getClientFromId($clientID, $clientName, $tableName);
	
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	
	$query = "SELECT * FROM ".$tableName;
	$result = $myDBConn->query($query) or die($myDBConn->error);

	$fileName = substr($tableName,7).".csv";
	$fh = fopen($fileName, 'w') or die("can't open file");
	
	while ($res = $result->fetch_assoc())
	{
		$taskName = '';
		$wordType = '';
		$sourceLang = '';
		$targetLang = '';
		
		
		$dbTask = $res['task_name'];
		$pieces = explode("=",$dbTask);
		
		if (count($pieces) > 1) //then we have language pairs
		{
			$sourceLang = str_replace("_"," ",$pieces[1]);
			$targetLang = str_replace("_"," ", $pieces[2]);
		}
		
		$tempName = $pieces[0];		//now explode this to check for word type
		$pieces = explode("#",$tempName);
		
		$taskName = str_replace("_"," ",$pieces[0]);
		if (count($pieces) > 1)
		{
			$wordType = str_replace("_"," ",$pieces[1]);
		}
		
		$outputString = $taskName.",".$wordType.",".$sourceLang.",".$targetLang.",".$res['rate']/1000 .",".$res['units']."\n";
		fwrite($fh, $outputString);

		
	}
	fclose($fh);
	$result->free();
	$myDBConn->close();

	if (file_exists($fileName))
	{
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($fileName));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($fileName));
		ob_clean();
		flush();
		readfile($fileName);
		
		//clean up the temporary file from the server
		unlink($fileName);
		exit;
	}
	else
	{
		echo "Temporary file could not be created.<br>";
	}
}
?>