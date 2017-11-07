<?PHP
require_once('../attaskconn/LingoAtTaskService.php');
include_once("../definitions.php");
session_start();

//first things first, check for admin, if not send them to the 'view' portal
if ((!isset($_SESSION['isAdmin'])) || (!$_SESSION['isAdmin']))
{
	header('location:./customPricing.php');
	exit;
}

if (isset($_POST['submit']) && ($_POST['submit'] == 'Edit'))
{
	$clientName = $_POST['client'];
	$tableName = "client_" . str_replace(" ", "_", $clientName);
	
	$taskName = str_replace("%2B", "+", $_POST['name']);
	$orig_taskName = str_replace(" ", "_", $taskName) . "#".str_replace(" ", "_",$_POST['orgSub'])."#";
	$orig_taskName .= "=" . str_replace(" ", "_", $_POST['orgSrc']) . "=" . str_replace(" ", "_", $_POST['orgTgt']);
			
	$subCategory = $_POST['subcat'];
	if ($subCategory != 'none')
	{
		$taskName .= "#".$subCategory."#";
	}
	$taskName .= "=" . str_replace(" ", "_", $_POST['sourcelang']) . "=" . str_replace(" ", "_", $_POST['targetlang']);
	
	$taskRate = $_POST['rate'];
	$_taskRate = $taskRate * 1000;
	
	$taskUnit = $_POST['units'];
	
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	
	$query = "UPDATE " . $tableName . " SET task_name='".$taskName;
	$query .= "', rate='". $_taskRate . "', units='" . $taskUnit . "' WHERE ";
	$query .= "task_name='" . $orig_taskName ."'";
	
	$myDBConn->query($query) or die($myDBConn->error);
	$myDBConn->close();
	
	$url = "customPricing.php?action=edit&target=" . $_POST['client'];
	header('location: ' . $url);
	exit(1);

}
	

try{
	$api = new LingoAtTaskService();
	
	$g = new getLanguageService;	
	$result = $api->getLanguageService($g);
	$sourceLangs = $result->return->sourceLanguages;
	sort($sourceLangs, SORT_STRING);
	
	$targetLangs = $result->return->targetLanguages;
	sort($targetLangs, SORT_STRING);
}
catch(Exception $e)
{
	echo "Error retrieving the language lists from @task<br>\n";
	exit;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Edit Language-based Task</title>
<link href="admin.css" rel="stylesheet" type="text/css" />

<script language="javascript">
function checkSub(typeValue)
{
	if (typeValue == 'word')
	{
		document.getElementById('subcat').disabled = false;
	}
	else
	{
		document.getElementById('subcat').disabled = true;
	}
		
}
</script>


</head>

<body>
<?PHP
$taskName = str_replace("%2B", "+", $_GET['n']);
$taskName = str_replace("_", " ", $taskName);


$srcLang = str_replace("_", " ", urldecode($_GET['src']));
$tgtLang = str_replace("_", " ", urldecode($_GET['tgt']));
?>

<h1>Editing <?PHP echo $_GET['c']; ?>, <?PHP echo $taskName; ?></h1>

<form action="#" method="post" name="editnonlanguage_form" >
<input type="hidden" name="client" value="<?PHP echo $_GET['c']; ?>" />
<input type="hidden" name="name" value="<?PHP echo $_GET['n']; ?>" />
<input type="hidden" name="orgSub" value="<?PHP echo $_GET['s']; ?>" />
<input type="hidden" name="orgSrc" value="<?PHP echo $_GET['src']; ?>" />
<input type="hidden" name="orgTgt" value="<?PHP echo $_GET['tgt']; ?>" />
<table border="1" class="admin">
	<tr>
		<th colspan="2" align="left" class="admin"><?PHP echo $taskName; ?></th>
	</tr>
	<tr>
		<td align="right" class="admin">Source Language:</td>
		<td align="left"><select name="sourcelang" id="sourcelang" /><?PHP
		foreach ($sourceLangs as $lang)
		{
			echo "		<option value=\"",$lang, "\"";
			if ($lang == $srcLang)
				echo " selected=\"selected\" ";
			echo ">", $lang, "</option>\n";
		}
		?></select></td>
	</tr>
	<tr>
		<td align="right" class="admin">Target Language:</td>
		<td><select name="targetlang" id="targetlang" /><?PHP
		foreach ($targetLangs as $lang)
		{
			echo "		<option value=\"",$lang, "\"";
			if ($lang == $tgtLang)
				echo " selected=\"selected\" ";
			echo ">",$lang,"</option>\n";
		}
		?></select></td>
	<tr>
		<td align="right" class="admin">Rate:</td>
		<td align="left"><input type="text" name="rate" id="rate" value="<?PHP echo number_format($_GET['r'],3); ?>" /></td>
	</tr>
	<tr>
		<td align="right" class="admin">Units:</td>
		<td align="left"><select name="units" id="units" onchange="checkSub(this.value)">
				<option value="word" <?PHP if(strtolower($_GET['u']) == 'word') echo 'selected=\"selected\"'; ?>>Word</option>
				<option value="hour" <?PHP if(strtolower($_GET['u']) == 'hour') echo 'selected=\"selected\"'; ?>>Hour</option>
				<option value="percent" <?PHP if(strtolower($_GET['u']) == 'percent') echo 'selected=\"selected\"'; ?>>Percent</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="admin">Sub-category:</td>
		<td><select name="subcat" id="subcat" <?PHP	if ($_GET['u'] != 'word') echo "disabled=\"disabled\""; ?> >
				<option value="none">-----</option>
				<option value="New_Text"<?PHP if ($_GET['s'] == 'New_Text') echo 'selected=\"selected\"'; ?>>New Text</option>
				<option value="Fuzzy_Text"<?PHP if ($_GET['s'] == 'Fuzzy_Text') echo 'selected=\"selected\"'; ?>>Fuzzy Text</option>
				<option value="Match_Text"<?PHP if ($_GET['s'] == 'Match_Text') echo 'selected=\"selected\"'; ?>>100% Match/Repetitions</option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" name='submit' value="Edit" /></td>
	</tr>
</table>
</form>

<a href="customPricing.php?action=edit&target=<?PHP echo $_GET['c']; ?>" class="breadcrumbs">Cancel</a>

</body>
</html>
