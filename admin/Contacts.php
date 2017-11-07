<?PHP
include_once("..\definitions.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Translation and Localization Services for the Multicultural Economy | Lingo Systems</title>


<script src="csspopup.js"></script>
<script src="editfunctions.js"></script>
<link href="main.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p><a href="index.php">Index</a> &gt; Lingo Contacts</p><br />
<?PHP
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	
	$query = "select * from lingocontacts order by name";
	$result = $myDBConn->query($query) or die($myDBConn->error);
	
	print("<table border='1' id='table1'>\n");
	print ("<tr><th>Name</th><th>Title</th><th>Phone</th><th>E-mail</th><th>&nbsp;</th></tr>\n");
	
	$rowCount = 0;
	
	while ($res =  $result->fetch_assoc())
	{
		print ("<tr>\n");
		
		$id = $res['id'];
		$Name = $res['name'];
		$Title = $res['title'];
		$Phone = $res['phone'];
		$Email = $res['email'];

		print ("	<td><span id=\"name-". $id ."\">". $Name . "</span> <a href=\"#\" onClick=\"return editContact('name', '".$id."');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n");
		print ("	<td><span id=\"title-". $id ."\">" . $Title ."</span> <a href=\"#\" onClick=\"return editContact('title', '". $id ."');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n");
		print ("	<td><span id=\"phone-". $id . "\">". $Phone . "</span> <a href=\"#\" onClick=\"return editContact('phone', '". $id ."');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n");
		print ("	<td><span id=\"email-". $id . "\">". $Email . "</span> <a href=\"#\" onClick=\"return editContact('email', '". $id ."');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n");
				
		print("	<td><a href=\"#\" onClick=\"return deleteContact(this.parentNode.parentNode, '".$id."');\"><img src=\"images/delete-icon.png\" alt=\"Delete\" border=0 title=\"Delete\"/></a></td>\n</tr>\n\n");
		
		$rowCount++;

	}
	$result->free();
	$myDBConn->close();
	
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
	<td align="right">Name</td>
	<td><input type="text" name="name" id="name" /></td>
	</tr>
	<tr>
		<td align="right">Title</td>
		<td>
			<input type="text" name="title" id="title" />
		</td>
	</tr>
	<tr>
		<td align="right">Phone</td>
		<td><input type="text" name="phone" id="phone" /></td>
	</tr>
	<tr>
		<td align="right">E-Mail</td>
		<td><input type="text" name="email" id="email" /></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><a href="#" onclick=" addContact(); return popup('popUpDiv');">Add Contact</a></td>
	</tr>
</table>
</form>
</div>


</body>
</html>
