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
<p><a href="index.php">Index</a> &gt; Custom Internal Effort Sell Pricing</p><br />
<?PHP
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	
	$query = "select * from pricinginternal order by ClientID";
	$result = $myDBConn->query($query) or die($myDBConn->error);
	
	print("<table border='1' id='table1'>\n");
	print ("<tr><th>Client</th><th>DTP</th><th>Engineering</th><th>Senior<br>Engineering</th><th>QA</th><th>PM<br>Percent</th><th>PM<br>Hourly</th><th>&nbsp;</th></tr>\n");
	
	$rowCount = 0;
	while ($res =  $result->fetch_assoc())
	{
		print ("<tr>\n");
		
		$clientName = "Null";
		$clientID = $res['ClientID'];
		$nameQuery = "select Name from clients where ID = '$clientID'";
		$nameResult = $myDBConn->query($nameQuery) or die($myDBConn->error);
		$nameRes = $nameResult->fetch_assoc();
		$clientName = $nameRes['Name'];
		print ("<td>$clientName</td>");
		
		print ("	<td><span id=\"dtp-".$clientID."\">");
		if (!isset($res['dtp']))
			print "{null}";
		else
			print $res['dtp'] / 1000;
		print ("</span> <a href=\"#\" onClick=\"return editCustomInternal('dtp', '".$clientID."');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n");
		
		print ("	<td><span id=\"eng-".$clientID."\">"); 
		if (!isset($res['engineering']))
			print "{null}";
		else
			print $res['engineering'] / 1000;
		print ("</span> <a href=\"#\" onClick=\"return editCustomInternal('eng', '".$clientID."');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n");
		
		print ("	<td><span id=\"senEng-".$clientID."\">");
		if (!isset($res['seniorengineering']))
			print "{null}";
		else
			print $res['seniorengineering'] / 1000;
		print ( "</span> <a href=\"#\" onClick=\"return editCustomInternal('senEng', '".$clientID."');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n");
		
		print ("	<td><span id=\"qa-".$clientID."\">");
		if (!isset($res['qa']))
			print "{null}";
		else
			print $res['qa'] / 1000;
		print ("</span> <a href=\"#\" onClick=\"return editCustomInternal('qa', '".$clientID."');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n");
		
		print ("	<td><span id=\"percent-".$clientID."\">");
		if (!isset($res['pmpercent']))
			print "{null}";
		else
			print $res['pmpercent'];
		print  ("</span> <a href=\"#\" onClick=\"return editCustomInternal('percent', '".$clientID."');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n");
		
		print ("	<td><span id=\"hourly-".$clientID."\">");
		if (!isset($res['pmhourly']))
			print "{null}";
		else
			print $res['pmhourly'] / 1000;
		print  ("</span> <a href=\"#\" onClick=\"return editCustomInternal('hourly', '".$clientID."');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n");
		
		print("	<td><a href=\"#\" onClick=\"return deleteCustInt( this.parentNode.parentNode, '".$clientID."');\"><img src=\"images/delete-icon.png\" alt=\"Delete\" border=0 title=\"Delete\"/></a></td>\n</tr>\n\n");
		
		$rowCount++;
		$nameResult->free();

	}
	$result->free();
	
	print ("<tr><td colspan=9 align=center><a href=\"#\" onClick=\"return popup('popUpDiv');\" ><img src=\"images/add-icon.png\" border=0 alt=\"Add New\" title=\"Add New\"></a></td></tr>");
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
	<td align="right">Client</td>
	<td>
		<select name-"Client" id="Client"><?PHP
						
			$query = "select * from clients order by Name";
			$result = $myDBConn->query($query) or die($myDBConn->error);
			
			
			while ($res =  $result->fetch_assoc())
			{
				$id = $res['ID'];
				$name = $res['Name'];
				
				echo "<option value=\"", $id, "\">", $name, "</option>\n";
			}
			$result->free();
			$myDBConn->close();
			?>
		</select>
	</td>
</tr>
	<tr>
		<td align="right">DTP Hourly Rate</td>
		<td><input type="text" name="dtp" id="dtp" />for NO VALUE, enter null</td>
	</tr>
	<tr>
		<td align="right">Engineering Hourly Rate</td>
		<td><input type="text" name="engineering" id="engineering" />for NO VALUE, enter null</td>
	</tr>
	<tr>
		<td align="right">Senior Engineering<br />Hourly Rate</td>
		<td><input type="text" name="seniorengineering" id="seniorengineering" />for NO VALUE, enter null</td>
	</tr>
	<tr>
		<td align="right">QA Hourly Rate</td>
		<td><input type="text" name="qa" id="qa" />for NO VALUE, enter null</td>
	</tr>
	<tr>
		<td align="right">Project Management<br />Percent</td>
		<td><input type="text" name="pmpercent" id="pmpercent" />for NO VALUE, enter null</td>
	</tr>
	<tr>
		<td align="right">Project Management<br />Hourly Rate</td>
		<td><input type="text" name="pmhourly" id="pmhourly" />for NO VALUE, enter null</td>
	</tr>
	<tr>
		<td colspan="2" align="center"><a href="#" onclick=" addCustInt(); return popup('popUpDiv');">Add Custom Pricing</a></td>
	</tr>
</table>
</form>
</div>


</body>
</html>
