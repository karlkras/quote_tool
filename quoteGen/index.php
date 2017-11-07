<?PHP
session_start();
require_once('../uuid.php');


//check to see if we're logged in
if (!isset($_SESSION['userID'])) {
    die('You are not properly logged in and are unable to use this application');
} elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID)) {
    die('Fatal Error! Application authentication is not correct');
}

//clean out the session variable so we have
//a fresh start
//keep the user data from the session
$userID = $_SESSION['userID'];  
$userRoles = $_SESSION['userRoles'];
$userFirstName = $_SESSION['userFirstName'];
$userLastName = $_SESSION['userLastName'];
$app_UUID = $_SESSION['appUUID'];

//clear out the session variable
$_SESSION = array();

//reload the user data into the session
$_SESSION['userID'] = $userID;
$_SESSION['userRoles'] = $userRoles;
$_SESSION['userFirstName'] = $userFirstName;
$_SESSION['userLastName'] = $userLastName;
$_SESSION['appUUID'] = $app_UUID;
$_SESSION['rushFee'] = 0;
//clear the temp variables
unset($userID);
unset($userRoles);
unset($userFirstName);
unset($userLastName);
unset($app_UUID);

ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 1);

//since this is the index file, and we assume that we're
//here as the first page load, get the current working
//directory and load it to the session so that includes
//will work throughout the program
$_SESSION['cwd'] = getcwd();

