<?PHP
require_once("definitions.php");

function CheckCustomRushRate($id)
{
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}

	$query = "SELECT table_name FROM clients WHERE attask_id = $id";
	$result = $myDBConn->query($query) or die($myDBConn->error);
	

	if($result->num_rows == 0) {
		$custom = 'no';
	}
	else {
		$res =  $result->fetch_assoc();
		$table = $res['table_name'];
		
		$query = "SHOW COLUMNS FROM $table";
		$result = $myDBConn->query($query) or die($myDBConn->error);

		$columns = array();
		while ($res = $result->fetch_assoc()) {
			$columns[] = $res['Field'];
		}
		if (in_array("rush_rate", $columns)) {
			$custom = 'yes';
		} else {
			$custom = 'no';
		}
	}

	$result->free();
	$myDBConn->close();
	return $custom;
}

function getClientID($clientName)
{
	$ID = 0;
	
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	
	//clean up single-quotes for the query
	$clean_name = str_replace("'", "\\'", $clientName);
	$query = "select ID from clients where Name = '$clean_name'";
	$result = $myDBConn->query($query) or die($myDBConn->error);
	
	if ($result->num_rows >= 1)
	{
		$res =  $result->fetch_assoc();
		$ID = $res['ID'];
	}
	
	$result->free();
	$myDBConn->close();
	return $ID;
}

function getSourceLangID($sourceLang)
{
	$ID = 0;
	
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	
	$query = "select ID from sourcelang where Language = '$sourceLang'";
	$result = $myDBConn->query($query) or die($myDBConn->error);
	
	
	if ($result->num_rows > 0)
	{
		$res =  $result->fetch_assoc();
		$ID = $res['ID'];
	}
	
	$result->free();
	$myDBConn->close();
	return $ID;
}

function getTargetLangID($sourceLang)
{
	$ID = 0;
	
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	
	$query = "select ID from targetlang where Language = '$sourceLang'";
	$result = $myDBConn->query($query) or die($myDBConn->error);
	
	if ($result->num_rows > 0)
	{
		$res =  $result->fetch_assoc();	
		$ID = $res['ID'];
	}
	
	$result->free();
	$myDBConn->close();
	return $ID;
}

function checkCustom($SrcLangID, $TgtLangID, $ClientID, $PriceID)
{
	$customPrice = 0;

	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	
	switch ($PriceID)
	{
		case "NEWTEXT":
			$colName = "newTextRate";
			$tblName = "pricinglinguistic";
			break;
		case FUZZY:
			$colName = "fuzzyTextRate";
			$tblName = "pricinglinguistic";
			break;
		case MATCHES:
			$colName = "matchTextRate";
			$tblName = "pricinglinguistic";
			break;
		case FORMAT:
		case GRAPHICS:
			$colName = "dtp";
			$tblName = "pricinginternal";
			break;
		case TMWORK:
		case ENGINEERING:
		case SCAPS:
			$colName = "engineering";
			$tblName = "pricinginternal";
			break;
		case QA:
			$colName = "qa";
			$tblName = "pricinginternal";
			break;
		case PM:
			$colName = "pmpercent";
			$tblName = "pricinginternal";
			break;
		case PROOF:
			$colName = "PRhourly";
			$tblName = "pricinglinguistic";
			break;
		case "transHourly":
			$colName = "transHourly";
			$tblName = "pricinglinguistic";
			break;
		
	}
	
	if ($tblName == "pricinglinguistic")
	{
		$query = "select $colName from $tblName where ClientID = '$ClientID' AND SrcLang = '$SrcLangID' AND TgtLang = '$TgtLangID'";
	}
	else
	{
		$query = "select $colName from $tblName where ClientID = '$ClientID'";
	}

	$result = $myDBConn->query($query) or die($myDBConn->error);
		
	if ($result->num_rows > 0)
	{
		$res =  $result->fetch_assoc();
		$customPrice = $res[$colName];
	}
	
	$result->free();
	$myDBConn->close();
	
	return $customPrice;
}


?>
