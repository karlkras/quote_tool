<?PHP
include_once("..\definitions.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Translation and Localization Services for the Multicultural Economy | Lingo Systems</title>


<script src="csspopup.js"></script>
<script src="editfunctions.js"></script>
<link href="main.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p><a href="index.php">Index</a> &gt; Source Languages</p><br />

<?PHP
$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
	exit;
}

$query = "select * from sourcelang order by Language";
$result = $myDBConn->query($query) or die($myDBConn->error);

print("<table border='1' id='table1'>\n");
print ("<tr><th>Name</th><th>&nbsp;</th><th>&nbsp;</th></tr>\n");

$rowCount = 0;

while ($res =  $result->fetch_assoc())
{
	$id = $res['ID'];
	$name = $res['Language'];

	echo "<tr><td><span id=\"name-".$id ."\">". $name . "</span></td><td><a href=\"#\" onClick=\"return editSource('". $id ."');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n";
			
	$delRow = $rowCount+1;
	print("	<td><a href=\"#\" onClick=\"return deleteSource( this.parentNode.parentNode, '". $id ."');\"><img src=\"images/delete-icon.png\" alt=\"Delete\" border=0 title=\"Delete\"/></a></td>\n</tr>\n\n");
	
	$rowCount++;

}
$result->free();
print ("<tr><td colspan=8 align=center><a href=\"#\" onClick=\"return popup('popUpDiv');\" ><img src=\"images/add-icon.png\" border=0 alt=\"Add New\" title=\"Add New\"></a></td></tr>");
print("</table>");
	
$myDBConn->close();
?>

<div id="blanket" style="display:none;"></div>
<div id="popUpDiv" style="display:none;">
<form action="process.php" method="post" name="addrow" >
<table width="480px" border="1" style="margin:10px">
<tr>
	<td align="right" colspan="2"><a href="#" onclick="return popup('popUpDiv');">Close</a></td>
</tr>
<tr>
	<td align="right">Language Name</td>
	<td><input type="text" name="language" id="language" />
		</td>
	</tr>
	
	<tr>
		<td colspan="2" align="center"><a href="#" onclick=" addSource(); return popup('popUpDiv');">Add Language</a></td>
	</tr>
</table>
</form>
</div>


</body>
</html>
