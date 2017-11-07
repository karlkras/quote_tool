<?PHP
include_once("database.php");
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
        <title>LLS - Ballparker Page 2</title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
        <script src="libs/bputils.js"></script>
        <script src="libs/estimate_2.js"></script>
        <script src="assets/jquery.validate.js"></script>
        <script src="libs/chosen.jquery.js"></script>
        <link rel="stylesheet" type="text/css" href="styles/chosen.css" />
        <link rel="stylesheet" type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" />
        <link rel="stylesheet" type="text/css" href="styles/main.css"/>
        <link rel="stylesheet" type="text/css" href="assets/style.css" />
    </head>

    <body>
        <form method='post' id="ballParkerForm2" action="estimate_3.php" >
            <fieldset class="bpfieldset">
                <legend class="ballParker" >Ballparker Quote Page 2</legend>
                <div class="sectionHeader" style="padding-bottom: 10px;">
                    Linguistic Specs
                </div>
                <div class="bpControlBoddy">
                    <table>
                        <tr>
                            <td style="width: 200px;">
                                <label>Source Language:</label>
                                <br/>
                                <select class="chosen-select-sourcelang" name="source_lang" value="" tabindex="2">
                                    <?PHP
                                    $outSpec = "<option value='{name}'{selected}>{name}</option>\n";
                                    $langs = getSourceLanguages();
                                    foreach ($langs as $name) {
                                        $selected = "";
                                        if ($name == 'English (US)') {
                                            $selected = " selected";
                                        }
                                        $output = $outSpec;
                                        $output = str_replace("{name}", $name, $output);
                                        $output = str_replace("{selected}", $selected, $output);

                                        echo $output;
                                    }
                                    ?>
                                </select>   
                            </td>
                            <td>
                                <label>Target Languages:</label>
                                <br/>
                                <select data-placeholder="Choose Target Language(s)..." class="chosen-select-targlangs" id="targlang_chosen" multiple tabindex="3" name="targ_langs[]" required>
                                    <option value=""></option>
                                    <?PHP
                                    $outSpec = "<option value='{name}'>{name}</option>\n";
                                    $langs = getTargetLanguages();
                                    foreach ($langs as $name) {
                                        $output = $outSpec;
                                        $output = str_replace("{name}", $name, $output);
                                        echo $output;
                                    }
                                    ?>
                                </select>         
                            </td>
                        </tr>
                    </table>
                    <div class="sectionDiv">
                        <div class="sectionHeader">
                            Estimated Word Counts
                        </div>
                        <p>
                        <table>
                            <tbody>
                                <tr>
                                    <td>
                                        <input id="words_total" type="number" placeholder="Total Words" name="words_total" required/>
                                    </td>
                                    <td>
                                        <label for="amount">Assumed Leveraging:</label>
                                        <input type="text" id="amount" name="assumedLeveraging" value="" readonly style="border:0; color:#f6931f; font-weight:bold;width:40px" tabindex="-1"/>
                                    </td>
                                    <td>
                                        <div id="slider-vertical" style="height:35px;width: 4px;"></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div style="line-height: 25px;"> 
                            <input id="words_type_new" name="words_type_new" type="number" placeholder="New Words" /><br/>
                            <input id="words_type_match" name="words_type_match" type="number" placeholder="100% Matches/Reps" /><br/>
                            <input id="words_type_fuzzy" name="words_type_fuzzy" type="number" placeholder="Fuzzy Matches" />
                        </div>
                    </div>

                    <div class="nextSectionDiv" style="border-top: 0px;">
                        <div class="sectionHeader">
                            Proofreading
                        </div>
                        <table>
                            <tr>
                                <td colspan="2" style="padding-left: 40px; padding-bottom: 10px;">
                                    <label><input type="radio" name="proofReading" value="yes" checked="checked" />Proofread and charge the client</label><br/>
                                    <label><input type="radio" name="proofReading" value="no" />Proofread but don’t charge the client</label><br/>
                                    <label><input type="radio" id="no_proofread" name="proofReading" value="none" />Don’t proofread</label><br/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Proofreading Rate (words/hour)</label>
                                </td>
                                <td>
                                    <input type="number" id="proofreading_rate" name="proofreading_rate" value="2000" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label># of Hours (Calculated)</label>
                                </td>
                                <td>
                                    <input type="number" id="proofreading_calc" name="proofreadingHours" class="smallNumberEntry" value=""  readonly/>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <p/>
                </div>


                <?PHP
                $getArgs = "?";
                if (isset($_POST)) {
                    $getArgs .= http_build_query($_POST);
                    foreach ($_POST as $param_name => $param_val) {
                        if (is_array($param_val)) {
                            foreach ($param_val as $selectedOption) {
                                echo "<input type='hidden' name='" . $param_name . "[]' value='" . $selectedOption . "'>\n";
                            }
                        } else {
                            echo "<input type='hidden' name='" . $param_name . "' value='" . $param_val . "'>\n";
                        }
                    }
                }
                ?>
                <table align="center">
                    <tbody>
                        <tr>
                            <td>
                                <button id="backButton">< Back</button>
                            </td>
                            <td>
                                <input type="submit" value="Continue >" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
            <input type="hidden" name="qtroot" value="<?PHP echo QTROOT ?>" />
        </form>
    </body>
</html>
