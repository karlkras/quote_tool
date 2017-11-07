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
<p><a href="index.php">Index</a> &gt; Internal Efforts</p><br />

<?PHP
$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
	exit;
}

$query = "select * from internalefforts order by Name";
$result = $myDBConn->query($query) or die($myDBConn->error);

print("<table border='1' id='table1'>\n");
print ("<tr><th>Name</th><th>Cost</th><th>&nbsp;</th></tr>\n");

$rowCount = 0;

while ($res =  $result->fetch_assoc())
{
	$id = $res['ID'];
	$name = $res['Name'];
	$rate = $res['HourlyRate'];
	$rate = $rate/100;

	echo "<tr><td><span id=\"name-".$id ."\">". $name . "</span><a href=\"#\" onClick=\"return editIEName('";
	echo $id ."');\"> <img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n";
	
	echo "<td><span id=\"rate-".$id ."\">$". number_format($rate,2) ."</span><a href=\"#\" onClick=\"return editIERate('";
	echo $id ."');\"> <img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n";
			
	echo "	<td><a href=\"#\" onClick=\"return deleteInternalEffort(this.parentNode.parentNode, '";
	echo $id ."', '". $name ."');\"><img src=\"images/delete-icon.png\" alt=\"Delete\" border=0 title=\"Delete\"/></a></td>\n</tr>\n\n";
	
	$rowCount++;

}
$result->free();

print ("<tr><td colspan=8 align=center><a href=\"#\" onClick=\"return popup('popUpDiv');\" ><img src=\"images/add-icon.png\" border=0 alt=\"Add New\" title=\"Add New\"></a></td></tr>");
print("</table>");
?>

<div id="blanket" style="display:none;"></div>
<div id="popUpDiv" style="display:none;">
<form action="process.php" method="post" name="addrow" >
<table width="480px" border="1" style="margin:10px">
<tr>
	<td align="right" colspan="2"><a href="#" onclick="return popup('popUpDiv');">Close</a></td>
</tr>
<tr>
	<td align="right">Task Name</td>
	<td><input type="text" name="taskName" id="taskName" />
		</td>
	</tr>
	<tr><td align="right">Hourly Rate</td>
	<td><input type="text" name="hourlyRate" id="hourlyRate" />
		</td>
	</tr>
	
	<tr>
		<td colspan="2" align="center"><a href="#" onclick=" addInternalEffort(); return popup('popUpDiv');">Add Internal Effort</a></td>
	</tr>
</table>
</form>
</div>


</body>
</html>