include_once('../definitions.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js" ></script>
        <script type="text/javascript" src="../libs/indexpagehelper.js"></script>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Project Selection - LLS Quote Tool</title>
        <link href="../styles/main.css" rel="stylesheet" type="text/css" />
        <script src="../libs/common.js"></script>
        <script src="ajax/getpmpercent.js" language="javascript"></script>
        <script src="ajax/pricingschemes.js" language="javascript"></script>
        <script src="../libs/quoteGenIndex.js"></script>
        <link href="../styles/common.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <h1>&nbsp; <img src="../images/languageline-logo.png" width="223" height="62" style="padding:2px"/> &nbsp;</h1>
        <hr />
        <h2 align="center">Advanced Quote Generator</h2>
        <h3 align="center"> &mdash; On Demand Version 2 &mdash; </h3>
        <h5 align="center">Welcome <?PHP echo $_SESSION['userFirstName'] . " " . $_SESSION['userLastName']; ?></h5>

        <?PHP
        if (isset($_GET['error'])) {
            echo "<p>There was a problem with your submission, please try again.</p>";
        }
        ?>


        <form name="project" id="project_form" action="getproject.php" method="post">

            <fieldset><legend>Project Selection</legend>
                <table border="1" bgcolor="#FFFFFF" width="100%">
                    <tr>
<!--                        <td align="right">
                            choose:  
                        </td>-->
                        <td align="center" colspan=\"2\">
                            <?PHP
                            require_once("../attaskconn/LingoAtTaskService.php");
                            require_once("./functions/f_sortProjects.php");

                            try {
                                set_time_limit(60);
                                $options["trace"] = 1;
                                //$options["exceptions"] = 0;

                                $api = new LingoAtTaskService();

                                $g = new getQuotableProjects();

                                $quotableProjects = $api->getQuotableProjects($g)->return;


                                if (count($quotableProjects) > 1) {
                                    $_SESSION['quotableProjects'] = serialize($quotableProjects);
                                     echo "\n\n<select class='project' name=\"project\" id=\"project\" >\n";
                                    ?>
                                    <option class="selectIndent" value="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;------------&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Select&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;------------</option>
                                    <?PHP
                            
//                                    echo "\n\n<select class='project' name=\"project\" id=\"project\" >\n";
//                                    echo "\t<option class=\"selectIndent\" value=\"0\">-- Select --</option>\n";
                                    $sortedProjects = sortProjects($quotableProjects);
                                    foreach ($sortedProjects as $qp) {
                                        //if ($qp->type == 'Localization')
                                        echo "\t<option value=\"", $qp->id, "\">", $qp->name, "</option>\n";
                                    }
                                    echo "</select>\n\n";
                                } elseif (count($quotableProjects) == 1) {
                                    $_SESSION['quotableProjects'] = serialize($quotableProjects);

                                    echo "\n\n<select name=\"project\" id=\"project\">\n";
                                    echo "\t<option value=\"", $quotableProjects->id, "\">", $quotableProjects->name, "</option>\n";
                                    echo "</select>\n\n";
                                } else {
                                    echo "<b>There are no projects marked \"Ready for Quote\". Please check the project settings in @task and refresh.</b>";
                                }

                                /* echo "<pre>";
                                  var_dump($quotableProjects);
                                  echo "</pre>"; */

                                unset($api);
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
                        </td>
                        <td>
                            <input type="button" name="Load" value="Reload XML" onclick="window.location = 'reload.php'" />
                        </td>
                    </tr>
                </table>
            </fieldset>
            <fieldset><legend>Project Information</legend>
                <table border="1" bgcolor="#FFFFFF" width="100%">

                    <tr>
                        <td align="right" height="16px">
                            Pricing scheme applied: 
                        </td>
                        <td id="schemecell">
<!--                            <img src="../images/small-loading.gif" alt="loading..." />-->
                            <!-- <select name="priceScheme" onchange="getPMPercentFromDB(this.value)">
                            <option value="none" selected="selected">None</option>
    
                            </select> -->
                        </td>
                    </tr>

                    <tr>
                        <td width="30%" align="right" valign="top">Requested Delivery Date:</td>
                        <td width="70%">
                            
                            <input name="estDeliveryDate" type="text" /> 
                        </td>
                    </tr>
                    <tr>
                        <td align="right" valign="top">Rush Fees </td>
                        <td>
                            <div id="loading"></div>
                            <label><input type="radio"  id="norushfee_radio" name="rushFees" value="0" checked="checked" />None &nbsp;</label>
                            <label ><input type="radio" id='2' name="rushFees" value="0.25" />25% &nbsp;</label>
                            <label><input type="radio" name="rushFees" value="0.50" />50%  &nbsp;</label>
                            <label><input type="radio" name="rushFees" value="1.00" />100% </label>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" valign="top">Discount</td>
                        <td><input type="text" name="discountAmount" id="discountAmount" value="0" size="5"/>
                            <input type="radio" name="discountType" value="percent" id="discountPercent" checked="checked"/>Percent &nbsp; 

                            <input type="radio" name="discountType" value="fixed" id="discountFixed"/>Fixed Amount
                        </td>
                    </tr>
<!--                    <tr>
                        <td width="30%" align="right" valign="top">Use U.S.-based linguists?</td>
                        <td width="70%">
                            <input type="radio" value="no" name="us-linguists"/>No &nbsp;
                            <input type="radio" value="yes" name="us-linguists" />Yes &nbsp;
                            <input type="radio" value="attask" name="us-linguists" checked="checked" />Use value from @task
                        </td>
                    </tr>-->
                    <tr>
                        <td valign="top" align="right" style="padding-top:4px">Billing Cycle</td>
                        <td>
                            <select name="cycle" onchange="changeCycle(this.value);">
                                <option value="On Delivery">On Delivery</option>
                                <option value="Project Start">Project Start</option>
                                <option value="50-50">50-50</option>
                                <option value="Progress">Progress</option>
                            </select><br />
                            <input type="text" name="cycleOther" id="cycleOther" style="margin-top:5px" disabled="disabled" size="45"/>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" valign="top">Charge Client Proofreading?</td>
                        <td>
                            <!-- Question used to be "pass on proofreading", this is why yes/no is swapped -->
                            <input type="radio" id="radio1" name="proofReading" value="yes" />Yes &nbsp;
                            <input type="radio" name="proofReading" value="no"/>No
                        </td>
                    </tr>
                </table>
            </fieldset>

            <!--
            <fieldset><legend>Linguistic</legend>
            <table border="1" bgcolor="#FFFFFF" width="100%">
                    <tr>
                            <td width="30%" align="right">Trans/CE Buy Units: </td>
                            <td>
                                    <select name="linguisticCostType">
                                            <option value="Words">By Word</option>
                                            <option value="Hours">Hourly</option>
                                    </select>
                            </td>
                    </tr>
                    <tr>
                            <td width="30%" align="right">Trans/CE Sell Units: </td>
                            <td>
                                    <select name="linguisticSellType">
                                            <option value="Words">By Word</option>
                                            <option value="Hours">Hourly</option>
                                    </select>
                            </td>
                    </tr>
            </table>
            </fieldset>
            -->
            <!--
            <fieldset><legend>Formatting</legend>
            <table border="1" bgcolor="#FFFFFF" width="100%">
                    
                    <tr>
                            <td width="30%"align="right">Buy Units:</td>
                            <td width="70%">
                                    <select id="DTPCostunits" name="DTPCostunits" onChange="toggleDTP(this.options[this.selectedIndex].value);">
                                            <option value="pages">Pages</option>			
                                            <option value="hours" selected="selected">Hours</option>
                                    </select>
                                    <span id="pages">Number of Pages: <input id="pageNumber" name="pageNumber" type="text" size="10" maxlength="10" /> Cost Per Page: $<input type="text" name="pageCost" id="pageCost" size="10" maxlength="10" /></span>
                            </td>
                    </tr>
                    <tr>
                            <td align="right">Sell Units:</td>
                            <td>
                                    <select id="DTPSellunits" name="DTPSellunits" onChange="toggleDTP(this.options[this.selectedIndex].value);">
                                            <option value="pages">Pages</option>
                                            <option value="hours" selected="selected">Hours</option>
                                    </select>
                            </td>
                    </tr>
            </table>
            </fieldset>
            -->
            <fieldset><legend>Project Management</legend>
                <table border="1" bgcolor="#FFFFFF" width="100%">
                    <tr>
                        <td width="30%" align="right">Percent of project price to charge client:</td>
                        <td><input type="text" name="pmPercent" id="pmPercent" value="10" size="4"/>% <span id="pmCustom" class="instruction"></span></td>
                    </tr>
                    <tr>
                        <td width="30%" align="right">Project Management minimums<br />apply to all languages</td>
                        <td><input type="radio" name="pmMinPerLanguage" value="yes" />Yes &nbsp; <input type="radio" name="pmMinPerLanguage" value="no" checked="checked" />No
                    </tr>
                </table>
            </fieldset>
            <fieldset><legend>Quality Assurance Budget</legend>
                <table border="1" bgcolor="#FFFFFF" width="100%">
                    <tr>
                        <td width="30%" align="right">Are the number of pages the<br />same for all languages?</td>
                        <td> 
                            <input type="radio" name="qaRequired" value="yes" onchange="changeQA(this.value);" />Yes &nbsp;
                            <input type="radio" id="noQARequired_id" name="qaRequired" value="no"  checked="checked" onchange="changeQA(this.value);"/>No / Project does not require QA&nbsp;
                        </td>
                    </tr>
                    <tr id="pagesRow" style="display:none">
                        <td width="30%" align="right"># pages: </td>
                        <td><input type="text" name="numpages" id="numpages" style="margin-top:5px" disabled="disabled" size="45" value="1"/></td>
                    </tr>
                    <tr id="rateRow" style="display:none">
                        <td width="30%" align="right">Pages per hour: </td>
                        <td><input type="text" name="pagesPerHour" id="pagesPerHour" style="margin-top:5px" disabled="disabled" size="45" value="1" /></td>
                    </tr>
                </table>
            </fieldset>
            <fieldset>
                <table border="1" bgcolor="#FFFFFF" width="50%" align="center">
                    <tr>
                        <td align="center">
                            <?PHP
                            if (count($quotableProjects) > 0) {
                                echo "<input type=\"submit\" name=\"submit\" value=\"Proceed\" />\n";
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <?php
            echo "<input type=\"hidden\" name=\"callingPage\" value=\"./index.php\" />\n";
            ?>
            <input type="hidden" name="priceScheme" id="priceScheme" />
            <input type="hidden" name="customPM" id='customPM' value='false' />
        </form>
        <div style="text-align: center;">
            <a href="../index.php"><button >Return to Main Page</button></a>
        </div>
    </body>
</html>
