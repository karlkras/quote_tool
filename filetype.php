<?PHP

include_once("definitions.php");

$filetype = $_GET['filetype'];


//get filetype data from the database
$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
	exit;
}

$query = "select pagerate from formatting where filetype = '$filetype'";
$result = $myDBConn->query($query) or die($myDBConn->error);


if ($result >= 1)
{
	$res =  $result->fetch_assoc();
	$pagerate = $res['pagerate'];
}
else
{
	$pagerate = 0;
}
	
$result->free();
$myDBConn->close();

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><filetype>';
echo "<pagerate>". $pagerate ."</pagerate>";
echo "</update>";



?>
