<?PHP
session_start();
require_once('../function.fatal_handler.php');
require_once(__DIR__ . '/uuid.php');
require_once(__DIR__ . '/functions/f_processLogin.php');

$msg = "<p>You are trying to access one of the LLTS custom apps from "
                . "outside the Dashboard; this is not permitted.</p><p>Please log "
                . "into the dashboard at <a "
                . "href=\"http://pm.llts.com:9090/LingoApps/faces/menu/menulogon.jsp\">"
                . "http://pm.llts.com:9090/LingoApps/faces/menu/menulogon.jsp</a>"
                . " and access the custom app you want from there.</p>";

//if these two are set, assume we're trying to start a fresh session so clear the
//session variables out.
if (isset($_GET['authcode']) && isset($_GET['username'])){
    $_SESSION = array();
    /* if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();
    session_start(); */
}

if (!isset($_SESSION['userID'])){
    if (!isset($_GET['authcode']) || !isset($_GET['username'])){
        die($msg);
    } else {
        $AuthCode = $_GET['authcode'];
        $WF_Username = $_GET['username'];
    }
    processAdminLogin($AuthCode,$WF_Username,$app_UUID);	
} elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID)){
	die($msg);
}

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Client Specific Information Administration | Lingo Systems</title>
<link href="admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<h1>Client Specific Information Administration Tool</h1>
<h3 style="text-align:center"> &mdash; OnDemand Version &mdash; </h3>
<h5 style="text-align:center">Welcome <?PHP echo $_SESSION['userFirstName']; ?></h5>



<table align="center" width="80%" class="admin">
	<thead>
		<tr>
			<th class="admin">Custom Sell Pricing</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><ul class="admin"><li><a href="customPricing.php">View <?PHP 
			
				if ((isset($_SESSION['isAdmin'])) && ($_SESSION['isAdmin']))
				{
					echo '/ Edit / Create';
				}
			?></a></li></ul></td>
		</tr><?PHP
		
			if ((isset($_SESSION['isAdmin'])) && ($_SESSION['isAdmin']))
			{
				echo "\t\t<tr>\n";
				echo "\t\t\t<td><ul class=\"admin\"><li><a href=\"backupAll.php\">Bulk Export / Backup</a></li>\n";
				echo "\t\t\t</ul></td>\n";
				echo "\t\t</tr>\n";
			}
	?></tbody>
	<tfoot>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</tfoot>
</table>
<br />
<table align="center" width="80%" class="admin">
	<thead>
		<tr>
			<th class="admin">Agreed Terms &amp; Conditions</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><ul class="admin"><li>T.B.D.</li></ul></td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</tfoot>
</table>

<div class="logo">
	<img src="../images/languageline-logo.png" alt="Lingo Systems" width="223" height="62" class="logoimg"/><br />
	<span class="small">&copy; 2010-2012 Language Line Translation Solutions, All Rights Reserved. Company Confidential &ndash; Do Not Distribute</span>
</div>


</body>
</html>
