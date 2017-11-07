<?PHP

include_once("../../definitions.php");

$row = $_GET['row'];
$clientid = $_GET['clientid'];
$dtp = $_GET['dtp'];
$engineering = $_GET['engineering'];
$seniorengineering = $_GET['seniorengineering'];
$qa = $_GET['qa'];
$pmpercent = $_GET['pmpercent'];
$pmhourly = $_GET['pmhourly'];

$error = "FALSE";
$error_text = "none";

if (($dtp == "") || ($dtp == "{null}") || ($dtp == "null"))
	$dtp = "NULL";
else
	$dtp *= 1000;
	
if (($engineering == "") || ($engineering == "{null}") || ($engineering == "null"))
	$engineering = "NULL";
else
	$engineering *= 1000;
	
if (($seniorengineering == "") || ($seniorengineering == "{null}") || ($seniorengineering == "null"))
	$seniorengineering = "NULL";
else
	$seniorengineering *= 1000;
	
if (($qa == "") || ($qa == "{null}") || ($qa == "null"))
	$qa = "NULL";
else
	$qa *= 1000;
	
if (($pmpercent == "") || ($pmpercent == "{null}") || ($pmpercent == "null"))
	$pmpercent = "NULL";
else
	$pmpercent *= 1000;
	
if (($pmhourly == "") || ($pmhourly == "{null}") || ($pmhourly == "null"))
	$pmhourly = "NULL";
else
	$pmhourly *= 1000;
	
	
//get data from the database
$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	$error = "TRUE";
	$error_text = "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
}
else
{

	$query = "INSERT INTO pricinginternal (ClientID, dtp, engineering, seniorengineering, qa, pmpercent, pmhourly) VALUES ('". $clientid . "', '". $dtp ."', '". $engineering ."', ". $seniorengineering .", ". $qa .", ". $pmpercent .", ". $pmhourly .")";
	$result = $myDBConn->query($query);
	
	if (!$result)
	{	
		$error = "TRUE";
		$error_text = "Could not insert pricing info  into database.\n".$myDBConn->error;	
	}
	else
	{
		$error = "FALSE";
		$error_text = "none";
		
		//get client and language names
		$query = "SELECT Name FROM clients WHERE ID='".$clientid."'";
		$result = $myDBConn->query($query);
		$res = $result->fetch_assoc();
		$clientname = $res['Name'];
		$result->free();
		
	}
}
$myDBConn->close();


if ($dtp == "NULL")
	$dtp = "{null}";
else
	$dtp /= 1000;
	
if ($engineering == "NULL")
	$engineering = "{null}";
else
	$engineering /= 1000;	

if ($seniorengineering == "NULL")
	$seniorengineering = "{null}";
else
	$seniorengineering /= 1000;	
	
if ($qa == "NULL")
	$qa = "{null}";
else
	$qa /= 1000;
	
if ($pmpercent == "NULL")
	$pmpercent = "{null}";
else
	$pmpercent /= 1000;
	
if ($pmhourly == "NULL")
	$pmhourly = "{null}";
else
	$pmhourly /= 1000;

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><update>';
echo "<errorflag>". $error ."</errorflag>";
echo "<errortext>". $error_text ."</errortext>";
echo "<row>". $row ."</row>";
echo "<clientid>". $clientid ."</clientid>";
echo "<clientname>". $clientname ."</clientname>";
echo "<dtp>".$dtp."</dtp>";
echo "<engineering>".$engineering."</engineering>";
echo "<seniorengineering>".$seniorengineering."</seniorengineering>";
echo "<qa>".$qa."</qa>";
echo "<pmpercent>".$pmpercent."</pmpercent>";
echo "<pmhourly>".$pmhourly."</pmhourly>";
echo "</update>";




?>
