<?PHP
include_once("database.php");
//restart the session
header('Cache-Control: no cache'); //no cache
session_cache_limiter('private_no_expire');

session_start();
require_once('uuid.php');

//check to make sure we're logged in
if (!isset($_SESSION['userID'])) {
    die('You are not properly logged in and are unable to use this application');
} elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID)) {
    die('Fatal Error! Application authentication is not correct');
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>LLS - Ballparker Page 3</title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
        <script src="libs/estimate_3.js"></script>
        <script src="assets/jquery.validate.js"></script>
        <script src="libs/chosen.jquery.js"></script>
        <link rel="stylesheet" type="text/css" href="styles/chosen.css" />
        <link rel="stylesheet" type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" />
        <link rel="stylesheet" type="text/css" href="styles/main.css"/>
        <link rel="stylesheet" type="text/css" href="assets/style.css" />
    </head>

    <body>
        <form method='post' id="ballParkerForm3" action="prepareProject.php" >
            <fieldset class="bpfieldset">
                <legend class="ballParker" >Ballparker Quote Page 3</legend>
                <div class="sectionHeader" style="padding-bottom: 10px;">
                    Internal Efforts
                </div>
                <div class="bpControlBoddy">
                    <table>
                        <tr>
                            <td>
                                <label>File Format:</label>
                            </td>
                            <td>
                                <select data-placeholder="Choose format..." id="fileFormatSelect" class="chosen-select-format" id="fileformat_chosen" style="width:200px;" required=>
                                    <option value=""></option>
                                    <?PHP
                                    $outSpec = "<option value='{name}' >{name}</option>\n";
                                    $fileFormats = getFileFormats();
                                    foreach ($fileFormats as $name) {
                                        $output = $outSpec;
                                        $output = str_replace("{name}", $name, $output);
                                        echo $output;
                                    }
                                    ?>
                                </select> 
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr class="document_formatting_row">
                            <td>
                                <label>Document Formatting</label><br/>
                            </td>
                        </tr>
                        <tr class="document_formatting_row">
                            <td align="right">
                                <label>Number of pages</label>
                            </td>
                            <td>
                                <input type="number" class="document_formatting smallNumberEntry" id="formattingPageCount" name="numpages" value="<?php echo isset($_POST['pagesPerHour']) ? $_POST['pagesPerHour'] : '0' ?>"/>
                            </td>
                        </tr>
                        <tr class="document_formatting_row">
                            <td align="right">
                                <label>QA pages/hour</label>
                            </td>
                            <td>
                                <input type="number" class="document_formatting smallNumberEntry" id="qaPagesPerHour" name="pagesPerHour" value="<?php echo isset($_POST['pagesPerHour']) ? $_POST['pagesPerHour'] : '12' ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label><input type="checkbox" id="tmWorkNeeded" checked/>File Prep/TM Work Needed?</label><br/>
                            </td>
                        </tr>
                        <tr class="tmFilePrepHours">
                            <td align="right">
                                <label>Number of File Prep/TM hours</label>
                            </td>
                            <td>
                                <input type="number" name="tmWorkHours" class="smallNumberEntry" id="tmFilePrepHours" required value="<?php echo isset($_POST['tmWorkHours']) ? $_POST['tmWorkHours'] : '0' ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label><input type="checkbox" id="otherEngineering"/>Other Engineering Needed?</label><br/>
                            </td>
                        </tr>
                        <tr class="engineeringHours" hidden>
                            <td align="right">
                                <label>Number of Other Eng. hours</label>
                            </td>
                            <td>
                                <input type="number" name="uiEngineeringHours" class="smallNumberEntry" id="engineeringHours" value="<?php echo isset($_POST['uiEngineeringHours']) ? $_POST['uiEngineeringHours'] : '0' ?>"/>
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tbody>
                            <tr>
                                <td>
                                    <label for="projectManagement">Project Management</label>
                                    <input type="text" id="projectManagement" readonly class="pbPMNormalState" tabindex="-1"/>
                                </td>
                                <td align="right">
                                    <div id="slider-vertical-pm" style="height:35px;width: 4px;"></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="sectionDiv">
                        <div class="sectionHeader">
                            Additional Tasks
                        </div>

                        <div style="line-height: 10px; padding-left: 75px; padding-top: 10px;"> 
                            <?PHP
                            $outSpec = "<input type='checkbox' name='additionalTasks[]' value='{name}' />{name}<br/>\n";
                            $otherTasks = getOtherTasks();
                            foreach ($otherTasks as $name) {
                                $selected = "";
//                                if ($name == 'Margin Pricing') {
//                                    $selected = " selected";
//                                }
                                $output = $outSpec;
                                $output = str_replace("{name}", $name, $output);
                                $output = str_replace("{selected}", $selected, $output);
                                echo $output;
                            }
                            ?>
                        </div>
                    </div>
                    <?PHP
                    $getArgs = "";
                    if (isset($_POST)) {
                        foreach ($_POST as $param_name => $param_val) {
                            if (is_array($param_val)) {
                                $getArgs .= "&" . $param_name . "=";
                                $arrayValues = "";
                                foreach ($param_val as $selectedOption) {
                                    if (!empty($arrayValues)) {
                                        $arrayValues .= ",";
                                    }
                                    $arrayValues .= urlencode($selectedOption);

                                    echo "<input type='hidden' name='" . $param_name . "[]' value='" . $selectedOption . "'>\n";
                                }
                                $getArgs .= $arrayValues;
                            } else {
                                $getArgs .= "&" . $param_name . "=" . urlencode($param_val);
                                echo "<input type='hidden' name='" . $param_name . "' value='" . $param_val . "'>\n";
                            }
                        }
                        // now get rid of the leading & in the array...
                        $getArgs = "?" . substr($getArgs, 1);
                    }
                    ?>
                </div>
                <table align="center">
                    <tbody>
                        <tr>
                            <td>
                                <button onclick="window.location.href = 'estimate_2.php<?PHP echo $getArgs ?>';
                                        return false;">< Back</button>
                            </td>
                            <td>
                                <input type="submit" name="submit" value="Continue >"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <input type="hidden" name="qtroot" value="<?PHP echo QTROOT ?>" />
                <input type="hidden" id="pmpercent" name="pmPercent"  value="" />
                <input type="hidden" id="formattingHours" name="formattingHours"  value=""/>
                <input type="hidden" id="qaHours" name="qaRequired"  value="" />
            </fieldset>
        </form>
    </body>
</html>
