<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
<?PHP 
define ('DBServerName', 'konic.llts.com');
define ('UserName', 'root');
define ('Password', 'Db2gk2Vxe');
define ('DBName', 'clientpricing');


$mysqli = new mysqli(DBServerName, UserName, Password, DBName);

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
echo $mysqli->host_info . "<br><br>";

$query = "SELECT * FROM minimumtypes";

if (!$result = $mysqli->query($query)) {

    die("There was an error querying the database<br>");

}

while ($row = $result->fetch_assoc())
{
	echo "id ".$row[id]." = '".$row['name']."'<br>";
}

$result->close();


$mysqli->close();
 ?>
</body>
</html>
