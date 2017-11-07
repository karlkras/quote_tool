<?PHP
include_once("..\definitions.php");
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
	
	$taskName = $_POST['name'];
	$orig_taskName = str_replace(" ", "_", $taskName);
			
	if (isset($_POST['subcat']))
		$subCategory = $_POST['subcat'];
	else
		$subCategory = 'none';
		
	if ($subCategory != 'none')
	{
		$taskName .= "#".$subCategory."#";
		$orig_taskName .= "#".str_replace(" ", "_",$_POST['orgSub'])."#";
	}
	
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
		
	$result = $myDBConn->query($query) or die($myDBConn->error);
	$myDBConn->close();
	
	$url = "customPricing.php?action=edit&target=" . $_GET['client'];
	header('location: ' . $url);
	exit(1);

}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Edit Non-Language Task</title>
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

<h1>Editing <?PHP echo $_GET['client']; ?>, <?PHP echo str_replace("_", " ",$_GET['name']); ?></h1>

<form action="#" method="post" name="editnonlanguage_form" >
<input type="hidden" name="client" value="<?PHP echo $_GET['client']; ?>" />
<input type="hidden" name="name" value="<?PHP echo $_GET['name']; ?>" />
<input type="hidden" name="orgSub" value="<?PHP echo $_GET['sub']; ?>" />
<table border="1" class="admin">
	<tr>
		<th colspan="2" align="left" class="admin"><?PHP echo str_replace("_", " ",$_GET['name']); ?></th>
	</tr>
	
	<tr>
		<td align="right" class="admin">Rate:</td>
		<td align="left"><input type="text" name="rate" id="rate" value="<?PHP echo number_format($_GET['rate'],3); ?>" /></td>
	</tr>
	<tr>
		<td align="right" class="admin">Units:</td>
		<td align="left"><select name="units" id="units" onchange="checkSub(this.value)">
				<option value="word" <?PHP if(strtolower($_GET['unit']) == 'word') echo 'selected=\"selected\"'; ?>>Word</option>
				<option value="hour" <?PHP if(strtolower($_GET['unit']) == 'hour') echo 'selected=\"selected\"'; ?>>Hour</option>
				<option value="percent" <?PHP if(strtolower($_GET['unit']) == 'percent') echo 'selected=\"selected\"'; ?>>Percent</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="admin">Sub-category:</td>
		<td><select name="subcat" id="subcat" <?PHP	if ($_GET['unit'] != 'word') echo "disabled=\"disabled\""; ?> >
				<option value="none">-----</option>
				<option value="New_Text"<?PHP if ($_GET['sub'] == 'New_Text') echo 'selected=\"selected\"'; ?>>New Text</option>
				<option value="Fuzzy_Text"<?PHP if ($_GET['sub'] == 'Fuzzy_Text') echo 'selected=\"selected\"'; ?>>Fuzzy Text</option>
				<option value="Match_Text"<?PHP if ($_GET['sub'] == 'Match_Text') echo 'selected=\"selected\"'; ?>>100% Match/Repetitions</option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" name='submit' value="Edit" /></td>
	</tr>
</table>
</form>

<a href="customPricing.php?action=edit&target=<?PHP echo $_GET['client']; ?>" class="breadcrumbs">Cancel</a>

</body>
</html>
