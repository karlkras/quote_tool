<?PHP
include_once('functions/f_hasTask.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>testHasTask</title>
</head>

<body>
<p>
<?PHP
$clientHasTask = false;
$clientHasTask = hasTask('client_alberta_health_services', 'WebSite_Engineering');

//echo $clientHasTask ? 'true' : 'false';

?>
</p>
</body>
</html>