<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Save Files</title>

<script language="javascript">

function SaveBoth()
{
	window.open('s2.php',"savetext");
	window.close('savetext');
	window.open('saveXML.php',"savexml");
	window.close('savexml');
	//window.location = 'saveXML.php';
	

}

</script>
</head>

<body onload="SaveBoth();">
<p><a href="estimate.php">Return to start</a></p>
</body>
</html>
