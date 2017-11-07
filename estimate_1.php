<?PHP
include_once("database.php");
//start the session
header('Cache-Control: no cache'); //no cache
session_cache_limiter('private_no_expire');

session_start();
require_once('uuid.php');

//check to make sure we're logged ins
if (!isset($_SESSION['userID'])) {
    die('You are not properly logged in and are unable to use this application');
} elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID)) {
    die('Fatal Error! Application authentication is not correct');
}

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
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>LLS - Ballparker Page 1</title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
        <link rel="stylesheet" type="text/css" href="assets/style.css" />
        <script src="libs/bputils.js"></script>
        <script src="libs/estimate_1.js"></script>
        <script src="libs/chosen.jquery.js"></script>
        <script src="assets/jquery.validate.js"></script>
        <link rel="stylesheet" type="text/css" href="styles/chosen.css" />
        <link rel="stylesheet" type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" />
        <link rel="stylesheet" type="text/css" href="styles/main.css"/>
    </head>

    <body>
        <fieldset class="bpfieldset">
            <legend class="ballParker">Ballparker Quote Page 1</legend>
            <div class="sectionHeader" style="padding-bottom: 10px;">
                Client Info
            </div>
            <table align="center">
                <tr class="bordered" >
                    <td>
                        <label><input type="radio" id='companyClient' name="companytype" value="client" checked="checked" />Client &nbsp;</label>
                        <label><input type="radio" id='companyProspect' name="companytype" value="prospect" />Prospect</label>
                    </td>
                </tr>
            </table>
            <form method='post' id="ballParkerForm1" action="estimate_2.php" >
                <div class="bpControlBoddy">
                    <label class="companyClient">Company: </label><input type="text" id="companyClientInput" style="width: 300px;" placeholder="Select Company name ..." required class='company_auto companyClient' />
                    <label class="companyProspect" hidden>Company: </label><input type="text" id="companyProspectInput" style="width: 300px;" placeholder="Enter Company name" hidden class='companyProspect'/><br/>
                    <p/>
                    <label>Client Contact Name:</label>&nbsp;
                    <input type="text" name="clientFirstName" value="" style="width: 70pt" placeholder="First name" />&nbsp;
                    <input type="text" name="clientLastName" value="" style="width: 140pt"  placeholder="Last name" /><br/>
                    <p/>
                    <label>Sales Rep:&nbsp;</label>
                    <select data-placeholder="Choose Sales Rep..." class="chosen-select" id="salesrep_chosen" name="sales_rep" style="width:250px;" required >
                        <option value=""/>
                        <?PHP
                        $outSpec = "<option value='{name}'>{name}</option>\n";
                        $reps = getSalesReps();
                        foreach ($reps as $name) {
                            $output = $outSpec;
                            $output = str_replace("{name}", $name, $output);
                            echo $output;
                        }
                        ?>
                    </select> 
                    <div class="sectionDiv">
                        <div class="sectionHeader">
                            Quote Info
                        </div>
                        <p>
                            <label>Project Name:&nbsp;</label><input type="text" name="projectName" value="" placeholder="Project Name" required/>
                        </p>
                        <p>     
                            <label>Pricing Scheme Applied:&nbsp;</label>
                            <select name="priceScheme" id="pricingSchemeSelect" style="width:200px;" >
                                <?PHP
                                $outSpec = "<option value='{name}'{selected}>{name}</option>\n";
                                $pSchemes = getPricingSchemes();
                                foreach ($pSchemes as $name) {
                                    $selected = "";
                                    if ($name == 'Margin Pricing') {
                                        $selected = " selected";
                                    }
                                    $output = $outSpec;
                                    $output = str_replace("{name}", $name, $output);
                                    $output = str_replace("{selected}", $selected, $output);

                                    echo $output;
                                }
                                ?>
                            </select>            
                        </p>
                        <div id="errorMsg" ></div>
                        <table>
                            <tr>
                                <td valign="center">Rush Fees:</td>
                                <td>
                                    <label><input type="radio" id="norushfee_radio" name="rushFees" value="0" checked="checked" />None &nbsp;</label>
                                    <label><input type="radio" id='rush_25' name="rushFees" value="0.25" />25% &nbsp;</label>
                                    <label><input type="radio" id='rush_50' name="rushFees" value="0.50" />50% &nbsp;</label>
                                    <label><input type="radio" id='rush_100' name="rushFees" value="1.00" />100%</label>
                                </td>
                            </tr>
                            <tr>
                                <td valign="center">Discount:</td>
                                <td><input type="number" name="discountAmount" id="discountAmount" value="0" class="smallNumberEntry" />
                                    <input type="radio" name="discountType" value="percent" id="discountPercent" checked="checked"/>Percent &nbsp; 
                                    <input type="radio" name="discountType" value="fixed" id="discountFixed"/>Fixed Amount
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <table align='center'>
                    <tr>
                        <td>
                            <input type="button" onclick="window.location.href = $('#qtroot').val() + '/index.php';
                                    return false;" value="< Return"/>
                        </td>
                        <td>
                            <input type="submit" value="Continue >" />
                        </td>
<!--                        <td>
                            <input type="button" id="resetPage1" value="Reset"/>
                        </td>-->
                    </tr>
                </table>
                <input type="hidden" id="qtroot" name="qtroot" value="<?PHP echo QTROOT ?>" />
                <input type="hidden" name="callingPage" value="../estimate_1.php" />
                <input type="hidden" name="cycle" value="On Delievery" />
                <input type="hidden" name="companyName" id="companyName" />
                <input type="hidden" name="companyNameSwitch"/>
                <input type="hidden" name="companyType" id="companyType" />
                <input type="hidden" id="clientPMPercent" name="clientPMPercent" value="10">
                <input type="hidden" id="clientPricingDiscount" name="clientPricingDiscount"/>
            </form>
        </fieldset>
    </body>
</html>
