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
<p><a href="index.php">Index</a> &gt; Linguistic Cost</p><br />
<?PHP
$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno)
{
	echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
	exit;
}

$query = "select * from linguisticcost order by srcLang, targetLang";
$result = $myDBConn->query($query) or die($myDBConn->error);

print("<table border='1' id='table1'>\n");
print ("<tr><th>Source<br>Language</th><th>Target<br>Language</th><th>New Text<br>Rate</th><th>Fuzzy Text<br>Rate</th>");
print ("<th>Match Text<br>Rate</th><th>Translation<br>(Hourly)</th><th>Proofread<br>(Hourly)</th><th>&nbsp;</th></tr>\n");

$rowCount = 0;

while ($res =  $result->fetch_assoc())
{
	print ("<tr>\n");
	
	$srcLangName = "Null";
	$srcLangID = $res['srcLang'];
	$nameQuery = "select Language from sourcelang where ID = '$srcLangID'";
	$nameResult = $myDBConn->query($nameQuery) or die($myDBConn->error);
	$nameRes = $nameResult->fetch_assoc();
	$srcLangName = $nameRes['Language'];
	print ("<td>$srcLangName</td>");
	$nameResult->free();
	
	$tgtLangName = "Null";
	$tgtLangID = $res['targetLang'];
	$nameQuery = "select Language from targetlang where ID = '$tgtLangID'";
	$nameResult = $myDBConn->query($nameQuery) or die($myDBConn->error);
	$nameRes = $nameResult->fetch_assoc();
	$tgtLangName = $nameRes['Language'];
	print ("<td>$tgtLangName</td>");
	$nameResult->free();
	
	print ("	<td><span id=\"ntr-".$srcLangID."-".$tgtLangID. "\">". $res['newTextRate'] / 1000 . "</span> <a href=\"#\" onClick=\"return editDB('newtext', '".$srcLangID."', '".$tgtLangID."');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n");
	print ("	<td><span id=\"fuz-".$srcLangID."-".$tgtLangID. "\">" . $res['fuzzyTextRate'] / 1000 ."</span> <a href=\"#\" onClick=\"return editDB('fuzzy', '". $srcLangID."', '".$tgtLangID."');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n");
	print ("	<td><span id=\"mat-".$srcLangID."-".$tgtLangID. "\">". $res['matchTextRate'] / 1000 . "</span> <a href=\"#\" onClick=\"return editDB('match', '". $srcLangID."', '".$tgtLangID."');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n");
	print ("	<td><span id=\"trans-".$srcLangID."-".$tgtLangID. "\">". $res['transHourly'] / 1000 . "</span> <a href=\"#\" onClick=\"return editDB('trans', '". $srcLangID."', '".$tgtLangID."');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n");
	print ("	<td><span id=\"proof-".$srcLangID."-".$tgtLangID. "\">". $res['prHourly'] / 1000 ."</span> <a href=\"#\" onClick=\"return editDB('proof', '". $srcLangID."', '".$tgtLangID."');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>\n");
	
	$delRow = $rowCount+1;
	print("	<td><a href=\"#\" onClick=\"return deleteDB('". $delRow ."', '". $srcLangID."', '".$tgtLangID."', '". $srcLangName ."', '". $tgtLangName ."');\"><img src=\"images/delete-icon.png\" alt=\"Delete\" border=0 title=\"Delete\"/></a></td>\n</tr>\n\n");
	
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
	<td align="right">Source Language</td>
	<td><?PHP
		
		$query = "select * from sourcelang order by Language";
		$result = $myDBConn->query($query) or die($myDBConn->error);
		
		print("<select name=\"sourceL\" id=\"sourceL\">\n");
		
		while ($res =  $result->fetch_assoc())
		{
			$id = $res['ID'];
			$name = $res['Language'];
		
			echo "\t<option value=\"".$name."\"";
			if ($name == "English (US)")
				echo " selected ";
			echo ">".$name."</option>\n";
					
		}
		
		
		print("</select>");
		
		$result->free();
	?></td>
	</tr>
	<tr>
		<td align="right">Target Language</td>
		<td><?PHP
		$query = "select * from targetlang order by Language";
		$result = $myDBConn->query($query) or die($myDBConn->error);
		
		print("<select name=\"targetL\" id=\"targetL\">\n");
		
		while ($res = $result->fetch_assoc())
		{
			$id = $res['ID'];
			$name = $res['Language'];
		
			echo "\t<option value=\"".$name."\">".$name."</option>\n";		
		}
		$result->free();
		print("</select>");
		
		$myDBConn->close();
	?></td>
	</tr>
	<tr>
		<td align="right">New Text Rate</td>
		<td><input type="text" name="newtext" id="newtext" /></td>
	</tr>
	<tr>
		<td align="right">Fuzzy Text Rate</td>
		<td><input type="text" name="fuzzytext" id="fuzzytext" /></td>
	</tr>
	<tr>
		<td align="right">Reps/100% Match Rate</td>
		<td><input type="text" name="matchtext" id="matchtext" /></td>
	</tr>
	<tr>
		<td align="right">Hourly Translation Rate</td>
		<td><input type="text" name="transHourly" id="transHourly" /></td>
	</tr>
	<tr>
		<td align="right">Hourly Proofreading Rate</td>
		<td><input type="text" name="prHourly" id="prHourly" /></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><a href="#" onclick=" addRow(<?PHP print($rowCount); ?>); return popup('popUpDiv');">Add Language Pair</a></td>
	</tr>
</table>
</form>
</div>


</body>
</html>
