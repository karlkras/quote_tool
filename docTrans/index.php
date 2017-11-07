<?PHP
session_start();
require_once('../uuid.php');

//check to see if we're logged in
if (!isset($_SESSION['userID'])) {
    header('location:../login.php?location=standard');
    exit;
} elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID)) {
    header('location:../login.php?err=6&location=standard');
    exit;
}

//check to see if there's already session data, and if so, clean it out,
//but save any user information
if (isset($_SESSION['taskService'])) {
    $tempUID = $_SESSION['userID'];
    $tempF_Name = $_SESSION['userFirstName'];
    $tempL_Name = $_SESSION['userLastName'];
    $tempRoles = $_SESSION['userRoles'];

    $_SESSION = array();

    //start a new session
    session_start();

    $_SESSION['userID'] = $tempUID;
    $_SESSION['userFirstName'] = $tempF_Name;
    $_SESSION['userLastName'] = $tempL_Name;
    $_SESSION['userRoles'] = $tempRoles;
}

$_SESSION['bundleInternal'] = true;


ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 1);
include_once('../definitions.php');

require_once("../attaskconn/LingoAtTaskService.php");
require_once('function.sortProjects.php');

try {
    set_time_limit(60);
    $options["trace"] = 1;

    $api = new LingoAtTaskService();

    $g = new getQuotableProjects();
    //$quotableProjects = new projectStub;

    $quotableProjects = $api->getQuotableProjects($g)->return;

    $_SESSION['quotableProjects'] = serialize($quotableProjects);
} catch (exception $e) {

    echo "<br><strong>$e->faultstring</strong><br>";

    echo "<hr>Debug Data:<br>";
    echo "Error:<pre>\n";
    //var_dump($e);
    echo"\nresponse:\n";
    echo $api->__getLastResponse();

    echo "</pre>";
    exit;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script>
        <script type="text/javascript" src="../libs/indexpagehelper.js"></script>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Project Selection - LLS Quote Tool</title>
        <link href="../styles/main.css" rel="stylesheet" type="text/css" />
        <script src="../libs/common.js"></script>
        <script src="ajax/pricingschemes.js" language="javascript"></script>
    </head>

    <body onload="setTimeout('changeScheme();', 1250);">
        <h1>&nbsp; <img src="../images/languageline-logo.png" width="223" height="62" style="padding:2px"/> &nbsp;</h1>
        <hr />
        <h2 align="center">Standard Quote Generator</h2>
        <h3 align="center"> &mdash; OnDemand Version &mdash; </h3>
        <h5 align="center">Welcome <?PHP echo $_SESSION['userFirstName'] . " " . $_SESSION['userLastName']; ?> (<a href="../logout.php">logout</a>)</h5>

        <?PHP
        if (isset($_GET['error'])) {
            echo "<p>There was a problem with your submission, please try again.</p>";
        }
        ?>

        <form name="project" action="getproject.php" method="post">
            <fieldset>
                <legend>Project Selection</legend>
                <table border="1" bgcolor="#FFFFFF" width="100%">
                    <tr>
                        <td align="right" width="125px">
                            choose:  
                        </td>
                        <td align="left">
                            <?PHP
                            if (count($quotableProjects) > 1) {
                                echo "\n\n<select class='project' name=\"project\" id=\"project\" onChange=\"changeScheme();\">\n";
                                $sortedProjects = sortProjects($quotableProjects);
                                foreach ($sortedProjects as $qp) {
                                    //if ($qp->type == 'Localization')
                                    echo "\t<option value=\"", $qp->id, "\">", $qp->name, "</option>\n";
                                }
                                echo "</select>\n\n";
                            } elseif (count($quotableProjects) == 1) {

                                echo "\n\n<select name=\"project\" id=\"project\">\n";
                                echo "\t<option value=\"", $quotableProjects->id, "\">", $quotableProjects->name, "</option>\n";
                                echo "</select>\n\n";
                            } else {
                                echo "<b>There are no projects marked \"Ready for Quote\". Please check the project settings in @task and refresh.</b>";
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <fieldset>
                <legend>Project Information</legend>
                <table border="1" bgcolor="#FFFFFF" width="100%">
                    <tr>
                        <td width="30%" align="right" valign="top">Use U.S.-based linguists?</td>
                        <td width="70%">
                            <input type="radio" value="no" name="us-linguists"/>No &nbsp;
                            <input type="radio" value="yes" name="us-linguists" />Yes &nbsp;
                            <input type="radio" value="attask" name="us-linguists" checked="checked" />Use value from @task
                        </td>
                    </tr>
                    <tr>
                        <td align="right" valign="top">Pass on Trados leveraging to the client?</td>
                        <td>
                            <input type="radio" value="no" name="pass-leveraging"/>No &nbsp;
                            <input type="radio" value="yes" name="pass-leveraging" />Yes &nbsp;
                            <input type="radio" value="attask" name="pass-leveraging" checked="checked" />Use value from @task
                        </td>
                    </tr>
                    <tr>
                        <td align="right" valign="top">Apply a rush charge?</td>
                        <td>
                            <div id="loading"></div>
                            <label><input type="radio" name="rushFees" value="rf0"  checked="checked" />No &nbsp;</label>
                            <label class="showHide"><input type="radio" name="rushFees" value="yep" />Yes &nbsp;</label>
                            <label class="custHideShow"><input type="radio" id='1' name="rushFees" value="custom25" />custom 25% &nbsp;</label>
                            <label class="custHideShow"><input type="radio" name="rushFees" value="custom50" />custom 50% </label>
                            <label class="hideShow"><input type="radio" id='2' name="rushFees" value="rf25" />25% &nbsp;</label>
                            <label class="hideShow"><input type="radio" name="rushFees" value="rf50" />50% </label>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" valign="top">Apply &quot;Holiday&quot; costs?</td>
                        <td>
                            <input type="radio" name="holiday" value="no" checked="checked"/>No &nbsp;
                            <input type="radio" name="holiday" value="yes"/>Yes
                        </td>
                    </tr>
                    <tr>
                        <td align="right" valign="top">Charge Client Proofreading? (for LLS and Margin Pricing only)</td>
                        <td>
                            <!-- Question used to be "pass on proofreading", this is why yes/no is swapped -->
                            <input type="radio" name="proofReading" value="no" id="radio1"/>Yes &nbsp;
                            <input type="radio" name="proofReading" value="yes" />No
                        </td>
                    </tr>
                </table>
            </fieldset>
            <fieldset>
                <table border="1" bgcolor="#FFFFFF" width="50%" align="center">
                    <tr>
                        <td align="center">
                            <div id="specialInst" style="display:none">Nothing to see here</div>
                            <div id="submitBtn"><?PHP
                                if (count($quotableProjects) > 0) {
                                    echo "<input type=\"submit\" name=\"submit\" value=\"Proceed\" disabled=\"disabled\" id=\"submit\"/>";
                                }
                                ?></div>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
        <div style="text-align: center;">
            <a href="../index.php"><button >Return to Main Page</button></a>
        </div>
    </body>
</html>
