<?PHP

include_once("../../definitions.php");

$row = $_GET['row'];
$name = $_GET['name'];
$email = $_GET['email'];
$phone = $_GET['phone'];
$title = $_GET['title'];
$ID = -1;

$error = "FALSE";
$errortext = "";

//get data from the database
$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	$error = "TRUE";
	$errortext = "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
}
else
{

	$query = "INSERT INTO lingocontacts (name, email, phone, title) VALUES ('". $name . "', ";
	($email == "") ? $query .= "NULL, " : $query .= "'" . $email . "', ";
	($phone == "") ? $query .= "NULL, " : $query .= "'" . $phone . "', ";
	($title == "") ? $query .= "NULL)" : $query .= "'" . $title . "')";
	
	$result = $myDBConn->query($query);
	
	if (!$result)
	{	
		$error = "TRUE";
		$errortext = "Could not insert contact into database\n".$myDBConn->error;	
	}
	else
	{
		$error = "FALSE";
		$errortext = "none";
		
		//get the id to pass on
		$result->free();
		$query = "SELECT ID FROM lingocontacts WHERE name='". $name ."'";
		$result = $myDBConn->query($query);
		$res =  $result->fetch_assoc();
		$ID = $res['ID'];
	
	}
	
	$result->free();
}
$myDBConn->close();

if ($email == "") 
	$email= " " ;
if ($phone == "")
	$phone = " ";
if ($title == "")
	$title = " ";

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><update>';
echo "<errorflag>". $error ."</errorflag>";
echo "<errortext>". $errortext ."</errortext>";
echo "<row>". $row ."</row>";
echo "<name>". $name ."</name>";
echo "<email>". $email ."</email>";
echo "<phone>". $phone ."</phone>";
echo "<title>". $title ."</title>";
echo "<id>". $ID ."</id>";
echo "</update>";



?>