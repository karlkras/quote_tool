<?PHP
function get_dtp_hourly($clientScheme)
{
	$dtpHourly = 0;
	$rateRush = 1;
	$rushFee = $_SESSION['rushFee'];

	//get the DTP pricing from the database, if it exists
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	
	if($rushFee === 'custom25' || $rushFee === 'custom50') {
		$query = "SELECT rush_rate FROM ". $clientScheme. " WHERE task_name = 'Formatting_(DTP)'";
		$result = $myDBConn->query($query);
		$res1 = $result->fetch_assoc();
		if($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
			$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = 'Formatting_(DTP)'";
			$result = $myDBConn->query($query) or die($myDBConn->error);
			if($rushFee === 'custom25') {
				$rateRush = 1.25;
			}
			else{
				$rateRush = 1.5;
			}
		}
	}
	else {
		$query = "SELECT rate FROM ". $clientScheme. " WHERE task_name = 'Formatting_(DTP)'";
		$result = $myDBConn->query($query) or die($myDBConn->error);
	}

    $result->data_seek(0);
	$res =  $result->fetch_row();
	if ($res != NULL)
	{
		$dtpHourly = ($res[0]/1000)* $rateRush;
	}
	else
	{
		$dtpHourly = -1;
	}
	$result->free();
	$myDBConn->close();
	return $dtpHourly;
}

?>