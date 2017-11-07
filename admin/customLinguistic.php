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
<p><a href="index.php">Index</a> &gt; Custom Linguistic Sell Pricing</p><br />
<?PHP
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	
	$query = "select * from pricinglinguistic order by ClientID, SrcLang, TgtLang";
	$result = $myDBConn->query($query) or die($myDBConn->error);
	
	print("<table border='1' id='table1'>\n");
	print ("<tr><td colspan=9 align=center><a href=\"#\" onClick=\"return popup('popUpDiv');\" ><img src=\"images/add-icon.png\" border=0 alt=\"Add New\" title=\"Add New\"></a></td></tr>");
	print ("<tr><th>Client</th><th>Source<br>Language</th><th>Target<br>Language</th><th>New Text<br>Rate</th><th>Fuzzy Text<br>Rate</th>");
	print ("<th>Match Text<br>Rate</th><th>Translation<br>(Hourly)</th><th>Proofread<br>(Hourly)</th><th>&nbsp;</th></tr>\n");
	
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
		$nameResult->free();
		
		$srcLangName = "Null";
		$srcLangID = $res['SrcLang'];
		$nameQuery = "select Language from sourcelang where ID = '$srcLangID'";
		$nameResult = $myDBConn->query($nameQuery) or die($myDBConn->error);
		$nameRes = $nameResult->fetch_assoc();
		$srcLangName = $nameRes['Language'];
		print ("<td>$srcLangName</td>");
		$nameResult->free();
		
		$tgtLangName = "Null";
		$tgtLangID = $res['TgtLang'];
		$nameQuery = "select Language from targetlang where ID = '$tgtLangID'";
		$nameResult = $myDBConn->query($nameQuery) or die($myDBConn->error);
		$nameRes = $nameResult->fetch_assoc();
		$tgtLangName = $nameRes['Language'];
		print ("<td>$tgtLangName</td>");
		$nameResult->free();
		
		print ("	<td><span id=\"ntr-".$clientID."-".$srcLangID."-".$tgtLangID. "\">");
		if (!isset($res['newTextRate']))
			print "{null}";
		else
			print $res['newTextRate'] / 1000;
		print ("</span> <a href=\"#\" onClick=\"return editCustomLinguistic('newtext', '".$clientID."', '".$srcLangID."', '".$tgtLangID."');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n");
		
		print ("	<td><span id=\"fuz-".$clientID."-".$srcLangID."-".$tgtLangID. "\">"); 
		if (!isset($res['fuzzyTextRate']))
			print "{null}";
		else
			print $res['fuzzyTextRate'] / 1000;
		print ("</span> <a href=\"#\" onClick=\"return editCustomLinguistic('fuzzy', '".$clientID."', '".$srcLangID."', '".$tgtLangID."');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n");
		
		print ("	<td><span id=\"mat-".$clientID."-".$srcLangID."-".$tgtLangID. "\">");
		if (!isset($res['matchTextRate']))
			print "{null}";
		else
			print $res['matchTextRate'] / 1000;
		print ( "</span> <a href=\"#\" onClick=\"return editCustomLinguistic('match', '".$clientID."', '".$srcLangID."', '".$tgtLangID."');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n");
		
		print ("	<td><span id=\"trans-".$clientID."-".$srcLangID."-".$tgtLangID. "\">");
		if (!isset($res['transHourly']))
			print "{null}";
		else
			print $res['transHourly'] / 1000;
		print ("</span> <a href=\"#\" onClick=\"return editCustomLinguistic('trans', '".$clientID."', '". $srcLangID."', '".$tgtLangID."');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n");
		
		print ("	<td><span id=\"proof-".$clientID."-".$srcLangID."-".$tgtLangID. "\">");
		if (!isset($res['PRhourly']))
			print "{null}";
		else
			print $res['PRhourly'] / 1000;
		print  ("</span> <a href=\"#\" onClick=\"return editCustomLinguistic('proof', '".$clientID."', '".$srcLangID."', '".$tgtLangID."');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n");
		
		$delRow = $rowCount+1;
		print("	<td><a href=\"#\" onClick=\"return deleteCustLing( this.parentNode.parentNode, '".$clientID."', '".$srcLangID."', '".$tgtLangID."');\"><img src=\"images/delete-icon.png\" alt=\"Delete\" border=0 title=\"Delete\"/></a></td>\n</tr>\n\n");
		
		$rowCount++;

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
			?>
		</select>
	</td>
</tr>
	
<tr>
	<td align="right">Source Language</td>
	<td>
		<select name="sourceL" id="sourceL"><?PHP
			$query = "select * from sourcelang order by Language";
			$result = $myDBConn->query($query) or die($myDBConn->error);
			
			
			while ($res =  $result->fetch_assoc())
			{
				$id = $res['ID'];
				$name = $res['Language'];
				
				echo "<option value=\"", $id, "\"";
				if ($name == "English (US)")
					echo " selected ";
				echo ">", $name, "</option>\n";
			}
			$result->free();
			?>
		</select>
	</td>
</tr>
<tr>
	<td align="right">Target Language</td>
	<td>
		<select name="targetL" id="targetL"><?PHP
			$query = "select * from targetlang order by Language";
			$result = $myDBConn->query($query) or die($myDBConn->error);
			
			
			while ($res =  $result->fetch_assoc())
			{
				$id = $res['ID'];
				$name = $res['Language'];
				
				echo "<option value=\"", $id, "\">", $name, "</option>\n";
			}
			$result->free();
			$myDBConn->close();
			?>
		</select>
	</td>
</tr>
	<tr>
		<td align="right">New Text Rate</td>
		<td><input type="text" name="newtext" id="newtext" />for NO VALUE, enter null</td>
	</tr>
	<tr>
		<td align="right">Fuzzy Text Rate</td>
		<td><input type="text" name="fuzzytext" id="fuzzytext" />for NO VALUE, enter null</td>
	</tr>
	<tr>
		<td align="right">Reps/100% Match Rate</td>
		<td><input type="text" name="matchtext" id="matchtext" />for NO VALUE, enter null</td>
	</tr>
	<tr>
		<td align="right">Hourly Translation Rate</td>
		<td><input type="text" name="transHourly" id="transHourly" />for NO VALUE, enter null</td>
	</tr>
	<tr>
		<td align="right">Hourly Proofreading Rate</td>
		<td><input type="text" name="prHourly" id="prHourly" />for NO VALUE, enter null</td>
	</tr>
	<tr>
		<td colspan="2" align="center"><a href="#" onclick=" addCustLing(); return popup('popUpDiv');">Add Custom Pricing</a></td>
	</tr>
</table>
</form>
</div>


</body>
</html>
