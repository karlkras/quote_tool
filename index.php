<?PHP
session_start();
require_once('uuid.php');
require_once('function.processLogin.php');
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
}


if (!isset($_SESSION['userID'])) {
    if (!isset($_GET['authcode']) || !isset($_GET['username'])){
        die($msg);
    } else {
        $AuthCode = $_GET['authcode'];
        $WF_Username = $_GET['username'];
    }
    processLogin($AuthCode,$WF_Username,$app_UUID);
} elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID)) {
    die($msg);
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Quote Generation Tool | Language Line Translation Solutions</title>
        <link href="styles/main.css" rel="stylesheet" type="text/css" />
        <link href="styles/new.css" rel="stylesheet" type="text/css" /> 
    </head>

    <body>
        <h1>&nbsp; <img src="images/languageline-logo.png" width="223" height="62" style="padding:2px"/> &nbsp;</h1>
        <hr />
        <h2 align="center">Language Line Solutions Quote Tool</h2>
        <h3 align="center"> &mdash; OnDemand Version &mdash; </h3>

        <table align="center" class="border">
            <tr>
                <td class="banner" style="width:400px"><?PHP echo "Welcome " . $_SESSION['userFirstName'] . " " . $_SESSION['userLastName'] . "!"; ?>&nbsp;</td>
            </tr>
            <tr><th align="left">Please select the service you require:</th></tr>
            <tr>
                <td>
                    <ul style="margin-top:2px">
                        <li style="padding:2px"><a href="estimate_1.php">Ballpark Quote</a></li>
                        <li style="padding:2px"><a href="quoteGen/index.php">Firm Quote</a></li>
                        <li style="padding:2px"><a href="pdfUpload/index.php">Upload Quote PDF to Workfront</a></li>
                        <li style="padding:2px"><a href="quoteGen/reload.php">Reload Quote XML into Quote Tool</a></li>
                    </ul>
                </td>
            </tr>
        </table>
    </body>
</html>
