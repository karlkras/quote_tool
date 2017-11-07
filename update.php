<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>

<?PHP
/*
//definitions for testing DB on Konic
define (DBServerName, "192.168.1.206");
define (UserName, "root");
define (Password, "Db2gk2Vxe");
define (DBName, "clientpricing");
*/

//definitions for production DB on clom
//define (DBServerName, "192.168.1.52");
//define (UserName, "root");
//define (Password, "0RHtP8oaXnBb");
//define (DBName, "clientpricing");

define (DBServerName, "localhost");
define (UserName, "root");
define (Password, "");
define (DBName, "clientpricing");

	print(DBServerName . "<br>");

	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	
	$query = "update Sourcelang set Language='English (US)' where Id='0'";
	$result = $myDBConn->query($query) or die($myDBConn->error);
	print($result . "<br>");
	
	$query = "update Sourcelang set Language='English (UK)' where Id='1'";
	$result = $myDBConn->query($query) or die($myDBConn->error);
	print($result . "<br>");
	
	
	$myDBConn->close();

?>

</body>
</html>
